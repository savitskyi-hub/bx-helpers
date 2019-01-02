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
 * Class
 * @package SavitskyiHub\BxHelpers\Helpers\Install
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс удаляет Highload-блок с его свойствами которые нужны для роботы отправки оповещений администрации сайта
 */
final class Mail_Uninstall_Highload extends Highload_Uninstaller
{
	use ClassTrait;
	
	private static $mailEventType = "SAVITSKYI_BXHELPERS_HELPERS_MAIL";
	
	protected static $name = 'HelpersMailSend';
	protected static $tableName = '2hlblock_helpers_mail_send';
	
	/**
	 * Mail_HighloadTable constructor
	 */
	public function __construct(string $prefixTableName) {
		parent::$name = self::$name;
		parent::$tableName = self::$tableName;
		parent::__construct($prefixTableName);
		
		/**
		 * Почтовые события и шаблоны
		 */
		self::uninstalledMailEventType();
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
	
	/**
	 * Чтобы не запускать ложно процесс удаления почтовых шаблонов реализуем проверку существования
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