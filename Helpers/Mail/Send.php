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
use Bitrix\Main\Mail\Event;
use Bitrix\Main\SystemException;
use SavitskyiHub\BxHelpers\Helpers\Highload\Instance;
use SavitskyiHub\BxHelpers\Helpers\Install\Mail_Install_Highload;
use SavitskyiHub\BxHelpers\Helpers\Main\Debug;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class Send
 * @package SavitskyiHub\BxHelpers\Helpers\Mail
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для роботы с отправкой електронных писем штатными методами и логированием ошибки в случаи неудачи
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
	private static $limitTypeSend = 2;
	
	/**
	 * Реализует отправку письма администрации сайта в случаи возникновения ошибки или предупреждения в функционале проекта
	 *
	 * @param string $message
	 * @param string $typeReporting - тип оповещения;
	 * @param string $code
	 * @param bool $skip - пропустить проверку на повторною отправку;
	 * @return bool
	 */
	static function Admin(string $message, string $typeReporting, string $code, bool $skip = false): bool {
		try {
			Loader::includeModule('main');
			
			$mailEventType = Mail_Install_Highload::getStaticProp("mailEventType");
			$entityName = Mail_Install_Highload::getStaticProp("name");
			$entityID = Instance::getIdByEntityName($entityName);
			
			$datetimeCreate = date("d.m.Y H:i:s");
			$enumCode = "HLBLOCK_".$entityID."_UF_TYPE_SEND";
			$enumCode2 = "HLBLOCK_".$entityID."_UF_TYPE_STATUS";
			
			Variable::getInstance();
			
			$messReporting = Variable::$bxEnumField["XML2VAL"][$enumCode][$typeReporting];
			$typeReporting = Variable::$bxEnumField["XML2ID"][$enumCode][$typeReporting];
			$typeStatus = Variable::$bxEnumField["XML2ID"][$enumCode2]["NOT_PROCESSED"];
			
			if (!$typeReporting) {
				throw new SystemException('Неверный символьный код значения в объекте: '.$enumCode.' - '.$typeReporting);
			}
			
			if (!$skip && !self::checkLimitSendAdmin($typeReporting, $code, $entityID)) {
				return false;
			}
			
			$rsSend = Event::send([
				"EVENT_NAME" => $mailEventType,
				"LID" => (SITE_ID != "ru"? SITE_ID : key(Variable::$bxSitesInfo)),
				"C_FIELDS" => [
					"TYPE_SEND" => $messReporting,
					"DATETIME_SEND" => $datetimeCreate,
					"TEXT_MESSAGE" => $message
				]
			]);
			
			/**
			 * Логируем отправку
			 */
			if (!$rsSend->isSuccess()) {
				throw new SystemException($rsSend->getErrors());
			} else {
				$rsAdd = new Instance($entityID);
				$reAdd = $rsAdd->entityDataClass::add([
					"UF_TYPE_SEND" => $typeReporting,
					"UF_TYPE_STATUS" => $typeStatus,
					"UF_CODE" => $code,
					"UF_TEXT" => $message,
					"UF_DATETIME_CREATE" => $datetimeCreate
				]);
				
				if (!$reAdd->isSuccess()) {
					throw new SystemException('Произошла ошибка при добавлении записи в Highload-блок - '.$entityName);
				}
				
				$isSuccess = true;
			}
		} catch (SystemException $e) {
			Debug::writeToFile($e->getMessage(), false);
		}
		
		return $isSuccess ?? false;
	}
	
	/**
	 * Метод проверяет доступно ли отправлять "повторно" администрации письма
	 *
	 * @param string $typeReporting - тип оповещения;
	 * @param string $code
	 * @param int $entityID
	 *
	 * @return bool
	 */
	private static function checkLimitSendAdmin(string $typeReporting, string $code, int $entityID): bool {
		$obHd = new Instance($entityID);
		$rsHistory = $obHd->entityDataClass::getList([
			"select" => ['ID'],
			"filter" => [
				"=UF_TYPE_SEND" => $typeReporting,
				"=UF_CODE" => $code,
				">=UF_DATETIME_CREATE" => [ConvertTimeStamp(time() - 3600 * self::$periodBlocked, "FULL")]
			]
		]);
		
		if ($rsHistory && ($rsHistory->getSelectedRowsCount() >= self::$limitTypeSend)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Метод реализует отправку почтового события штатным методом и в случаи ошибки происходить логирования
	 *
	 * @param string $typeEmailEvent - символьный код почтового события;
	 * @param array $arFields - массив параметров события;
	 * @return bool
	 */
	static function Mail(string $typeEmailEvent, array $arFields): bool {
		try {
			Loader::includeModule('main');
			
			if (!$typeEmailEvent || !$arFields) {
				throw new SystemException('Передано не все данные для отправки почтового события');
			}
			
			$rsMail = Event::send([
				"EVENT_NAME" => $typeEmailEvent,
				"C_FIELDS" => $arFields,
				"LID" => (SITE_ID != "ru"? SITE_ID : key(Variable::$bxSitesInfo))
			]);
			
			if (!$rsMail->isSuccess()) {
				throw new SystemException('Ошибка отправки письма: '.implode("\r\n", $rsMail->getErrorMessages()));
			}
			
			return true;
		} catch (SystemException $e) {
			Debug::writeToFile($e->getMessage(), false);
			
			return false;
		}
	}
}