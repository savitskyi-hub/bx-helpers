<?php

namespace SavitskyiHub\BxHelpers\Helpers\Mail;

use Bitrix\Main\Mail\Event;
use Bitrix\Main\SystemException;

/**
 * Class Send
 * @package SavitskyiHub\BxHelpers\Helpers\Mail
 *
 * ...................................
 */
class Send
{
    /**
	 * Через какой интервал времени (Hours) доступно отправлять повторно письмо администратору
     * @var int
     */
    private static $periodBlocked = 1;
    
    /**
	 * Нужно ли логировать отправку сообщения
     * @var bool
     */
	private static $logging = false;
    
	/**
	 * Количество доступных отправок определенного типа в допустимый интервал времени
	 * @var int
	 */
	private static $limitTypeSend =  3;
	
	/**
	 * Метод реализует отправку письма администрации сайта в случаи возникновения ошибки или предупреждения в функционале проекта
	 * @param string $message
	 * @param string $typeSendEvent - символьный код почтового события;
	 * @param string $typeReporting - тип оповещения;
	 * @param bool $skip - пропустить проверку на повторною отправку;
	 * @return bool
	 */
	static function Admin(string $message, string $typeSendEvent, string $typeReporting, bool $skip = false): bool {
		try {
			
			if (!$skip && !self::checkLimitSendAdmin($typeSendEvent, $typeReporting)) {
				return false;
			}
			
			$rSend = Event::send([
				"EVENT_NAME" => $typeSendEvent,
				"LID" => SITE_ID,
				"C_FIELDS" => ["TEXT_MESSAGE" => $message]
			]);
			
			/**
			 * Логируем отправку
			 */
			if (!$rSend->isSuccess()) {
				//throw new SystemException('Ошибка отправки письма (type_email_event: '.$type_email_event.' | emailToSend: '.$email2Send.')');
			} else {
				//				$arParams = [
				//					"UF_TEXT" => $message,
				//					"UF_TYPE_SEND" => Variable::$bxEnumFields["XML2ID"]["HLBLOCK_3_UF_TYPE_SEND"][$typeReporting],
				//					"UF_TYPE_EMAIL_EVENT" => $typeSendEvent,
				//					"UF_DATETIME" => date("d.m.Y H:i:s")
				//				];
				//
				//				Highload::set(Highload::getTableId('ST2SendMailAdminHistory'), $arParams);
				//				return true;
			}
			
		} catch (SystemException $e) {
			
			//            if (self::get('logging')) {
			//                $logging = Log::Initial('LogFile');
			//                $logging->setValue($e->getMessage().self::getSuffixError());
			//                $logging->setWhereLogging('email-error');
			//                $logging->push();
			//            }
			
		}
	}
	
	/**
	 * Метод проверяет доступно ли отправлять "повторно" администрации письма
	 * @param string $typeEmailEvent - символьный код почтового события;
	 * @param string $typeReporting - тип оповещения;
	 * @return bool
	 */
	private static function checkLimitSendAdmin(string $typeEmailEvent, string $typeReporting): bool {
		
		
		//			$arHistorySendEmail = Highload::getList(Highload::getTableId('ST2SendMailAdminHistory'), [
		//				'select' => ['ID'],
		//				'filter' => [
		//					">=UF_DATETIME" => [ConvertTimeStamp(time() - 3600 * self::$periodBlocked, "FULL")],
		//					"UF_TYPE_EMAIL_EVENT" => $type_email_event,
		//					"UF_TYPE_SEND" => Variable::$bxEnumFields["XML2ID"]["HLBLOCK_3_UF_TYPE_SEND"][$type_send],
		//				],
		//				'count_total' => true
		//			]);
		//
		//			if ($arHistorySendEmail && $arHistorySendEmail->getCount() >= self::get('limitTypeSend')[$type_send]) {
		//				return false;
		//			} else {
		//				return true;
		//			}
		
	}
	
	/**
	 * Метод реализует отправку почтового события на определенный E-mail адрес (в случаи ошибки происходить логирования)
	 * @param string $typeEmailEvent - символьный код почтового события;
	 * @param string $sendTo - на какой адрес отправлять;
	 * @param string $message
	 * @param string $title
	 * @return bool
	 */
	static function Mail(string $typeEmailEvent, string $sendTo, string $message = "", string $title = ""): bool {
		try {
			
			if (!$typeEmailEvent || !$sendTo) {
				throw new SystemException('Передано не все данные для отправки уведомления');
			}
			
			//	            if ($testSendEmail = self::get('testSendEmail')) {
			//	                $email2Send = $testSendEmail;
			//	            }
			//
			//	            $r = Event::send([
			//	                "EVENT_NAME" => $type_email_event,
			//	                "LID" => SITE_ID,
			//	                "C_FIELDS" => [
			//	                    "EMAIL_TO" => $email2Send,
			//	                    "STR_TITLE" => $titleMessage,
			//	                    "TEXT_MESSAGE" => $textMessage
			//	                ]
			//	            ]);
			//
			//	            if (!$r->isSuccess()) {
			//	                throw new SystemException('Ошибка отправки письма (type_email_event: '.$type_email_event.' | emailToSend: '.$email2Send.')');
			//	            }
			//
		} catch (SystemException $e) {
			//
			//	            if (self::get('logging')) {
			//	                $logging = Log::Initial('LogFile');
			//	                $logging->setValue($e->getMessage().self::getSuffixError());
			//	                $logging->setWhereLogging('email-error');
			//	                $logging->push();
			//	            }
			//
		}
		
		
	}
}