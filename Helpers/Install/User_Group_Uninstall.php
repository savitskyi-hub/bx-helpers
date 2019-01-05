<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Install;

use Bitrix\Main\Loader;
use SavitskyiHub\BxHelpers\Helpers\Main\User;

/**
 * Class User_Group_Uninstall
 * @package SavitskyiHub\BxHelpers\Helpers\Install
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс производит деинсталляцию пользовательских групп которые были созданы через класс инсталяции "User_Group_Install"
 */
final class User_Group_Uninstall
{
	/**
	 * User_Group_Uninstall constructor
	 */
	public function __construct() {
		Loader::includeModule("main");
		
		if (self::isInstalledUserGroup()) {
			self::uninstalledUserGroup();
			
			echo "\r\n\r\nДеинсталляция пользовательских групп прошла успешно!";
		}
	}
	
	/**
	 * Запускает процесс деинсталляции необходимых групп
	 * - если в группе существует привязка пользователей, група удалена не будет;
	 */
	private static function uninstalledUserGroup() {
		global $APPLICATION, $DB;
		
		$userMethod = User::getInstance();
		$arCreatedGroup = User_Group_Install::getStaticProp("arGroupCreate");
		$arGroupNameCode = array_keys($arCreatedGroup);
		
		$CGroup = new \CGroup();
		$strError = '';
		
		foreach ($arGroupNameCode as $nameCode) {
			$groupID = $userMethod->getGroupIdByCode($nameCode);
			
			if ($groupID) {
				$cntUserInGroup = count(\CGroup::GetGroupUser($groupID));
				
				if ("REGISTERED_USERS" == $nameCode && 1 < $cntUserInGroup) {
					$strError .= "\r\nВ группе ".$nameCode." существует привязка пользователей";
					continue;
				} elseif ("REGISTERED_USERS" != $nameCode && 0 < $cntUserInGroup) {
					$strError .= "\r\nВ группе ".$nameCode." существует привязка пользователей";
					continue;
				}
				
				$DB->StartTransaction();
				
				if (!$CGroup->Delete($groupID)) {
					$DB->Rollback();
					$strError .= "\r\nНевозможно удалить группу: ".$nameCode;
				}
				
				$DB->Commit();
			}
		}
		
		if ($APPLICATION->LAST_ERROR || $strError) {
			echo "\r\n\r\n".$APPLICATION->LAST_ERROR->msg;
			echo "\r\n\r\n".$strError;
		}
	}
	
	/**
	 * Чтобы не запускать ложно процесс удаления пользовательских групп реализуем проверку существования
	 *
	 * @return bool
	 */
	private static function isInstalledUserGroup(): bool {
		$by = "c_sort";
		$order = "asc";
		$arCreatedGroup = User_Group_Install::getStaticProp("arGroupCreate");
		$rsGroups = \CGroup::GetList($by, $order, ["STRING_ID" => implode(" | ", array_keys($arCreatedGroup))]);
		
		if ($rsGroups && $rsGroups->result->num_rows >= 1) {
			return true;
		}
		
		return false;
	}
}