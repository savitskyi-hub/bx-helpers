<?php

namespace Local\Helpers\Highload;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

/**
 * Class Init
 * @package Local\Helpers\Highload
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
	 * @param int $highloadBlockID - идентификатор Highload-блока
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