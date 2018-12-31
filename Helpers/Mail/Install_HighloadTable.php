<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Mail;

use Bitrix\Main\Loader;
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;

/**
 * Class Install_HighloadTable
 * @package SavitskyiHub\BxHelpers\Helpers\Mail
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс устанавливает Highload-блок на сайте с необходимыми свойствами которые нужны для роботы с текущим пространством имен.
 * ВАЖНО!!! При повторном запуске, перезапись таблицы не будет осуществлятся, нужно удалить Highload-блок, и перезапустить процес инсталяции
 */
class Install_HighloadTable extends \SavitskyiHub\BxHelpers\Helpers\Highload\Installer
{
	use ClassTrait;
	
	private static $mailEventType = "SAVITSKYI_BXHELPERS_HELPERS_MAIL";
	
	protected static $name = 'HelpersMailSend';
	protected static $tableName = '2hlblock_helpers_mail_send';
	protected static $langNameRU = '✉ Письма - Администрация';
	protected static $langNameEN = '✉ Letters - Administration';
	protected static $mapCreatedField = [
		"UF_TYPE_EMAIL_EVENT" => [
			'USER_TYPE_ID' => 'string',
			'MANDATORY' => 'Y',
			'SHOW_FILTER' => 'S',
			'EDIT_IN_LIST' => 'N',
			'EDIT_FORM_LABEL' => ['ru' => 'Тип почтового события', 'en' => 'Mail event type'],
			'HELP_MESSAGE' => [
				'ru' => 'Код почтового события в системе',
				'en' => 'The code of the mail event in the system'
			]
		],
		"UF_TYPE_SEND" => [
			'USER_TYPE_ID' => 'enumeration',
			'MANDATORY' => 'Y',
			'EDIT_FORM_LABEL' => ['ru' => 'Тип письма', 'en' => 'Type of letter'],
			'HELP_MESSAGE' => [
				'ru' => 'Описание характера письма',
				'en' => 'Description of the nature of the letter'
			],
			'SETTINGS' => [
				"DISPLAY" => "UI",
				"LIST_HEIGHT" => "3",
			],
			"ENUM_LIST" => [
				"n0" => ["XML_ID" => "ERROR", "VALUE" => "Ошибка работы программы", "SORT" => 100, "DEF" => ""],
				"n1" => ["XML_ID" => "WARNING", "VALUE" => "Предупреждение о предстоящей ошибки", "SORT" => 200, "DEF" => ""],
				"n2" => ["XML_ID" => "NOTICE", "VALUE" => "Уведомление", "SORT" => 300, "DEF" => "n2"]
			]
		],
		"UF_TEXT" => [
			'USER_TYPE_ID' => 'string',
			'MANDATORY' => 'Y',
			'SHOW_FILTER' => 'S',
			'EDIT_IN_LIST' => 'N',
			'EDIT_FORM_LABEL' => ['ru' => 'Текст сообщения', 'en' => 'Message text'],
			'HELP_MESSAGE' => [
				'ru' => 'Причина отправки, описание ошибки, напоминания',
				'en' => 'Reason for sending, error description, reminder'
			],
			'SETTINGS' => [
				'SIZE' => '60',
				'ROWS' => '2'
			]
		],
		"UF_DATETIME_CREATE" => [
			'MANDATORY' => 'Y',
			'USER_TYPE_ID' => 'datetime',
			'EDIT_FORM_LABEL' => ['ru' => 'Время создания', 'en' => 'Time of creation'],
			'SETTINGS' => [
				'DEFAULT_VALUE' => ['TYPE' => 'NOW', 'VALUE' => ''],
			]
		]
	];
	
	/**
	 * Install constructor
	 */
	public function __construct(string $prefixTableName) {
		parent::$name = self::$name;
		parent::$tableName = self::$tableName;
		parent::$langNameRU = self::$langNameRU;
		parent::$langNameEN = self::$langNameEN;
		parent::$mapCreatedField = self::$mapCreatedField;
		
		parent::__construct($prefixTableName);
		
		/**
		 *
		 */
		self::installedMailEventType();
	}
	
	/**
	 *
	 */
	private static function installedMailEventType() {
		Loader::includeModule("main");
		
		$NAME = ["RU" => "55", "EN" => "66"];
		$DESC = ["RU" => "", "EN" => ""];
		
		$DESC["RU"] .= "#TYPE_SEND# - ";
		$DESC["RU"] .= "#DATETIME_SEND# - ";
		$DESC["RU"] .= "#TEXT_MESSAGE# - ";
		
		$DESC["EN"] .= "#TYPE_SEND# - ";
		$DESC["EN"] .= "#DATETIME_SEND# - ";
		$DESC["EN"] .= "#TEXT_MESSAGE# - ";
		
		$eventType = new \CEventType();
		$rsCreatedRu = $eventType->Add(["LID" => "ru", "EVENT_NAME" => self::$mailEventType, "NAME" => $NAME["RU"], "DESCRIPTION" => $DESC["RU"]]);
		$rsCreatedEn = $eventType->Add(["LID" => "en", "EVENT_NAME" => self::$mailEventType, "NAME" => $NAME["EN"], "DESCRIPTION" => $DESC["EN"]]);
		
		if ($eventType->LAST_ERROR) {
			echo "\r\n".$eventType->LAST_ERROR;
		}
		
		/**
		 *
		 */
		
		// создает дубликаты !!! проверку сделать
//		$em = new \CEventMessage;
//
//		$arFields = array(
//			'ACTIVE'     =>  'Y',
//			'EVENT_NAME' =>  self::$mailEventType,
//			"LID" => array("ru","en"),
//			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
//			"EMAIL_TO" => "",
//			"SUBJECT" => "Тема сообщения",
//			"BODY_TYPE" => "text",
//			"MESSAGE" => "
//				Текст сообщения
//			"
//		);
//
//		$result = $em->Add( $arFields );
	}
	
	/**
	 *
	 */
	private static function uninstalledMailEventType() {
	
	}
}