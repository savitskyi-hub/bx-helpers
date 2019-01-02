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

use Bitrix\Main\Diag\Debug as BxDebug;
use SavitskyiHub\BxHelpers\Helpers\Mail\Send;

/**
 * Class Debug
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для роботы с отладкой кода
 */
class Debug
{
	/**
	 * Путь к лог файлу
	 * @var string
	 */
	private static $filenameLog = 'local/logs/helpers-debug.log';

	/**
	 * Выполняет логирование цепочки вызовов при исключениях или произвольных вызовах
	 *
	 * @param string $message
	 */
	static function writeToFile(string $message) {
		$current = current(\debug_backtrace());
		$end = end(\debug_backtrace());
		$time = date("d.m.Y H:i:s");
		
		BxDebug::writeToFile([$time, $current, $end], "", self::$filenameLog);
		Send::Admin($message, "ERROR");
	}
}