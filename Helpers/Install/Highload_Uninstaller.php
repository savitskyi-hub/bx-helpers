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

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

/**
 * Class Highload_Uninstaller
 * @package SavitskyiHub\BxHelpers\Helpers\Install
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс родитель (не выполняется самостоятельно) для деинсталяции Highload-таблиц (не совсем, но своего рода Factory)
 */
class Highload_Uninstaller
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
	 * Идентификатор Highload-блока
	 * @var int
	 */
	private static $hdTableID = 0;
	
	/**
	 * Uninstall constructor
	 * @param string $prefixTableName
	 */
	public function __construct(string $prefixTableName) {
		try {
			
			Loader::includeModule("highloadblock");
			
			$hasUninstalled = self::uninstallHighloadTable($prefixTableName);
			
		} catch (SystemException $e) {
			echo $e->getMessage();
		} finally {
			
			if ($hasUninstalled) {
				echo "\r\n\r\nТаблица удалена успешно!";
			}
			
		}
	}
	
	/**
	 * Реализация деинсталляции Highload-блока в БД
	 *
	 * @param string $prefixTableName
	 * @return bool
	 */
	private static function uninstallHighloadTable(string $prefixTableName): bool {
		$tableName = $prefixTableName.self::$tableName;
		
		if (self::isInstallHighloadTable($tableName)) {
			$rsDelete = HighloadBlockTable::delete(self::$hdTableID);
			
			if ($rsDelete->isSuccess()) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Проверяет существует ли еще таблица в БД перед деинсталляцией
	 *
	 * @param string $tableName
	 * @return bool
	 */
	private static function isInstallHighloadTable(string $tableName): bool {
		$rsIsInstall = HighloadBlockTable::getList([
			"filter" => ["=TABLE_NAME" => $tableName],
			"select" => ["ID"],
			"limit" => 1
		]);
		
		if ($rsIsInstall && $rsIsInstall->getSelectedRowsCount()) {
			self::$hdTableID = $rsIsInstall->fetch()["ID"];
			
			return true;
		}
		
		return false;
	}
}