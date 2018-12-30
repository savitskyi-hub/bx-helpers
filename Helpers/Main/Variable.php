<?php

namespace SavitskyiHub\BxHelpers\Helpers\Main;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;

/**
 * Class Variable
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 *
 * Класс предназначен для хранения вспомогательных свойств, которые будут нужны в реализации или поддержки проекта (чтоб избавиться от дублирования кода при получения необходимых свойств)
 */
final class Variable
{
	private static $cacheTime = 600;
	private static $cacheDir = 'helpers_variable';
	
	
	static $bxApplication;
	static $bxContext;
	static $bxServer;
	static $bxRequest;
	
	
	/**
	 * !!!!!!!!!!!!!!! хранит информацию об всех пользовательских полях;
	 * @var array
	 */
	private static $bxEnum = [];
	
	/**
	 * !!!!!!!!!!!!!
	 * @var array
	 */
	private static $bxProp = [];
	
	/**
	 * !!!!!!!!!!!!!
	 * @var array
	 */
	private static $bxOption = [];


	/**
	 * Varaible constructor
	 */
	public function __construct() {
		
		try {
			
			self::$bxApplication = Application::getInstance();
			self::$bxContext = self::$bxApplication->getContext();
			self::$bxServer = self::$bxContext->getServer();
			self::$bxRequest = self::$bxContext->getRequest();
			
			
			
//			$сache = Cache::createInstance();
//			$cache_id = '';
//			$cache_time = 36000000;
//			$cache_dir = '/cccstore_geo_search_ajax';
			
//			$cache_id .= $site_id;
//			$cache_id .= $language_type;
//			$cache_id .= $search;
//			$cache_id .= $maxViewItem.'_'.$numPage;
			
//			if ($сache->initCache($cache_time, $cache_id, $cache_dir)) {
//				//$arLocationResult = $сache->getVars();
//			} elseif ($сache->startDataCache()) {
//
//					//$сache->abortDataCache();
//
//				//$сache->endDataCache($arLocationResult);
//			} else {
//				throw new SystemException("Ошибка в работе компонента");
//			}
			
			// !!!!!! cache
//			self::$bxEnum = self::getAllEnumFields(true, true, true);
			
			//https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockproperty/getpropertyenum.php
			//			self::$bxIbEnum =
			
			
//			echo '<pre>';
//			print_r(self::$bxEnum);
//			echo '</pre>';
			
		} catch (SystemException $e) {
			echo $e->getMessage();
		}
		
	}
	
	//	/**
	//	 * Метод для получения всех пользовательских полей разных объектов.
	//	 * @param bool $responseXml2Id - сохраняет отдельным ключом в массиве результат значений, но ключами массива будут XML_ID, а значением ID;
	//	 * @param bool $reverseId2Xml - сохраняет отдельным ключом в массиве результат значений, но ключами массива будут ID, а значением XML_ID;
	//	 * @return array
	//	 */
	private static function getAllFieldsEnum($reverseXml2Val = false, $responseXml2Id = false, $reverseId2Xml = false) {
		$rsTypeField = \CUserTypeEntity::GetList([], []);
		$arTypeField = ($rsTypeField? $rsTypeField->arResult : []);
		
		$rsEnums = \CUserFieldEnum::GetList([], []);
		$arEnums = ($rsEnums? $rsEnums->arResult : []);
		
		if ($arTypeField && $arEnums) {
			$arReturn = [];
			
			foreach ($arTypeField as $k => $field) {
				foreach ($arEnums as $j => $enum) {
					if ($field["ID"] == $enum["USER_FIELD_ID"]) {
						$arReturn["LIST"][$field["ENTITY_ID"].'_'.$field["FIELD_NAME"]][$enum["ID"]] = $enum["VALUE"];
					}
				}
			}
			
			if ($reverseXml2Val) {
				foreach ($arTypeField as $k => $field) {
					foreach ($arEnums as $j => $enum) {
						if ($field["ID"] == $enum["USER_FIELD_ID"] && $enum["XML_ID"]) {
							$arReturn["XML2VAL"][$field["ENTITY_ID"].'_'.$field["FIELD_NAME"]][$enum["XML_ID"]] = $enum["VALUE"];
						}
					}
				}
			}
			
			if ($responseXml2Id) {
				foreach ($arTypeField as $k => $field) {
					foreach ($arEnums as $j => $enum) {
						if ($field["ID"] == $enum["USER_FIELD_ID"] && $enum["XML_ID"]) {
							$arReturn["XML2ID"][$field["ENTITY_ID"].'_'.$field["FIELD_NAME"]][$enum["XML_ID"]] = $enum["ID"];
						}
					}
				}
			}
			
			if ($reverseId2Xml) {
				foreach ($arTypeField as $k => $field) {
					foreach ($arEnums as $j => $enum) {
						if ($field["ID"] == $enum["USER_FIELD_ID"] && $enum["XML_ID"]) {
							$arReturn["ID2XML"][$field["ENTITY_ID"].'_'.$field["FIELD_NAME"]][$enum["ID"]] = $enum["XML_ID"];
						}
					}
				}
			}
		}
		
		return $arReturn ?? [];
	}
	//
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