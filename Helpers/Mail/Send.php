<?php

namespace SavitskyiHub\BxHelpers\Helpers\Mail;

//use Bitrix\Main\Mail\Event;
//use Bitrix\Main\SystemException;

/**
 * Class Send
 * @package Local\Helpers\Mail
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
	 * Если свойство заполнено, будет осуществляться отправка тестовых писем на этот адрес
     * @var string
     */
    private static $testSendEmail = '';

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
	 * Идентификатор таблицы Highload-блока (нужно указать самостоятельно после инсталляции)
	 * @var int
	 */
	private static $tableID = 21;
	
	
	
	
	
	
    /**
     *
	 * Отправка письма администрации сайта в случаи возникновения ошибки на сайте.
     * @param $message - сообщение;
     * @param $type_send_event - тип сообщения;
     * @param bool $skip - пропустить проверку;
     * @return bool
     */
//    static function Admin($message, $typeSendEvent, $typeReporting, $skip = false) {
//
//        try {
//
//            if (!$skip && !self::checkLimitToAdminMail($typeSendEvent, $typeReporting)) {
//                return false;
//            }
//
//            $resultSend = Event::send([
//                "EVENT_NAME" => $typeSendEvent,
//                "LID" => SITE_ID,
//                "C_FIELDS" => ["TEXT_MESSAGE" => $message]
//            ]);
//
//            // Логируем отправку в БД
//            if ($resultSend->isSuccess()) {
//
//                $arParams = [
//                    "UF_TEXT" => $message,
//                    "UF_TYPE_SEND" => Variable::$bxEnumFields["XML2ID"]["HLBLOCK_3_UF_TYPE_SEND"][$typeReporting],
//                    "UF_TYPE_EMAIL_EVENT" => $typeSendEvent,
//                    "UF_DATETIME" => date("d.m.Y H:i:s")
//                ];
//
//                Highload::set(Highload::getTableId('ST2SendMailAdminHistory'), $arParams);
//                return true;
//            }
//
//        } catch (SystemException $e) {
//
////            if (self::get('logging')) {
////                $logging = Log::Initial('LogFile');
////                $logging->setValue($e->getMessage().self::getSuffixError());
////                $logging->setWhereLogging('email-error');
////                $logging->push();
////            }
//
//        }
//
//    }


//    /**
//     * Метож проверяет доступно ли отправлять (*повторно) администратору письма.
//     * @param $type_email_event - символьный код почтового события;
//     * @param $type_send - тип сообщения ошибки.
//     * @return bool
//     * @throws SystemException
//     */
//    private static function checkLimitToAdminMail($type_email_event, $type_send) {
//
//        try {
//
//            $arHistorySendEmail = Highload::getList(Highload::getTableId('ST2SendMailAdminHistory'), [
//                'select' => ['ID'],
//                'filter' => [
//                    ">=UF_DATETIME" => [ConvertTimeStamp(time() - 3600 * self::$periodBlocked, "FULL")],
//                    "UF_TYPE_EMAIL_EVENT" => $type_email_event,
//                    "UF_TYPE_SEND" => Variable::$bxEnumFields["XML2ID"]["HLBLOCK_3_UF_TYPE_SEND"][$type_send],
//                ],
//                'count_total' => true
//            ]);
//
//            if ($arHistorySendEmail && $arHistorySendEmail->getCount() >= self::get('limitTypeSend')[$type_send]) {
//                return false;
//            } else {
//                return true;
//            }
//
//        } catch (SystemException $e) {
//
//            if (self::get('exceptionGlobal')) {
//                Variable::set('error', $e->getMessage().self::getSuffixError());
//            } else {
//                throw $e;
//            }
//
//        }
//
//    }
//
//    /**
//     * Метод для отправки почтового шаблона по Email адресу.
//     * @param string $type_email_event
//     * @param string $email2Send
//     * @param string $textMessage
//     * @param string $titleMessage
//     */
//    static function sendMail($type_email_event, $email2Send, $textMessage = "", $titleMessage = "") {
//
//        try {
//
//            if (!$type_email_event || !$textMessage || !$titleMessage || !$email2Send) {
//                throw new SystemException('Передано не все данные для отправки уведомления');
//            }
//
//            if ($testSendEmail = self::get('testSendEmail')) {
//                $email2Send = $testSendEmail;
//            }
//
//            $r = Event::send([
//                "EVENT_NAME" => $type_email_event,
//                "LID" => SITE_ID,
//                "C_FIELDS" => [
//                    "EMAIL_TO" => $email2Send,
//                    "STR_TITLE" => $titleMessage,
//                    "TEXT_MESSAGE" => $textMessage
//                ]
//            ]);
//
//            if (!$r->isSuccess()) {
//                throw new SystemException('Ошибка отправки письма (type_email_event: '.$type_email_event.' | emailToSend: '.$email2Send.')');
//            }
//
//        } catch (SystemException $e) {
//
//            if (self::get('logging')) {
//                $logging = Log::Initial('LogFile');
//                $logging->setValue($e->getMessage().self::getSuffixError());
//                $logging->setWhereLogging('email-error');
//                $logging->push();
//            }
//
//        }
//
//    }
}