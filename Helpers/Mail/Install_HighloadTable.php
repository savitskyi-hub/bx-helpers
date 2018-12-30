<?php

namespace SavitskyiHub\BxHelpers\Helpers\Mail;

/**
 * Class Install_HighloadTable
 * @package SavitskyiHub\BxHelpers\Helpers\Mail
 *
 * Класс устанавливает Highload-блок на сайте с необходимыми свойствами которые нужны для роботы с текущим пространством имен;
 * ВАЖНО!!! При повторном запуске, перезапись таблицы не будет осуществлятся, нужно удалить Highload-блок, и перезапустить процес инсталяции
 */
class Install_HighloadTable extends \SavitskyiHub\BxHelpers\Helpers\Highload\Installer
{
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
	}
}