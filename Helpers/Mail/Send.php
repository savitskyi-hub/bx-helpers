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

use Bitrix\Main\Mail\Event;
use Bitrix\Main\SystemException;
use SavitskyiHub\BxHelpers\Helpers\Highload\Instance;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class Send
 * @package SavitskyiHub\BxHelpers\Helpers\Mail
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс .............................
 */
class Send
{
    /**
	 * Через какой интервал времени (Hours) доступно отправлять повторно письмо администратору
     * @var int
     */
    private static $periodBlocked = 1;
    
	/**
	 * Количество доступных отправок определенного типа в допустимый интервал времени
	 * @var int
	 */
	private static $limitTypeSend =  3;
	

	/**
	 * Почтовое событие ..........
	 */
	
	
	/**
	 * Реализует отправку письма администрации сайта в случаи возникновения ошибки или предупреждения в функционале проекта
	 *
	 * @param string $message
	 * @param string $typeSendEvent - символьный код почтового события;
	 * @param string $typeReporting - тип оповещения;
	 * @param bool $skip - пропустить проверку на повторною отправку;
	 *
	 * @return bool
	 * @throws SystemException
	 */
	static function Admin(string $message, string $typeSendEvent, string $typeReporting, bool $skip = false): bool {
		
		try {
			$entityName = Install_HighloadTable::get("name");
			$entityID = Instance::getIdByEntityName($entityName);
			$nameObjEntity = "HLBLOCK_".$entityID."_UF_TYPE_SEND";
			$typeReporting = Variable::$bxEnumField["XML2ID"][$nameObjEntity][$typeReporting];
			
			$timeSend = date("d.m.Y H:i:s");
			$isSuccess = false;
			
			if (!$typeReporting) {
				throw new SystemException('Неверный символьный код значения в объекте: '.$nameObjEntity.' - '.$typeReporting);
			}
			
			if (!$skip && !self::checkLimitSendAdmin($typeSendEvent, $typeReporting, $entityID)) {
				return false;
			}
			
			$rsSend = Event::send([
				"EVENT_NAME" => $typeSendEvent,
				"LID" => SITE_ID,
				"C_FIELDS" => ["TEXT_MESSAGE" => $message]
			]);

			/**
			 * Логируем отправку
			 */
			if (!$rsSend->isSuccess()) {
				throw new SystemException("Ошибка отправки письма:\r\ntypeSendEvent: ".$typeSendEvent."\r\ntypeReporting: ".$typeReporting."\r\ntime: ".$timeSend);
			} else {
				
				//new Instance($entityID);
				
//				$arParams = [
//					"UF_TYPE_EMAIL_EVENT" => $typeSendEvent,
//					"UF_TYPE_SEND" => $typeReporting,
//					"UF_TEXT" => $message
//				];
//
//				//				Highload::set(Highload::getTableId('ST2SendMailAdminHistory'), $arParams);
//				//				return true;
			
			}
			
		} catch (SystemException $e) {
			throw $e;
			
			//            if (self::get('logging')) {
			//                $logging = Log::Initial('LogFile');
			//                $logging->setValue($e->getMessage().self::getSuffixError());
			//                $logging->setWhereLogging('email-error');
			//                $logging->push();
			//            }
		}
		
		return $isSuccess;
	}
	
	/**
	 * Метод проверяет доступно ли отправлять "повторно" администрации письма
	 *
	 * @param string $typeEmailEvent - символьный код почтового события;
	 * @param string $typeReporting - тип оповещения;
	 * @param int $entityID
	 *
	 * @return bool
	 */
	private static function checkLimitSendAdmin(string $typeEmailEvent, string $typeReporting, int $entityID): bool {
		$obHd = new Instance($entityID);
		$rsHistory = $obHd->entityDataClass::getList([
			'select' => ['ID'],
			'filter' => [
				"UF_TYPE_EMAIL_EVENT" => $typeEmailEvent,
				"UF_TYPE_SEND" => $typeReporting,
				">=UF_DATETIME_CREATE" => [ConvertTimeStamp(time() - 3600 * self::$periodBlocked, "FULL")]
			]
		]);
		
		if ($rsHistory && ($rsHistory->getSelectedRowsCount() >= self::$limitTypeSend)) {
			return true;
		}
		
		return false;
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
			
			$entityName = Install_HighloadTable::get("name");
			
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