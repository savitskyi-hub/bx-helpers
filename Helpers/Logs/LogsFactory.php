<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers;

/**
 * Class Logs
 * @package SavitskyiHub\BxHelpers\Helpers\Logs
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс инициализирует тип логирования информации
 */
class Logs
{
	/**
	 *
	 * @var array
	 */
	private static $typeLog = ["File", "Mysql"];
	
	/**
	 * Logs constructor
	 * @param string $type - тип логирования;
	 */
	public function __construct(string $type) {
		//if (in_array($type, self::$typeLog)) {
			//return new ('\\'.__NAMESPACE__.'\\Logs\\'.$type)();
		//} else {
		//
		//}
	}
}