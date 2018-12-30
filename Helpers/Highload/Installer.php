<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Highload;

use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

/**
 * Class Installer
 * @package SavitskyiHub\BxHelpers\Helpers\Highload
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс родитель (не выполняется самостоятельно) для инсталяции Highload-таблиц (не совсем, но своего рода Factory)
 */
class Installer
{
	/**
	 * Название сущности
	 * @var string
	 */
	protected static $name = '';
	
	/**
	 * Название таблицы в БД
	 * @var string
	 */
	protected static $tableName = '';
	
	/**
	 * Языкозависимые названия
	 * @var string
	 */
	protected static $langNameRU = '';
	protected static $langNameEN = '';
	
	/**
	 * Карта пользовательских свойств что должны быть созданы
	 * @var array
	 */
	protected static $mapCreatedField = [];
	
	/**
	 * Идентификатор таблицы, который будет доступен после успешного создания
	 * @var int
	 */
	private static $tableID = 0;
	
	/**
	 * Дефолтные настройки для типа пользовательского свойства
	 * @var array
	 */
	private static $defaultSettingsType = [
		"integer" => [
			"DEFAULT_VALUE" => "",
			"SIZE" => "40", // Размер поля ввода для отображения:
			"MIN_VALUE" => "", // Минимальное значение (0 - не проверять)
			"MAX_VALUE" => "", // Максимальное значение (0 - не проверять)
		],
		"string" => [
			'DEFAULT_VALUE' => '',
			'SIZE' => '40', // Размер поля ввода для отображения
			'ROWS' => '1', // Количество строчек поля ввода
			'MIN_LENGTH' => '0', // Минимальная длина строки (0 - не проверять)
			'MAX_LENGTH' => '0', // Максимальная длина строки (0 - не проверять)
			'REGEXP' => '', // Регулярное выражение для проверки
		],
		"datetime" => [
			'DEFAULT_VALUE' => [
				'TYPE' => 'NONE', // Тип значения по умолчанию (NONE - нет | NOW - текущее время | FIXED - установить дефолтное)
				'VALUE' => '' // Если тип равен FIXED
			],
			'USE_SECOND' => '' // Использовать секунды (Y | N)
		],
		"enumeration" => [
			"DISPLAY" => "LIST", // Внешний вид (LIST - Список | CHECKBOX - Флажки | UI - Набираемый список)
			"LIST_HEIGHT" => "5", // Высота списка
			"CAPTION_NO_VALUE" => "", // Подпись при отсутствии значения
			"SHOW_NO_VALUE" => "Y" // Показывать пустое значение для обязательного поля (Y | N)
		],
		"boolean" => [
			"DEFAULT_VALUE" => 1, // Да - 1 | Нет - 0
			"DISPLAY" => "CHECKBOX", // Внешний вид (CHECKBOX - Флажок | RADIO - Радио кнопки | DROPDOWN - Выпадающий список)
			"LABEL_CHECKBOX" => "Да", // Подпись флажка
			"LABEL" => [
				0 => "Нет",
				1 => "Да"
			]
		]
	];
	
	/**
	 * Дефолтные настройки для пользовательского свойства
	 * @var array
	 */
	private static $defaultSettingsField = [
		'ENTITY_ID' => 'HLBLOCK_', // Идентификатор сущности, к которой будет привязано свойство
		'FIELD_NAME' => '', // Код поля (всегда должно начинаться с UF_)
		'USER_TYPE_ID' => 'string', // Указываем, что тип нового пользовательского свойства строка
		
		'XML_ID' => '', // XML_ID пользовательского свойства
		'SORT' => 100, // Сортировка
		'MULTIPLE' => 'N', // Является поле множественным или нет
		'MANDATORY' => 'N', // Обязательное или нет свойство
		'SHOW_FILTER' => 'N', // Показывать в фильтре списка (N - нет | I - точное совпадение | E - по маске | S - по подстроке)
		'SHOW_IN_LIST' => '', // Не показывать в списке (если пусто)
		'EDIT_IN_LIST' => '', // Разрешить редактировать (пустая строка разрешает)
		'IS_SEARCHABLE' => 'N', // Значения поля участвуют в поиске
		
		// Подписи в форме редактирования
		'EDIT_FORM_LABEL' => ['ru' => 'Название RU', 'en' => 'Название EN'],
		'LIST_COLUMN_LABEL' => ['ru' => '', 'en' => ''],
		'LIST_FILTER_LABEL' => ['ru' => '', 'en' => ''],
		'ERROR_MESSAGE' => ['ru' => 'Ошибка при заполнении свойства', 'en' => 'An error in completing the field'],
		'HELP_MESSAGE' => ['ru' => '', 'en' => ''],
		
		// Дополнительные настройки поля (зависят от типа)
		'SETTINGS' => [],
	];
	
	/**
	 * Install constructor
	 * @param string $prefixTableName
	 */
	public function __construct(string $prefixTableName) {
		try {
			
			Loader::includeModule("highloadblock");
			
			if ($hasInstalled = self::installHighloadTable($prefixTableName)) {
				$hasInstalledLangName = self::installHighloadTableLangName();
				$hasInstalledFields = self::installHighloadFields();
			} else {
				throw new SystemException('Ошибка при создании таблицы');
			}
			
		} catch (SystemException $e) {
			echo $e->getMessage();
		} finally {
			
			if ($hasInstalled) {
				echo "\r\n\r\nТаблица успешно создана!";
			}
			
			if ($hasInstalledLangName) {
				echo "\r\n\r\nЯзыковые названия для таблицы успешно заданы!";
			}
			
			if ($hasInstalledFields) {
				echo "\r\n\r\nСвойства таблицы успешно созданы";
			}
			
		}
	}
	
	/**
	 * Метод инициализирует Highload-блок в БД с необходимыми свойствами которые нужны для работы функционала этого класса
	 *
	 * @param string $prefixTableName
	 *
	 * @return bool
	 */
	private static function installHighloadTable(string $prefixTableName): bool {
		$tableName = $prefixTableName.self::$tableName;
		
		if (!self::isInstallHighloadTable($tableName)) {
			$rsAdd = HighloadBlockTable::add([
				'NAME' => self::$name,
				'TABLE_NAME' => $tableName
			]);
			
			if ($rsAdd->isSuccess()) {
				self::$tableID = $rsAdd->getId();
				
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	/**
	 * Метод проверяет существует ли уже таблица в БД перед инсталляцией
	 *
	 * @param string $tableName
	 *
	 * @return bool
	 */
	private static function isInstallHighloadTable(string $tableName): bool {
		$rsIsInstall = HighloadBlockTable::getList([
			"filter" => ["TABLE_NAME" => $tableName],
			"select" => ["ID"],
			"limit" => 1
		]);
		
		if ($rsIsInstall && $rsIsInstall->getSelectedRowsCount()) {
			self::$tableID = $rsIsInstall->fetch()["ID"];
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Метод добавляет языкозависимые названия к Highload-блоку
	 *
	 * @return bool
	 */
	private static function installHighloadTableLangName(): bool {
		if (!self::isInstalledHighloadTableLangName()) {
			$rsLangRu = HighloadBlockLangTable::add(["ID" => self::$tableID, "LID" => "ru", "NAME" => self::$langNameRU]);
			$rsLangEn = HighloadBlockLangTable::add(["ID" => self::$tableID, "LID" => "en", "NAME" => self::$langNameEN]);
			
			if ($rsLangRu->isSuccess() && $rsLangEn->isSuccess()) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	/**
	 * Метод проверяет существуют ли языкозависимые названия
	 *
	 * @return bool
	 */
	private static function isInstalledHighloadTableLangName(): bool {
		$rsIsLangName = HighloadBlockLangTable::getList([
			"filter" => ['ID' => self::$tableID],
			"select" => ["ID"],
			"limit" => 1
		]);
		
		if ($rsIsLangName && $rsIsLangName->getSelectedRowsCount()) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Метод устанавливает пользовательские поля к Highload-блоку
	 *
	 * @throws SystemException
	 *
	 * @return bool
	 */
	private static function installHighloadFields(): bool {
		
		if (!self::$mapCreatedField) {
			throw new SystemException('Параметры для создания пользовательських свойств отсутствуют');
		}
		
		$by = $order = '';
		$arCreatedFieldList = [];
		$j = 1;
		
		foreach (self::$mapCreatedField as $uf_name => $arSettings) {
			/**
			 * - проверяем чтобы код поля начинался с UF_
			 * - тип поля должен совпадать с одним из ключей массива $defaultSettingsType
			 */
			if (
				(substr($uf_name, 0, 3) != "UF_" || mb_strlen($uf_name) <= 3) ||
				!array_key_exists($arSettings["USER_TYPE_ID"], self::$defaultSettingsType)
			) {
				throw new SystemException('В параметрах пользовательських свойств присутствуют ошибки');
			}
			
			/**
			 * Склеиваем настройки перебивая дефолтные
			 */
			$arTmpSettings = array_merge(self::$defaultSettingsField, $arSettings);
			$arTmpSettings["SETTINGS"] = array_merge(self::$defaultSettingsType[$arSettings["USER_TYPE_ID"]], $arSettings["SETTINGS"] ?? []);
			
			$arTmpSettings["FIELD_NAME"] = $uf_name;
			$arTmpSettings["ENTITY_ID"] .= self::$tableID;
			$arTmpSettings["SORT"] = $j++ * 100;
			
			/**
			 * Дописываем автоматом рутинную работу
			 */
			if (!$arTmpSettings["LIST_COLUMN_LABEL"]["ru"]) {
				$arTmpSettings["LIST_COLUMN_LABEL"] = $arTmpSettings["EDIT_FORM_LABEL"];
			}
			
			if (!$arTmpSettings["LIST_FILTER_LABEL"]["ru"]) {
				$arTmpSettings["LIST_FILTER_LABEL"] = $arTmpSettings["EDIT_FORM_LABEL"];
			}
			
			foreach ($arTmpSettings["HELP_MESSAGE"] as $l => $v) {
				$arTmpSettings["HELP_MESSAGE"][$l] .= ($v? ' | ' : '').$uf_name;
			}
			
			$arCreatedList[] = $arTmpSettings;
		}
		
		/**
		 * Получаем список всех пользовательских свойств текущего Highload-блока
		 */
		$rsUserTypeEntity = \CUserTypeEntity::GetList([$by => $order], ["ENTITY_ID" => end($arCreatedList)["ENTITY_ID"]]);
		while ($userTypeEntity = $rsUserTypeEntity->Fetch()) {
			$arCreatedFieldList[] = $userTypeEntity["FIELD_NAME"];
		}
		
		/**
		 * Добавление пользовательских свойств
		 */
		$oUserTypeEntity = new \CUserTypeEntity();
		$obEnum = new \CUserFieldEnum();
		
		foreach ($arCreatedList as $arNewUserField) {
			if (!in_array($arNewUserField["FIELD_NAME"], $arCreatedFieldList)) {
				if ($fieldId = $oUserTypeEntity->Add($arNewUserField)) {
					echo "\r\nВ таблицу добавлено пользовательское поле: ".$arNewUserField["FIELD_NAME"];
					
					if ($arNewUserField["USER_TYPE_ID"] == "enumeration" && $arNewUserField["ENUM_LIST"]) {
						$obEnum->SetEnumValues($fieldId, $arNewUserField["ENUM_LIST"]);
					}
					
					$isSuccess = true;
				}
			}
		}
		
		return $isSuccess ?? false;
	}
}