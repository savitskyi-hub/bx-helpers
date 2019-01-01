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
 *
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс устанавливает Highload-блок на сайте с необходимыми свойствами которые нужны для роботы с текущим пространством имен.
 * ВАЖНО!!! При повторном запуске, перезапись таблицы не будет осуществлятся, нужно удалить Highload-блок, и перезапустить процес инсталяции
 */
final class Mail_Install_Highload extends Highload_Installer
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
	 * Mail_HighloadTable constructor
	 *
	 * @param string $prefixTableName
	 * @param bool $isUninstall
	 */
	public function __construct(string $prefixTableName, bool $isUninstall = false) {
		if (!$isUninstall) {
			parent::$name = self::$name;
			parent::$tableName = self::$tableName;
			parent::$langNameRU = self::$langNameRU;
			parent::$langNameEN = self::$langNameEN;
			parent::$mapCreatedField = self::$mapCreatedField;
			
			parent::__construct($prefixTableName);
			self::installedMailEventType();
		} else {
			self::uninstalledMailEventType();
		}
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
			
			foreach (Variable::getSitesInfo() as $lid => $param) {
				(new \CEventMessage)->Add([
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
	 * - если существует повторно создаваться шаблоны не будут
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
	
	/**
	 * Запускает процесс удаления почтовых событий и шаблонов (нужен для деинсталляции библиотеки)
	 */
	private static function uninstalledMailEventType() {
		Loader::includeModule("main");
		
		global $APPLICATION;
		
		$eventType = new \CEventType();
		$eventType->Delete(self::$mailEventType);
		
		if (self::isInstalledEventMessage()) {
			$rsDeleteMessage = \CEventMessage::GetList($by = "id", $order = "desc", ["EVENT_NAME" => self::$mailEventType]);
			
			while($arDeleteMessage = $rsDeleteMessage->Fetch()) {
				(new \CEventMessage())->Delete($arDeleteMessage["ID"]);
			}
			
			if ($APPLICATION->LAST_ERROR) {
				echo "\r\n\r\n".$APPLICATION->LAST_ERROR->msg;
			}
		}
		
		echo "\r\nУдаление почтовых событий и шаблонов прошло успешно!";
	}
}