<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\User;

//use Bitrix\Main\SystemException;
//use Bitrix\Main\UserTable;
use Bitrix\Main\Loader;
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;

/**
 * Class Instance
 * @package SavitskyiHub\BxHelpers\Helpers\User
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
class Instance
{
	use ClassTrait;

	/**
	 * Идентификатор пользователя
	 * @var int
	 */
	static $ID = 0;

	/**
	 * Авторизован ли пользователь
	 * @var bool
	 */
	static $isAuth = false;
	
	//    /**
	//     * @var array $openPages - список дополнительных  url адресов, которые доступны (открытые в доступе) для неавторизованных пользователей;
	//     */
	//    protected static $openPages = [];
	//
	//    /**
	//     * @var array - свойство об информации сыстемных груп, что приведены к человеческому пониманию.
	//     * @warning - редактирование свойства может привести к непредсказуемым ситуациям.
	//     */
	//    protected static $systemGroup = [];
	//
	//    /**
	//     * @var array - массив груп в который входит пользователь;
	//     */
	//    protected static $GROUP = [];
	//
	//    /**
	//     * @var array - массив содержащий свойства пользователя;
	//     */
	//    protected static $PROP = [];
	
	/**
	 * User constructor.
	 */
	public function __construct() {
		try {
			//Loader::includeModule("main");
			
			// Создать автоматом 3 групы
			// Registred, delete, ban
			
			//            // Сохраняем список системных груп для удобного использования
			//            self::set("systemGroup", $this->getSystemGroup());
			//
			//            if ($GLOBALS["USER"]->IsAuthorized()) {
			//
			//                // Отмечаем что пользователь сейчас активен
			//                $GLOBALS["USER"]->SetLastActivityDate(self::$ID);
			//
			//                self::set("ID", $GLOBALS["USER"]->GetID());
			//                self::set("isAuth", 1);
			//
			//                // Получаем список груп в которые входит пользователь
			//                self::set("GROUP", \CUser::GetUserGroup(self::$ID));
			//
			//                // Удаляем стандартные групы и групу глобального администратора
			//                self::set("GROUP", self::getCleanDefaultSystemGroup(self::$GROUP));
			//
			//                // Если пользователь заблокирован сообщим ему об этом
			//                if (self::isInGroup(self::$GROUP, "BAN")) {
			//                    Variable::set('ban', true);
			//                }
			//
			//                // Если пользователь был удален, разлогиниваем его
			//                if (self::isInGroup(self::$GROUP, "DELETED")) {
			//                    LocalRedirect(SITE_DIR.'?logout=yes');
			//                }
			//
			//                // Получаем персональные данные пользователя
			//                self::set("PROP", $this->getProps());
			//
			//            }
			
		} catch (SystemException $e) {
			//$e->getMessage()
		}
	}
	//
	//    /**
	//     * Метод возращает список системных груп.
	//     * @return array
	//     */
	//    private function getSystemGroup() {
	//        $arReturn = [];
	//        $rsGroups = \CGroup::GetList(($by = "c_sort"), ($order = "asc"), ["ACTIVE" => "Y"]);
	//
	//        if ($rsGroups->result->num_rows) {
	//            while ($row = $rsGroups->fetch()) {
	//                if (!empty($row["STRING_ID"])) {
	//                    $arReturn[$row["STRING_ID"]] = ["ID" => $row["ID"], "NAME" => $row["NAME"], "DESCRIPTION" => $row["DESCRIPTION"]];
	//                }
	//            }
	//        }
	//
	//        return $arReturn;
	//    }
	//
	//    /**
	//     * Метод возвращает чистый список груп, удаляя стандартные (системные).
	//     * @return array
	//     */
	//    static function getCleanDefaultSystemGroup(array $arGroup) {
	//        foreach ($arGroup as $id) {
	//            if ($id < 5) {
	//                unset($arGroup[array_search($id, $arGroup)]);
	//            }
	//        }
	//
	//        return array_values($arGroup);
	//    }
	//
	//    /**
	//     * Метод проверяет находится ли пользователь в определенной системной групе пользователей.
	//     * @param array $arGroup
	//     * @parap string
	//     * @return boolean
	//     */
	//    static function isInGroup($arGroup, $keyGroupName) {
	//        if (in_array(self::getGroupId($keyGroupName), $arGroup)) {
	//            return true;
	//        } else {
	//            return false;
	//        }
	//    }
	//
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
	//     * Метод для получения свойств пользователя.
	//     * @param $user_id
	//     * @return array
	//     */
	//    private function getProps() {
	//
	//        try {
	//
	//            if (!self::$ID) {
	//                throw new SystemException("Failed to get user ID");
	//            }
	//
	//            $rsProps = UserTable::GetList(["filter" => ["ID" => self::$ID], "limit" => 1, "select" => ["*", "UF_*"], "count_total" => true]);
	//
	//            if ($rsProps && $rsProps->getCount()) {
	//                $arProps = $rsProps->fetch();
	//                unset($arProps["PASSWORD"]);
	//                unset($arProps["BX_USER_ID"]);
	//
	//                return $arProps;
	//            } else {
	//                return [];
	//            }
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
	//
	//    /**
	//     * Метод возвращает ID пользователя по его логину.
	//     * @param string $login
	//     * @return int
	//     */
	//    static function getIdbyLogin($login) {
	//        $rsUser = UserTable::GetList([
	//            "select" => ["ID"],
	//            "filter" => ["LOGIN" => $login],
	//            "limit" => 1,
	//            "count_total" => true
	//        ]);
	//
	//        if ($rsUser && $rsUser->getCount()) {
	//            return $rsUser->Fetch()["ID"];
	//        } else {
	//            return 0;
	//        }
	//    }
	//
	//    /**
	//     * Метод для получения списка пользователей и их свойств.
	//     * @param array $select
	//     * @param array $filter
	//     * @param array $order
	//     * @param int $limit
	//     * @return bool|array
	//     */
	//    static function getList($select = ["*", "UF_*"], $filter = ["ACTIVE" => "Y"], $order = ["ID" => "DESC"], $limit = 0) {
	//
	//        try {
	//
	//            $arReturn = [];
	//
	//            $rsUsers = UserTable::GetList([
	//                "filter" => $filter,
	//                "order" => $order,
	//                "limit" => $limit,
	//                "select" => $select
	//            ]);
	//
	//            if ($rsUsers && $rsUsers->getSelectedRowsCount()) {
	//                while ($arUser = $rsUsers->fetch()) {
	//                    unset($arUser["PASSWORD"]);
	//                    unset($arUser["BX_USER_ID"]);
	//
	//                    $arReturn[$arUser["ID"]] = $arUser;
	//                }
	//            }
	//
	//            return $arReturn;
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
	//     * Метод для изменения свойств пользователя.
	//     * @param int $id
	//     * @param array $params
	//     * @throws SystemException
	//     */
	//    static function update($id = 0, $params = []) {
	//
	//        try {
	//
	//            if (!$id || !$params) {
	//                throw new SystemException('Missing required parameters');
	//            }
	//
	//            $user = new \CUser();
	//            $user->Update($id, $params);
	//
	//            if ($user->LAST_ERROR) {
	//                throw new SystemException($user->LAST_ERROR);
	//            }
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
}