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

use Bitrix\Main\SystemException;
use SavitskyiHub\BxHelpers\Helpers\Main\Debug;

/**
 * Trait ClassTrait
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
trait ClassTrait
{
	public function __call($name, $arguments) {
		self::writeToLog('Method "'.$name.'()" was not found in - '.self::getCalledClassName());
	}
	
	public static function __callStatic($name, $arguments) {
		self::writeToLog('The static method "::'.$name.'()" was not found in - '.self::getCalledClassName());
	}
	
	public function __unset($name) {
		self::writeToLog('The property "'.$name.'" cannot be removed in - '.self::getCalledClassName());
	}
	
	public function __set($name, $value) {
		self::writeToLog('Can not set "'.$value.'" to property "'.$name.'" in'.self::getCalledClassName());
	}
	
	public function __clone() {
		self::writeToLog('Cloning "'.self::getCalledClassName().'" is not allowed');
	}
	
	public function __wakeup() {
		self::writeToLog('Unserializing "'.self::getCalledClassName().'" is not allowed');
	}
	
	public function __isset($name) {
		self::writeToLog('The property "'.$name.'" does not exist in - '.self::getCalledClassName());
	}
	
	public function __get($name) {
		try {
			if (!property_exists($this, $name)) {
				throw new SystemException('Неизвестная переменная: '.$name.' - '.self::getCalledClassName());
			}
			
			return $this->$name;
		} catch (SystemException $e) {
			self::writeToLog($e->getMessage());
		}
		
		return null;
	}
	
	/**
	 * Геттер для статических свойств
	 *
	 * @param string $name - название свойства;
	 * @return null|void
	 */
	static function getStaticProp(string $name) {
		try {
			if (!isset(self::${$name})) {
				throw new SystemException('Неизвестная переменная: '.$name.' - '.self::getCalledClassName());
			}
			
			return self::${$name};
		} catch (SystemException $e) {
			self::writeToLog($e->getMessage());
		}
		
		return null;
	}
	
	/**
	 * Метод возвращает название класса из которого реализовано вызов
	 *
	 * @return mixed
	 */
	static function getCalledClassName() {
		return get_called_class();
	}
	
	/**
	 * Сохраняет исключения в лог
	 *
	 * @param $message
	 */
	static function writeToLog($message) {
		$debug = new Debug();
		$debug->onBacktrace();
		$debug->writeData($message, false, true);
	}
}