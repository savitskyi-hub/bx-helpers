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

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;
use SavitskyiHub\BxHelpers\Helpers\Content\Image;
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
	private static $instance = [];
	
	/**
	 * Название кэша для отслеживания очистки данных пользователя
	 * @var string
	 */
	private static $nameSessionCleanCache = 'HELPERS_MAIN_USER_CLEAN_CACHE';
	
	/**
	 * Приватные параметры кеша
	 */
	private $cacheTime = 5400;
	private $cacheDir = '_helpers_user';
	
	/**
	 * Идентификатор пользователя
	 * @var int
	 */
	private $ID = 0;
	
	/**
	 * Группы в которые входит пользователь
	 * @var array
	 */
	private $GROUP = [];
	
	/**
	 * Свойства пользователя
	 * @var array;
	 */
	private $PROP = [];
	
	/**
	 * Авторизован ли пользователь
	 * @var bool
	 */
	private $isAuth = false;
	
	/**
	 * Заблокирован ли пользователь
	 * @var bool
	 */
	private $isBan = false;
	
	/**
	 * Удален ли пользователь
	 * @var bool
	 */
	private $isDelete = false;
	
	/**
	 * Администратор ли пользователь
	 * @var bool
	 */
	private $isAdmin = false;
	
	/**
	 * Список системных груп
	 * @var array
	 */
	private $arSystemGroup = [];
	
	/**
	 * User constructor
	 */
	private function __construct($ID) {
		try {
			Loader::includeModule("main");
			
			global $USER;
			
			$сache = Cache::createInstance();
			$cacheDirSysG = Dir::getCacheDirectoryPrefixName().$this->cacheDir.'/cacheSystemGroup';
			$cacheDirUser = Dir::getCacheDirectoryPrefixName().$this->cacheDir;
			$cacheIdSysG = SITE_ID.'_cacheSystemGroup';
			$cacheIdUser = SITE_ID.'_user_';
			
			/**
			 * Если были изменены данные скидываем кэш
			 */
			if ($keysUserCache = $this->isClearCache()) {
				$сache->clean($cacheIdSysG, $cacheDirSysG);
			}
			
			/**
			 * Кэшируем выборку системных групп
			 */
			if ($сache->initCache($this->cacheTime, $cacheIdSysG, $cacheDirSysG)) {
				$this->arSystemGroup = $сache->getVars()["arSystemGroup"];
			} elseif ($сache->startDataCache()) {
				$this->arSystemGroup = $this->getSystemGroup();
				
				$сache->endDataCache([
					"arSystemGroup" => $this->arSystemGroup
				]);
			}
			
			if ($USER->IsAuthorized() || $ID) {
				$this->ID = $ID? $ID : $USER->GetID();
				$this->isAuth = 0;
				
				if ($USER->IsAuthorized() && $ID == $USER->GetID()) {
					$this->isAuth = 1;
				} elseif (!$ID) {
					$this->isAuth = 1;
				}
				
				/**
				 * Если были изменены данные скидываем кэш
				 */
				if (!empty($keysUserCache)) {
					foreach ($keysUserCache as $cleanCacheUserID) {
						$сache->clean($cacheIdUser.$cleanCacheUserID, $cacheDirUser.'/'.$cleanCacheUserID);
					}
				}
				
				$cacheDirUser = $cacheDirUser.'/'.$this->ID;
				$cacheIdUser = $cacheIdUser.$this->ID;
				
				/**
				 * Кэшируем выборку данных
				 */
				if ($сache->initCache($this->cacheTime, $cacheIdUser, $cacheDirUser)) {
					$arCacheVars = $сache->getVars();
					
					$this->GROUP = $arCacheVars["GROUP"];
					$this->PROP = $arCacheVars["PROP"];
				} elseif ($сache->startDataCache()) {
					$this->GROUP = \CUser::GetUserGroup($this->ID);
					$this->PROP = $this->getProps();
					$this->PROP["PERSONAL_PHOTO_PATH"] = $this->getAvatar();
					
					$сache->endDataCache([
						"GROUP" => $this->GROUP,
						"PROP" => $this->PROP
					]);
				} else {
					$isBad = true;
				}
				
				if (!isset($isBad)) {
					/**
					 * Если пользователь администратор
					 */
					if ($this->isInGroup("ADMINISTRATORS")) {
						$this->isAdmin = true;
					}
					
					/**
					 * Если пользователь заблокирован делаем отметку
					 */
					if ($this->isInGroup("BANED_USERS")) {
						$this->isBan = true;
					}
					
					/**
					 * Если пользователь был удален делаем отметку
					 */
					if ($this->isInGroup("DELETED_USERS")) {
						$this->isDelete = true;
					}
				}
			}
		} catch (SystemException $e) {
			$debug = new Debug();
			$debug->onBacktrace();
			$debug->writeData($e->getMessage(), false, true);
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
		$rsGroups = \CGroup::GetList($by, $order, ["=ACTIVE" => "Y"]);
		
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
				"filter" => ["=ID" => $this->ID],
				"limit" => 1,
				"select" => ["*", "UF_*"]
			]);
			
			if ($rsUserProps && $rsUserProps->getSelectedRowsCount()) {
				$arProps = $rsUserProps->fetch();
				
				unset($arProps["PASSWORD"]);
				unset($arProps["BX_USER_ID"]);
			}
			
		} catch (SystemException $e) {
			$debug = new Debug();
			$debug->onBacktrace();
			$debug->writeData($e->getMessage(), false, true);
		}
		
		return $arProps ?? [];
	}
	
	/**
	 * Отслеживаем сессию что создается после изменения данных пользователя
	 * - если есть, чистим кэш и удаляем сессию
	 *
	 * @return array
	 */
	private function isClearCache(): array {
		$return = [];
		
		if (isset($_SESSION[self::$nameSessionCleanCache])) {
			if (!is_array($_SESSION[self::$nameSessionCleanCache])) {
				return $return;
			}
			
			foreach ($_SESSION[self::$nameSessionCleanCache] as $k => $data) {
				$sessionTime = explode("|", $data);
				
				if ((time() - $sessionTime[0]) <= 60) {
					$return[] = (int) $sessionTime[1];
				}
				
				unset($_SESSION[self::$nameSessionCleanCache][$k]);
			}
		}
		
		return $return;
	}
	
	/**
	 * После изменения данных пользователя создадим сессию для отслеживания очистки кэша
	 *
	 * @param array $arFields - свойства пользователя;
	 */
	public function setClearCacheVar(array &$arFields) {
		if (!isset($_SESSION[self::$nameSessionCleanCache])) {
			$_SESSION[self::$nameSessionCleanCache] = [];
		}
		
		$_SESSION[self::$nameSessionCleanCache][] = time().'|'.$arFields["ID"];
	}
	
	/**
	 * Возвращает ID пользователя производя поиск по логину
	 *
	 * @param string $login
	 * @return int
	 */
	public function getIdByLogin(string $login): int {
		$rsIdByLogin = UserTable::GetList([
			"select" => ["ID"],
			"filter" => ["=LOGIN" => $login],
			"limit" => 1
		]);
		
		if ($rsIdByLogin && $rsIdByLogin->getSelectedRowsCount()) {
			return $rsIdByLogin->Fetch()["ID"];
		}
		
		return 0;
	}
	
	/**
	 * Получение инициалов пользователя
	 *
	 * @param bool $getSurname
	 * @return string
	 */
	public function getFullName(bool $getSurname = false): string {
		$strReturn = '';
		
		if (isset($this->PROP["LAST_NAME"]) && $this->PROP["LAST_NAME"]) {
			$strReturn .= $this->PROP["LAST_NAME"];
		}
		
		if (isset($this->PROP["NAME"]) && $this->PROP["NAME"]) {
			$strReturn .= ' '.$this->PROP["NAME"];
		}
		
		if ($getSurname && isset($this->PROP["SECOND_NAME"]) && $this->PROP["SECOND_NAME"]) {
			$strReturn .= ' '.$this->PROP["SECOND_NAME"];
		}
		
		return $strReturn;
	}
	
	/**
	 * Метод для удобного получения аббревиатурного имени пользователя
	 *
	 * @return string
	 */
	public function getAbbrName(): string {
		if (
			isset($this->PROP["LAST_NAME"], $this->PROP["NAME"])
			&& ($this->PROP["LAST_NAME"] && $this->PROP["NAME"])
		) {
			return substr($this->PROP["LAST_NAME"], 0, 1).substr($this->PROP["NAME"], 0, 1);
		}
		
		return '--';
	}
	
	/**
	 * Получения возраста пользователя
	 *
	 * @return int
	 */
	public function getAge(): int {
		$birthday = $this->PROP["PERSONAL_BIRTHDAY"];
		
		if ($birthday && is_object($birthday)) {
			$birthdayTimestamp = strtotime($birthday->toString());
			$age = date('Y') - date('Y', $birthdayTimestamp);
			
			if (date('md', $birthdayTimestamp) > date('md')) {
				$age--;
			}
			
			return (int) $age;
		}
		
		return 0;
	}
	
	/**
	 * Возвращает путь к аватару пользователя
	 *
	 * @param bool $getTag
	 * @return string
	 */
	public function getAvatar(bool $getTag = false): string {
		$fileID = $this->PROP["PERSONAL_PHOTO"];
		$path2file = $this->PROP["PERSONAL_PHOTO_PATH"];
		
		if (!$path2file) {
			if ($fileID) {
				$path2file = \CFile::GetPath($fileID);
			} else {
				$path2file = Image::getPathNoAvatar();
			}
		}
		
		if ($getTag) {
			return Image::show($path2file, "User Avatar");
		}
		
		return $path2file;
	}
	
	/**
	 * Возращает идентификатор системной группы
	 *
	 * @param string $groupCode - символьный код группы;
	 * @param bool $saveInArray - бывает необходимо возвращать результат в виде массива;
	 * @return array|int
	 */
	public function getGroupIdByCode(string $groupCode, bool $saveInArray = false) {
		try {
			if (!array_key_exists($groupCode, $this->arSystemGroup)) {
				throw new SystemException('Неопределенный код названия группы: '.$groupCode);
			}
			
			$groupId = (int) $this->arSystemGroup[$groupCode]["ID"];
			
			if ($saveInArray && !is_array($groupId)) {
				$groupId = [$groupId];
			}
		} catch (SystemException $e) {
			$debug = new Debug();
			$debug->onBacktrace();
			$debug->writeData($e->getMessage(), false, true);
		}
		
		return $groupId ?? 0;
	}
	
	/**
	 * Проверяет находится ли пользователь в определенной группе
	 *
	 * @param string $groupCode
	 * @return bool
	 */
	public function isInGroup(string $groupCode): bool {
		if (in_array($this->getGroupIdByCode($groupCode), $this->GROUP)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Объект создается внутри самого класса, только если у класса нет экземпляра
	 *
	 * @param int $ID
	 * @return null|Instance
	 */
	static function getInstance($ID = 0) {
		if (empty(self::$instance)) {
			self::$instance[$ID] = new User($ID);
		} elseif (!array_key_exists($ID, self::$instance)) {
			self::$instance[$ID] = new User($ID);
		}
		
		return self::$instance[$ID];
	}
}