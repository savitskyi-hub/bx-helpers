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
	 * @param bool $sendAdmin
	 * @param string $codeMail2SendAdmin
	 */
	static function writeToFile(string $message, bool $sendAdmin = true, string $codeMail2SendAdmin = 'MAIN') {
		$backtrace = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 20);
		$current = current($backtrace);
		$end = end($backtrace);
		$time = date("d.m.Y H:i:s");
		
		BxDebug::writeToFile([$time, $current, $end], "", self::$filenameLog);
		
		if ($sendAdmin) {
			Send::Admin($message, "ERROR", $codeMail2SendAdmin);
		}
	}
}