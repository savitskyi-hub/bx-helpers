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
use Bitrix\Main\IO\File;
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
	private $filenameLog = '';
	
	/**
	 * Производить ли отладку
	 * @var bool
	 */
	private $backtrace = false;
	
	/**
	 * Debug constructor
	 * @param string $fileNameLog
	 */
	public function __construct(string $fileNameLog = '/local/logs/helpers-debug.log') {
		$obFile = new File(Application::getDocumentRoot().$fileNameLog);
		
		if ($obFile->isExists() && $obFile->isFile()) {
			$this->filenameLog = $obFile->getPath();
		}
	}
	
	/**
	 * Включить отладку
	 */
	public function onBacktrace() {
		$this->backtrace = true;
	}
	
	/**
	 * Выключить отладку
	 */
	public function offBacktrace() {
		$this->backtrace = false;
	}
	
	/**
	 * Добавляет строку в лог файл для визуального отделения (можно производить очистку логов по времени)
	 *
	 * @param string $yourOtherHeader
	 */
	public function writeNewEvent(string $yourOtherHeader = "-") {
		if (!$this->filenameLog) {
			return;
		} elseif ($this->backtrace) {
			return;
		}
		
		file_put_contents($this->filenameLog, "\r\n\r\n----- ".date('d.m.Y H:i:s')." | ".$yourOtherHeader." -----\r\n", FILE_APPEND);
	}
	
	/**
	 * Выполняет основное логирование, при включенной отладке, пишет цепочки вызовов при исключениях или произвольных вызовах
	 *
	 * @param $data
	 * @param bool $isChain - чтобы не дописывать следующее действие с новой строки
	 * @param bool $sendAdmin
	 * @param string $codeMail2SendAdmin
	 */
	public function writeData($data, bool $isChain = false, bool $sendAdmin = false, string $codeMail2SendAdmin = 'MAIN') {
		if (!$this->filenameLog) {
			return;
		}
		
		$isArrayData = is_array($data);
		$tmpData = $isArrayData? print_r($data, true) : $data;
		
		if ($this->backtrace) {
			$backtrace = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 20);
			$tmpData = print_r([
				"startReadScript" => current($backtrace),
				"endReadScript" => end($backtrace)
			], true);
			
			file_put_contents($this->filenameLog, "\r\n\r\n----- ".date('d.m.Y H:i:s')." | backtrace -----\r\n", FILE_APPEND);
			file_put_contents($this->filenameLog, $tmpData, FILE_APPEND);
		} else {
			if (!$isArrayData && !$isChain) {
				$tmpData = "\r\n".$tmpData;
			} elseif (!$isArrayData && $isChain) {
				$tmpData .= " ";
			}
			
			file_put_contents($this->filenameLog, $tmpData, FILE_APPEND);
		}
		
		if ($sendAdmin) {
			Send::Admin($tmpData, "ERROR", $codeMail2SendAdmin);
		}
	}
}