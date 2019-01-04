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

/**
 * Class User_Group_Uninstall
 * @package SavitskyiHub\BxHelpers\Helpers\Install
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 *
 *
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Класс устанавливает необходимые пользовательские группы которые нужны для работы над контролем и отслеживанием пользователей.
 * ВАЖНО!!! При повторном запуске, перезапись групп не будет осуществлятся
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
			
			echo "\r\n\r\nДеинсталляция пользовательских групп прошла успешно";
		}
	}

	/**
	 * Запускает процесс деинсталляции необходимых групп
	 * - если в группе существует привязка пользователей, група удалена не будет;
	 */
	private static function uninstalledUserGroup() {
//		global $APPLICATION;
//
//		foreach (self::$arGroupCreate as $arNewGroupField) {
//			$group = new \CGroup;
//			$group->Add($arNewGroupField);
//		}
//
//		if ($APPLICATION->LAST_ERROR) {
//			echo "\r\n\r\n".$APPLICATION->LAST_ERROR->msg;
//		}
	
	//если есть пользователи не удалять
	
	//if(IntVal($del_id)>2)
	//{
	//	$del_id = IntVal($del_id);
	//	$group = new CGroup;
	//	$DB->StartTransaction();
	//	if(!$group->Delete($del_id))
	//	{
	//		$DB->Rollback();
	//		$strError.=GetMessage("DELETE_ERROR");
	//	}
	//	$DB->Commit();
	//}
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