<?php


/**
 * Запускает процесс удаления почтовых событий и шаблонов (нужен для деинсталляции библиотеки)
 */
/*
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
*/