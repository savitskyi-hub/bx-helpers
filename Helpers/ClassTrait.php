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
		Debug::writeToFile('Method &ldquo;'.$name.'()&rdquo; was not found in - '.self::getCalledClassName());
    }

    public static function __callStatic($name, $arguments) {
		Debug::writeToFile('The static method &ldquo;::'.$name.'()&rdquo; was not found in - '.self::getCalledClassName());
    }

    public function __get($name) {
		Debug::writeToFile('The property &ldquo;'.$name.'&rdquo; does not exist in - '.self::getCalledClassName());
    }

    public function __isset($name) {
		Debug::writeToFile('The property &ldquo;'.$name.'&rdquo; does not exist in - '.self::getCalledClassName());
    }

    public function __unset($name) {
		Debug::writeToFile('The property &ldquo;'.$name.'&rdquo; cannot be removed in - '.self::getCalledClassName());
    }

    public function __set($name, $value) {
		Debug::writeToFile('Can not set &ldquo;'.$value.'&rdquo; to property &ldquo;'.$name.'&rdquo; in'.self::getCalledClassName());
    }

    public function __clone() {
		Debug::writeToFile('Cloning &ldquo;'.self::getCalledClassName().'&rdquo; is not allowed');
    }

    public function __wakeup() {
		Debug::writeToFile('Unserializing &ldquo;'.self::getCalledClassName().'&rdquo; is not allowed');
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
	 * Геттер для статических свойств
	 *
	 * @param string $name - название свойства;
	 */
    static function get(string $name) {
        try {
            if (!isset(self::${$name})) {
				throw new SystemException('Неизвестная переменная: '.$name);
            }
	
			return self::${$name};
        } catch (SystemException $e) {
			Debug::writeToFile($e->getMessage());
        }
        
        return;
    }
}