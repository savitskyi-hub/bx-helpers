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
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class Mail_Install_Highload
 * @package SavitskyiHub\BxHelpers\Helpers\Install
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс устанавливает Highload-блок с необходимыми свойствами которые нужны для роботы с отправкой оповещений администрации сайта.
 * ВАЖНО!!! При повторном запуске, перезапись таблиц не будет осуществлятся, нужно запустить деинсталляцию, и перезапустить процесс инсталяции
 */
final class Mail_Install_Highload extends Highload_Installer
{
	use ClassTrait;
	
	protected static $name = 'HelpersMailSend';
	protected static $tableName = '2hlblock_helpers_mail_send';
	protected static $langNameRU = '✉ Письма - Администрация';
	protected static $langNameEN = '✉ Letters - Administration';
	protected static $mapCreatedField = [
		"UF_TYPE_STATUS" => [
			'USER_TYPE_ID' => 'enumeration',
			'MANDATORY' => 'Y',
			'EDIT_FORM_LABEL' => ['ru' => 'Статус обработки', 'en' => 'Processing Status'],
			'HELP_MESSAGE' => [
				'ru' => 'Описание статуса обработки письма',
				'en' => 'Description of the letter processing status'
			],
			'SETTINGS' => [
				"DISPLAY" => "UI",
				"LIST_HEIGHT" => "3",
			],
			"ENUM_LIST" => [
				"n0" => ["XML_ID" => "NOT_PROCESSED", "VALUE" => "Не обработан", "SORT" => 100, "DEF" => "Y"],
				"n1" => ["XML_ID" => "IN_PROCESS", "VALUE" => "В процессе", "SORT" => 200, "DEF" => ""],
				"n2" => ["XML_ID" => "SUCCESS_FIXED", "VALUE" => "Исправлено", "SORT" => 300, "DEF" => ""],
				"n3" => ["XML_ID" => "NOTHING_WRONG", "VALUE" => "Ничего страшного", "SORT" => 400, "DEF" => ""]
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
				"n2" => ["XML_ID" => "NOTICE", "VALUE" => "Уведомление", "SORT" => 300, "DEF" => "Y"]
			]
		],
		"UF_CODE" => [
			'USER_TYPE_ID' => 'string',
			'MANDATORY' => 'Y',
			'SHOW_FILTER' => 'S',
			'EDIT_IN_LIST' => 'N',
			'EDIT_FORM_LABEL' => ['ru' => 'Символьный код', 'en' => 'Symbolic code'],
			'HELP_MESSAGE' => [
				'ru' => 'Код ошибки/предупреждения/уведомления (перед отправкой лимит будет считатся по суме записей с одинаковым кодом)',
				'en' => 'Error/warning/notification code (before sending the limit will be counted by the sum of entries with the same code)'
			],
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
			'EDIT_IN_LIST' => 'N',
			'USER_TYPE_ID' => 'datetime',
			'EDIT_FORM_LABEL' => ['ru' => 'Время создания', 'en' => 'Time of creation'],
			'SETTINGS' => [
				'DEFAULT_VALUE' => ['TYPE' => 'NOW', 'VALUE' => ''],
			]
		]
	];
	
	private static $mailEventType = "SAVITSKYI_BXHELPERS_HELPERS_MAIL";
	
	/**
	 * Mail_HighloadTable constructor
	 */
	public function __construct(string $prefixTableName) {
		parent::$name = self::$name;
		parent::$tableName = self::$tableName;
		parent::$langNameRU = self::$langNameRU;
		parent::$langNameEN = self::$langNameEN;
		parent::$mapCreatedField = self::$mapCreatedField;
		parent::__construct($prefixTableName);
		
		/**
		 * Почтовые события и шаблоны
		 */
		self::installedMailEventType();
	}
	
	/**
	 * Запускает процесс инсталяции почтовых событий и шаблонов что нужны для функционала оповещения администрации сайта
	 */
	private static function installedMailEventType() {
		Loader::includeModule("main");
		
		global $APPLICATION;
		
		$DESC = [];
		$NAME = [
			"RU" => "Уведомления администрации сайта в случаи возникновения ошибки или предупреждения",
			"EN" => "Notifications of site administration in case of errors or warnings"
		];
		
		$DESC["RU"] = "#TYPE_SEND# - тип оповещения";
		$DESC["RU"] .= "\r\n#DATETIME_SEND# - время отправки";
		$DESC["RU"] .= "\r\n#TEXT_MESSAGE# - содержимое письма";
		
		$DESC["EN"] = "#TYPE_SEND# - alert type";
		$DESC["EN"] .= "\r\n#DATETIME_SEND# - dispatch time";
		$DESC["EN"] .= "\r\n#TEXT_MESSAGE# - letter content";
		
		$eventType = new \CEventType();
		$eventType->Add(["LID" => "ru", "EVENT_NAME" => self::$mailEventType, "NAME" => $NAME["RU"], "DESCRIPTION" => $DESC["RU"]]);
		$eventType->Add(["LID" => "en", "EVENT_NAME" => self::$mailEventType, "NAME" => $NAME["EN"], "DESCRIPTION" => $DESC["EN"]]);
		
		if (!self::isInstalledEventMessage()) {
			$MESSAGE = "Во время работы функционала произошел перехват исключения,\r\n";
			$MESSAGE .= "письмо уже отправлено разработчику и в скором времени он исправит проблему.\r\n";
			$MESSAGE .= "----------\r\n";
			$MESSAGE .= "Дополнительные сведения:\r\n";
			$MESSAGE .= "Время отправки: #DATETIME_SEND#\r\n";
			$MESSAGE .= "Тип оповещения: #TYPE_SEND#\r\n";
			$MESSAGE .= "Описание: #TEXT_MESSAGE#\r\n";
			$MESSAGE .= "----------\r\n";
			$MESSAGE .= "На письмо отвечать не нужно!";
			
			foreach (Variable::getAllSitesInfo() as $lid => $param) {
				(new \CEventMessage())->Add([
					'ACTIVE' => 'Y',
					'EVENT_NAME' => self::$mailEventType,
					"LID" => $lid,
					"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
					"EMAIL_TO" => "   ",
					"SUBJECT" => "Предупреждение работы функционала на сайте: #SITE_NAME#",
					"BODY_TYPE" => "text",
					"MESSAGE" => $MESSAGE
				]);
			}
			
			if ($APPLICATION->LAST_ERROR) {
				echo "\r\n\r\n".$APPLICATION->LAST_ERROR->msg;
			}
		}
	}
	
	/**
	 * Чтобы не дублировать создания почтовых шаблонов реализуем проверку на существования одного из них
	 * - если существует, повторно создаваться шаблоны не будут
	 *
	 * @return bool
	 */
	private static function isInstalledEventMessage(): bool {
		$rsMessage = \CEventMessage::GetList($by = "id", $order = "desc", [
			"EVENT_NAME" => self::$mailEventType
		]);
		
		if ($rsMessage && $rsMessage->result->num_rows) {
			return true;
		}
		
		return false;
	}
}