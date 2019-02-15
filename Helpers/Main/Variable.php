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
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;
use SavitskyiHub\BxHelpers\Helpers\IO\Dir;

/**
 * Class Variable
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для хранения вспомогательных свойств или методов для их обработки, которые будут нужны в реализации или
 * поддержки проекта (чтоб избавиться от дублирования кода при получении свойств)
 */
final class Variable
{
	use ClassTrait;
	
	/**
	 * Глобальные сущности ядра
	 */
	static $bxApplication;
	static $bxContext;
	static $bxServer;
	static $bxRequest;
	static $bxResponse;
	
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
	 * Список всех сайтов с их базовой информацией
	 * @var array
	 */
	static $bxSitesInfo = [];
	
	/**
	 * Singleton Instance
	 */
	private static $instance = null;
	
	/**
	 * Приватные параметры кеша
	 */
	private static $cacheTime = 600;
	private static $cacheDir = '_helpers_variable';
	
	/**
	 * Varaible constructor
	 */
	private function __construct() {
		try {
			self::$bxApplication = Application::getInstance();
			self::$bxContext = self::$bxApplication->getContext();
			self::$bxServer = self::$bxContext->getServer();
			self::$bxRequest = self::$bxContext->getRequest();
			self::$bxResponse = self::$bxContext->getResponse();
			
			Loader::includeModule("main");
			Loader::includeModule("iblock");
			
			$сache = Cache::createInstance();
			$cacheId = SITE_ID.'_'.LANGUAGE_ID;
			$cacheTime = self::$cacheTime;
			$cacheDir = Dir::getCacheDirectoryPrefixName().self::$cacheDir;
			
			if ($сache->initCache($cacheTime, $cacheId, $cacheDir)) {
				$arCacheVars = $сache->getVars();
				
				if ($arCacheVars["bxSitesInfo"]) {
					self::$bxSitesInfo = $arCacheVars["bxSitesInfo"];
				}
				
				if ($arCacheVars["bxEnumField"]) {
					self::$bxEnumField = $arCacheVars["bxEnumField"];
				}
				
				if ($arCacheVars["bxIbEnumProp"]) {
					self::$bxIbEnumProp = $arCacheVars["bxIbEnumProp"];
				}
			} elseif ($сache->startDataCache()) {
				self::$bxSitesInfo = self::getAllSitesInfo();
				self::$bxEnumField = self::getAllEnumFields(true, true, true);
				self::$bxIbEnumProp = self::getAllIbEnumProp(true, true, true);
				
				$сache->endDataCache([
					"bxSitesInfo" => self::$bxSitesInfo,
					"bxEnumField" => self::$bxEnumField,
					"bxIbEnumProp" => self::$bxIbEnumProp
				]);
			} else {
				throw new SystemException("Невозможно инициализировать работу кэширования: Varaible __construct");
			}
			
		} catch (SystemException $e) {
			Debug::writeToFile($e->getMessage());
		}
	}
	
	/**
	 * Объект создается внутри самого класса, только если у класса нет экземпляра
	 *
	 * @return null|Instance
	 */
	static function getInstance() {
		if (null == self::$instance) {
			self::$instance = new Variable();
		}
		
		return self::$instance;
	}
	
	/**
	 * Возвращает массив с информацией о всех сайтах системы
	 *
	 * @return array
	 */
	public static function getAllSitesInfo(): array {
		$arReturn = [];
		$rsSites = \CSite::GetList($by = "sort", $order = "desc", []);
		
		while ($arSite = $rsSites->Fetch()) {
			$arReturn[$arSite["LID"]] = $arSite;
		}
		
		return $arReturn;
	}
	
	/**
	 * Возвращает все пользовательские поля типа "список" из разных объектов
	 *
	 * @param bool $reverseXml2Val - сохраняет отдельным ключом в массиве результат значений, ключами массива будут XML_ID, а значением VALUE;
	 * @param bool $responseXml2Id - сохраняет отдельным ключом в массиве результат значений, ключами массива будут XML_ID, а значением ID;
	 * @param bool $reverseId2Xml - сохраняет отдельным ключом в массиве результат значений, ключами массива будут ID, а значением XML_ID;
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
	 * Возвращает массив индексами которого являются значение $key
	 *
	 * @param array $array
	 * @param string $key - ключ который будет вместо индексного номера в ключе массива;
	 * @param bool $group - если true, в качестве значений массива являются массивы с одинаковыми значениями $key;
	 * @return array
	 */
	public static function reverseKeyByID(array $array, $key = "ID", $group = false): array {
		$arReturn = [];
		
		foreach ($array as $index => $val) {
			if ($group) {
				$arReturn[$val[$key]][] = $val;
			} else {
				$arReturn[$val[$key]] = $val;
			}
		}
		
		return $arReturn;
	}
}