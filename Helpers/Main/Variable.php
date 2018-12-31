<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

/**
 * Class Variable
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для хранения вспомогательных свойств или методов для обработки, которые будут нужны в реализации или поддержки
 * проекта (чтоб избавиться от дублирования кода при получении свойств)
 */
final class Variable
{
	/**
	 * Приватные параметры кеша
	 */
	private static $cacheTime = 600;
	private static $cacheDir = '_helpers_variable';
	
	/**
	 * Глобальные сущности ядра
	 */
	static $bxApplication;
	static $bxContext;
	static $bxServer;
	static $bxRequest;
	
	/**
	 * Список всех пользовательских свойств типа "Список"
	 * @var array
	 */
	static $bxEnumField = [];
	
	/**
	 * Список всех свойств информационных блоков типа "Список"
	 * @var array
	 */
	static $bxIbEnumProp = [];
	
	/**
	 * Varaible constructor
	 */
	public function __construct() {
		
		try {
			
			self::$bxApplication = Application::getInstance();
			self::$bxContext = self::$bxApplication->getContext();
			self::$bxServer = self::$bxContext->getServer();
			self::$bxRequest = self::$bxContext->getRequest();
			
			Loader::includeModule("iblock");
			
			$сache = Cache::createInstance();
			$cacheId = SITE_ID.''.LANGUAGE_ID;
			$cacheTime = self::$cacheTime;
			$cacheDir = Base::getCacheDirectoryPrefixName().self::$cacheDir;
			
			if ($сache->initCache($cacheTime, $cacheId, $cacheDir)) {
				$arCacheVars = $сache->getVars();
				
				if ($arCacheVars["bxEnumField"]) {
					self::$bxEnumField = $arCacheVars["bxEnumField"];
				}
				
				if ($arCacheVars["bxEnumProp"]) {
					self::$bxIbEnumProp = $arCacheVars["bxEnumProp"];
				}
			} elseif ($сache->startDataCache()) {
				
				self::$bxEnumField = self::getAllEnumFields(true, true, true);
				self::$bxIbEnumProp = self::getAllIbEnumProp(true, true, true);
				
				if (!self::$bxIbEnumProp && !self::$bxIbEnumProp) {
					$сache->abortDataCache();
				}
				
				$сache->endDataCache(["bxEnumField" => self::$bxEnumField, "bxEnumProp" => self::$bxIbEnumProp]);
				
			} else {
				
				$caller = \debug_backtrace()[0];
				$caller["message"] = $e->getMessage();
				
				Debug::dumpToFile($caller);
				//throw new SystemException("");
				
				// Залогировать!!!
				
			}
			
		} catch (SystemException $e) {
//			$caller = \debug_backtrace()[0];
//			$caller["message"] = $e->getMessage();
//
//			Debug::dumpToFile($caller);
		}
		
	}
	
	/**
	 * Метод возвращает все пользовательские поля типа "список" из разных объектов
	 *
	 * @param bool $reverseXml2Val - сохраняет отдельным ключом в массиве результат значений, ключами массива будут XML_ID, а значением VALUE;
	 * @param bool $responseXml2Id - сохраняет отдельным ключом в массиве результат значений, ключами массива будут XML_ID, а значением ID;
	 * @param bool $reverseId2Xml - сохраняет отдельным ключом в массиве результат значений, ключами массива будут ID, а значением XML_ID;
	 *
	 * @return array
	 */
	private static function getAllEnumFields(bool $reverseXml2Val = false, bool $responseXml2Id = false, bool $reverseId2Xml = false): array {
		$rsTypeField = \CUserTypeEntity::GetList([], []);
		$arTypeField = ($rsTypeField? $rsTypeField->arResult : []);
		
		$rsEnums = \CUserFieldEnum::GetList([], []);
		$arEnums = ($rsEnums? $rsEnums->arResult : []);
		
		if ($arTypeField && $arEnums) {
			$arReturn = [];
			
			foreach ($arTypeField as $k => $field) {
				foreach ($arEnums as $j => $enum) {
					$objectCode = $field["ENTITY_ID"].'_'.$field["FIELD_NAME"];
					
					if ($field["ID"] == $enum["USER_FIELD_ID"]) {
						$arReturn["LIST"][$objectCode][$enum["ID"]] = $enum["VALUE"];
						
						if ($reverseXml2Val && $enum["XML_ID"]) {
							$arReturn["XML2VAL"][$objectCode][$enum["XML_ID"]] = $enum["VALUE"];
						}
						
						if ($responseXml2Id && $enum["XML_ID"]) {
							$arReturn["XML2ID"][$objectCode][$enum["XML_ID"]] = $enum["ID"];
						}
						
						if ($reverseId2Xml) {
							$arReturn["ID2XML"][$objectCode][$enum["ID"]] = $enum["XML_ID"];
						}
					}
				}
			}
		}
		
		return $arReturn ?? [];
	}
	
	/**
	 * Метод возвращает все свойства типа "список" из разных информационных блоков
	 *
	 * @param bool $reverseXml2Val - сохраняет отдельным ключом в массиве результат значений, ключами массива будут XML_ID, а значением VALUE;
	 * @param bool $responseXml2Id - сохраняет отдельным ключом в массиве результат значений, ключами массива будут XML_ID, а значением ID;
	 * @param bool $reverseId2Xml - сохраняет отдельным ключом в массиве результат значений, ключами массива будут ID, а значением XML_ID;
	 *
	 * @return array
	 */
	private static function getAllIbEnumProp(bool $reverseXml2Val = false, bool $responseXml2Id = false, bool $reverseId2Xml = false): array {
		$arIbList = [];
		$rsIbList = \CIBlock::GetList(["SORT" => "ASC"], ["CHECK_PERMISSIONS" => "N"]);
		
		while ($arIb = $rsIbList->Fetch()) {
			$arIbList[] = $arIb["ID"];
		}
		
		$arEnumList = [];
		$rsEnumList = \CIBlockProperty::GetList([], ["PROPERTY_TYPE" => "L"]);
		
		while ($arEnum = $rsEnumList->Fetch()) {
			$arEnumList[] = $arEnum;
		}
		
		if ($arIbList && $arEnumList) {
			$arReturn = [];
			$arEnumFieldList = [];
			
			foreach ($arIbList as $ibID) {
				$rsPropEnums = \CIBlockPropertyEnum::GetList(["SORT" => "ASC"], ["IBLOCK_ID" => $ibID]);
				
				while ($arEnumField = $rsPropEnums->GetNext()) {
					$arEnumFieldList[] = $arEnumField;
				}
			}
			
			foreach ($arEnumList as $k => $arEnum) {
				foreach ($arEnumFieldList as $j => $arEnumField) {
					$ibEnumCode = "IBLOCK_".$arEnum["IBLOCK_ID"].'_'.$arEnum["CODE"];
					
					if ($arEnum["ID"] == $arEnumField["PROPERTY_ID"]) {
						$arReturn["LIST"][$ibEnumCode][$arEnumField["ID"]] = $arEnumField["VALUE"];
						
						if ($reverseXml2Val && $arEnumField["XML_ID"]) {
							$arReturn["XML2VAL"][$ibEnumCode][$arEnumField["XML_ID"]] = $arEnumField["VALUE"];
						}
						
						if ($responseXml2Id && $arEnumField["XML_ID"]) {
							$arReturn["XML2ID"][$ibEnumCode][$arEnumField["XML_ID"]] = $arEnumField["ID"];
						}
						
						if ($reverseId2Xml) {
							$arReturn["ID2XML"][$ibEnumCode][$arEnumField["ID"]] = $arEnumField["XML_ID"];
						}
					}
				}
			}
		}
		
		return $arReturn ?? [];
	}
	
	
	/**
	 * Метод возвращает массив индексами которого являются значение $key
	 * - если $group == true, в качестве значений массива являются массиві с одинаковыми значениями $key
	 */
	//public static function reverseKeyByID($array, $key = "ID", $group = false): array {
	//	$arReturn = [];
	//
	//	foreach ($array as $index => $val) {
	//		if (!is_array($val)) {
	//			$arKey = $val->key;
	//		} else {
	//			$arKey = $val[$key];
	//		}
	//
	//		if ($group) {
	//			$arReturn[$arKey][] = $val;
	//		} else {
	//			$arReturn[$arKey] = $val;
	//		}
	//	}
	//
	//	return $arReturn;
	//}
	
	//	/**
	//	 * Метод проверяет существует(ют) ли XML значение(я) в пользовательськом поле типа "список".
	//	 * @param $checkName
	//	 * @param $typeEnumName
	//	 * @return bool
	//	 * @throws SystemException
	//	 */
	//	static function isExistXmlKeyInEnumField($checkName, $typeEnumName) {
	//
	//		try {
	//
	//			$is_success = true;
	//			$arEnumList = self::$bxEnumFields["XML2ID"];
	//
	//			if (!isset($arEnumList[$typeEnumName])) {
	//				throw new SystemException("Пользовательськое поле с названием &ldquo;".$typeEnumName."&rdquo; не существует");
	//			} elseif (is_array($checkName)) {
	//
	//				$listEnum = $arEnumList[$typeEnumName];
	//
	//				foreach ($checkName as $v) {
	//					if (!array_key_exists($v, $listEnum)) {
	//						$is_success = false;
	//						break;
	//					}
	//				}
	//
	//			} else {
	//
	//				if (!array_key_exists($checkName, $arEnumList[$typeEnumName])) {
	//					$is_success = false;
	//				}
	//
	//			}
	//
	//			return $is_success;
	//
	//		} catch (SystemException $e) {
	//
	//			if (self::get('exceptionGlobal')) {
	//				Variable::set('error', $e->getMessage().self::getSuffixError());
	//			} else {
	//				throw $e;
	//			}
	//
	//		}
	//
	//	}
	//
	//	/**
	//	 * Метод проверяет существует(ют) ли ID значение(я) в пользовательськом поле типа "список".
	//	 * @param $checkName
	//	 * @param $typeEnumName
	//	 * @return bool
	//	 * @throws SystemException
	//	 */
	//	static function isExistIdValueInEnumField($checkName, $typeEnumName) {
	//
	//		try {
	//
	//			$is_success = true;
	//			$arEnumList = self::$bxEnumFields["XML2ID"];
	//
	//			if (!isset($arEnumList[$typeEnumName])) {
	//				throw new SystemException("Пользовательськое поле с названием &ldquo;".$typeEnumName."&rdquo; не существует");
	//			} elseif (is_array($checkName)) {
	//
	//				$listEnum = $arEnumList[$typeEnumName];
	//
	//				foreach ($checkName as $v) {
	//					if (!in_array($v, $listEnum)) {
	//						$is_success = false;
	//						break;
	//					}
	//				}
	//
	//			} else {
	//
	//				if (!in_array($checkName, $arEnumList[$typeEnumName])) {
	//					$is_success = false;
	//				}
	//
	//			}
	//
	//			return $is_success;
	//
	//		} catch (SystemException $e) {
	//
	//			if (self::get('exceptionGlobal')) {
	//				Variable::set('error', $e->getMessage().self::getSuffixError());
	//			} else {
	//				throw $e;
	//			}
	//
	//		}
	//
	//	}
}