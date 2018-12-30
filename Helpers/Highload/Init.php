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

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

/**
 * Class Init
 * @package SavitskyiHub\BxHelpers\Helpers\Highload
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предоставляет возможность работать с записями из Highload таблиц через одну инициализацию простым методом
 */
class Init
{
	/**
	 * Объект entityDataClass
	 * @var object
	 */
	public $entityDataClass;
	
	/**
	 * Название таблицы
	 * @var string
	 */
	public $tableName = "";
	
	/**
	 * Query constructor - Инициализируем объект сущности
	 *
	 * @param int $highloadBlockID - идентификатор Highload-блока;
	 */
	public function __construct(int $highloadBlockID) {
		Loader::includeModule("highloadblock");
		
		$hlBlock = HighloadBlockTable::getById($highloadBlockID)->fetch();
		$entityDataClass = HighloadBlockTable::compileEntity($hlBlock)->getDataClass();
		
		$this->entityDataClass = $entityDataClass;
		$this->tableName = $hlBlock["TABLE_NAME"];
		
		return $this;
	}
}