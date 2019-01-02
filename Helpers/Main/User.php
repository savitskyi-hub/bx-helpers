<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Main;

use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Bitrix\Main\Data\Cache;
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;
use SavitskyiHub\BxHelpers\Helpers\IO\Dir;

/**
 * Class User
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для хранения данных пользователя и необходимых методов для их обработки, которые будут нужны в реализации
 * или поддержки проекта (чтобы избавиться от дублирования кода и другой рутинной работы)
 */
final class User
{
	use ClassTrait;
	
	/**
	 * Singleton Instance
	 */
	private static $instance = null;
	
	/**
	 * Приватные параметры кеша
	 */
	private static $cacheTime = 600;
	private static $cacheDir = '_helpers_user';
	
	/**
	 * Идентификатор пользователя
	 * @var int
	 */
	protected $ID = 0;
	
	/**
	 * Группы в которые входит пользователь
	 * @var array
	 */
	protected $GROUP = [];
	
	/**
	 * Свойства пользователя
	 * @var array;
	 */
	protected $PROP = [];
	
	/**
	 * Авторизован ли пользователь
	 * @var bool
	 */
	protected $isAuth = false;
	
	/**
	 * Заблокирован ли пользователь
	 * @var bool
	 */
	protected $isBan = false;
	
	/**
	 * Удален ли пользователь
	 * @var bool
	 */
	protected $isDelete = false;
	
	/**
	 * Список системных груп
	 * @var array
	 */
	protected $arSystemGroup = [];
	
	/**
	 * Объект создается внутри самого класса, только если у класса нет экземпляра
	 *
	 * @return null|Instance
	 */
	static function getInstance() {
		if (null == self::$instance) {
			self::$instance = new User();
		}
		
		return self::$instance;
	}
	
	/**
	 * User constructor
	 */
	private function __construct() {
		try {
			Loader::includeModule("main");
			
			global $USER;
			
			$сache = Cache::createInstance();
			
			$cacheDirSysG = Dir::getCacheDirectoryPrefixName().self::$cacheDir.'/cacheSystemGroup';
			$cacheDirUser = Dir::getCacheDirectoryPrefixName().self::$cacheDir;
			$cacheIdSysG = SITE_ID.'_cacheSystemGroup';
			$cacheIdUser = SITE_ID.'_user_';
			
			if ($this->isClearCache()) {
				$сache->clean($cacheIdSysG, $cacheDirSysG);
			}
			
			/**
			 * Кэшируем выборку системных групп
			 */
			if ($сache->initCache(self::$cacheTime, $cacheIdSysG, $cacheDirSysG)) {
				$this->arSystemGroup = $сache->getVars()["arSystemGroup"];
			} elseif ($сache->startDataCache()) {
				$this->arSystemGroup = $this->getSystemGroup();
				
				$сache->endDataCache([
					"arSystemGroup" => $this->arSystemGroup
				]);
			}
			
			if ($USER->IsAuthorized()) {
				$this->isAuth = 1;
				$this->ID = $USER->GetID();
				
				$cacheDirUser = $cacheDirUser.'/'.$this->ID;
				$cacheIdUser = $cacheIdUser.$this->ID;
				
				/**
				 * Кэшируем выборку данных
				 */
				if ($сache->initCache(self::$cacheTime, $cacheIdUser, $cacheDirUser)) {
					$arCacheVars = $сache->getVars();

					$this->GROUP = $arCacheVars["GROUP"];
					$this->PROP = $arCacheVars["PROP"];
				} elseif ($сache->startDataCache()) {
					$this->GROUP = \CUser::GetUserGroup($this->ID);
					$this->PROP = $this->getProps();

					$сache->endDataCache([
						"GROUP" => $this->GROUP,
						"PROP" => $this->PROP
					]);
				}
				
				// Создать автоматом 3 групы !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				// Registred, delete, ban
				
				/**
				 * Если пользователь заблокирован делаем отметку
				 */
				if ($this->isInGroup($this->GROUP, "BAN")) {
					$this->isBan = true;
				}
				
				/**
				 * Если пользователь был удален делаем отметку
				 */
				if ($this->isInGroup($this->GROUP, "DELETED")) {
					$this->isDelete = true;
				}
				
				/**
				 * Отмечаем что пользователь сейчас активен
				 */
				$USER->SetLastActivityDate($this->ID);
			}
		} catch (SystemException $e) {
			Debug::writeToFile($e->getMessage());
		}
	}
	
	/**
	 * Возращает список системных груп
	 *
	 * @return array
	 */
	private function getSystemGroup(): array {
		$by = "c_sort";
		$order = "asc";
		$rsGroups = \CGroup::GetList($by, $order, ["ACTIVE" => "Y"]);
		
		if ($rsGroups && $rsGroups->result->num_rows) {
			$arReturn = [];
			
			while ($arGroup = $rsGroups->fetch()) {
				if (!empty($arGroup["STRING_ID"])) {
					$arReturn[$arGroup["STRING_ID"]] = $arGroup;
				}
			}
		}
		
		return $arReturn ?? [];
	}
	
	/**
	 * Возвращает свойства пользователя
	 *
	 * @return array
	 */
	private function getProps(): array {
		try {
			if (!$this->ID) {
				throw new SystemException("Не удалось получить идентификатор пользователя");
			}
			
			$rsUserProps = UserTable::GetList([
				"filter" => ["ID" => $this->ID],
				"limit" => 1,
				"select" => ["*", "UF_*"]
			]);
			
			if ($rsUserProps && $rsUserProps->getSelectedRowsCount()) {
				$arProps = $rsUserProps->fetch();
				
				unset($arProps["PASSWORD"]);
				unset($arProps["BX_USER_ID"]);
			}
			
		} catch (SystemException $e) {
			Debug::writeToFile($e->getMessage());
		}
		
		return $arProps ?? [];
	}
	
	/**
	 *
	 *
	 * @return bool
	 */
	private function isClearCache(): bool {
		$nameSession = "SAVITSKYIHUB_BXHELPERS_HELPERS_MAIN_USER_CLEAN_CACHE";
		
		if (isset($_SESSION[$nameSession]) && "Y" == $_SESSION[$nameSession]) {
		
		}
		
		return false;
	}
	
	/**
	 * Возвращает ID пользователя производя поиск по логину
	 *
	 * @param string $login
	 * @return int
	 */
	public static function getIdByLogin(string $login): int {
		$rsIdByLogin = UserTable::GetList([
			"select" => ["ID"],
			"filter" => ["LOGIN" => $login],
			"limit" => 1
		]);

		if ($rsIdByLogin && $rsIdByLogin->getSelectedRowsCount()) {
			return $rsIdByLogin->Fetch()["ID"];
		}
		
		return 0;
	}
	
//	/**
//	 * Метод проверяет находится ли пользователь в определенной системной групе пользователей.
//	 * @param array $arGroup
//	 * @parap string
//	 * @return boolean
//	 */
	public static function isInGroup(array $arGroup, string $keyGroupName) {
//		if (in_array(self::getGroupId($keyGroupName), $arGroup)) {
//			return true;
//		} else {
//			return false;
//		}
	}

	//    /**
	//     * Метод возращает идентификатор системной групы.
	//     * @param string $keyGroupName - название символьного кода системной групы;
	//     * @param bool $convert2Array - бывает необходимо возвращать результат в виде массива;
	//     * @return array|integer
	//     * @throws SystemException
	//     */
	//    static function getGroupId($keyGroupName, $convert2Array = false) {
	//
	//        try {
	//
	//            if (!array_key_exists($keyGroupName, self::$systemGroup)) {
	//                throw new SystemException('Undefined key &ldquo;'.$keyGroupName.'&rdquo; group name');
	//            }
	//
	//            $groupId = (int) self::$systemGroup[$keyGroupName]["ID"];
	//
	//            if ($convert2Array && !is_array($groupId)) {
	//                $groupId = [$groupId];
	//            }
	//
	//            return $groupId;
	//
	//        } catch (SystemException $e) {
	//
	//            if (self::get('exceptionGlobal')) {
	//                Variable::set('error', $e->getMessage().self::getSuffixError());
	//            } else {
	//                throw $e;
	//            }
	//
	//        }
	//
	//    }
	//
	//    /**
	//     * Метод возвращает путь к аватару пользователя.
	//     * @param $idFile
	//     * @return string
	//     */
	//    static function getAvatar($html = false) {
	//
	//        $idFile = self::$PROP["PERSONAL_PHOTO"];
	//        $path = self::$PROP["PERSONAL_PHOTO_PATH"];
	//
	//        if (!$path) {
	//
	//            if ($idFile) {
	//                $path = \CFile::GetPath($idFile);
	//            } else {
	//                $path = SITE_TEMPLATE_PATH.'/img/no-avatar.png';
	//            }
	//
	//            self::push('PROP', $path, 'PERSONAL_PHOTO_PATH');
	//
	//        }
	//
	//        if ($html) {
	//            return '<img src="'.$path.'" alt="User Avatar">';
	//        } else {
	//            return $path;
	//        }
	//
	//    }
	//
	//    /**
	//     * Метод для удобного получения полного имени пользователя.
	//     * @return string
	//     */
	//    static function getFullName($surname = false) {
	//
	//        $name = self::$PROP["NAME"];
	//        $lastName = self::$PROP["LAST_NAME"];
	//
	//        if ($surname) {
	//            $name .= ' '.self::$PROP["SECOND_NAME"];
	//        }
	//
	//        return $lastName.' '.$name;
	//
	//    }
	//
	//    /**
	//     * Метод для удобного получения аббревиатурного имени пользователя.
	//     * @return string
	//     */
	//    static function getNameAbbr() {
	//
	//        $name = self::$PROP["NAME"];
	//        $lastName = self::$PROP["LAST_NAME"];
	//
	//        if ($name && $lastName) {
	//            return $name[0].$lastName[0];
	//        } else {
	//            return '--';
	//        }
	//
	//    }
	//
	//    /**
	//     * Метод для получения возраста пользователя.
	//     * @return int
	//     */
	//    static function getAge() {
	//
	//        $birthday = self::$PROP["PERSONAL_BIRTHDAY"];
	//
	//        if ($birthday && is_object($birthday)) {
	//
	//            $birthdayTimestamp = strtotime($birthday->toString());
	//            $age = date('Y') - date('Y', $birthdayTimestamp);
	//
	//            if (date('md', $birthdayTimestamp) > date('md')) {
	//                $age--;
	//            }
	//
	//            return (int) $age;
	//
	//        } else {
	//            return 0;
	//        }
	//
	//    }
}