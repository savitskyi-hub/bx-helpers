<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Logs;

/**
 * Class LogsFactory
 * @package SavitskyiHub\BxHelpers\Helpers\Logs
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс инициализирует тип логирования информации
 */
class LogsFactory
{
	/**
	 * Варианты куда будет происходить логирование
	 * @var array
	 */
	private static $typeLog = ["File", "Mysql"];
	
	/**
	 * @param string $class - тип логирования;
	 * @return mixed
	 */
	public static function getInstance(string $class) {
		if (in_array($class, self::$typeLog)) {
			$nameSpace = '\\'.__NAMESPACE__.'\\'.$class.'\\Logging';
		}
		
		if (isset($nameSpace)) {
			return new $nameSpace;
		}
	}
}