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
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;

/**
 * Class User_Group_Install
 * @package SavitskyiHub\BxHelpers\Helpers\Install
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс устанавливает необходимые пользовательские группы которые нужны для работы над контролем и отслеживанием пользователей.
 * ВАЖНО!!! При повторном запуске, перезапись групп не будет осуществлятся
 */
final class User_Group_Install
{
	use ClassTrait;
	
	protected static $arGroupCreate = [
		"REGISTERED_USERS" => [
			"ACTIVE" => "Y",
			"C_SORT" => 5,
			"NAME" => "Зарегистрированные пользователи",
			"DESCRIPTION" => "При регистрации пользователь будет автоматически присоединен к этой группе",
			"USER_ID" => [1],
			"STRING_ID" => "REGISTERED_USERS"
		],
		"BANED_USERS" => [
			"ACTIVE" => "Y",
			"C_SORT" => 6,
			"NAME" => "Заблокированные пользователи",
			"DESCRIPTION" => "Пользователи что заблокированы за плохое поведение на сайте",
			"USER_ID" => [],
			"STRING_ID" => "BANED_USERS"
		],
		"DELETED_USERS" => [
			"ACTIVE" => "Y",
			"C_SORT" => 7,
			"NAME" => "Пользователи что удалены",
			"DESCRIPTION" => "Пользователи, которые считаются удаленными (авторизоваться не получаеться)",
			"USER_ID" => [],
			"STRING_ID" => "DELETED_USERS"
		]
	];
	
	/**
	 * User_Group_Install constructor
	 */
	public function __construct() {
		Loader::includeModule("main");
		
		if (!self::isInstalledUserGroup()) {
			self::installedUserGroup();
			
			echo "\r\n\r\nИнсталляция пользовательских групп прошла успешно";
		}
	}
	
	/**
	 * Запускает процесс инсталляции необходимых групп
	 */
	private static function installedUserGroup() {
		global $APPLICATION;
		
		foreach (self::$arGroupCreate as $arNewGroupField) {
			$group = new \CGroup;
			$group->Add($arNewGroupField);
		}
		
		if ($APPLICATION->LAST_ERROR) {
			echo "\r\n\r\n".$APPLICATION->LAST_ERROR->msg;
		}
	}
	
	/**
	 * Чтобы не дублировать создания пользовательских групп реализуем проверку на существования одной из них
	 *
	 * @return bool
	 */
	private static function isInstalledUserGroup(): bool {
		$by = "c_sort";
		$order = "asc";
		$rsGroups = \CGroup::GetList($by, $order, ["STRING_ID" => implode(" | ", array_keys(self::$arGroupCreate))]);
		
		if ($rsGroups && $rsGroups->result->num_rows == 3) {
			return true;
		}
		
		return false;
	}
}