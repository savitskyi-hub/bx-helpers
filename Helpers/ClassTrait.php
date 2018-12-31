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

/**
 * Trait ClassTrait
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
trait ClassTrait
{
    public function __call($name, $arguments) {
        //Variable::set('error', 'Method &ldquo;'.$name.'()&rdquo; was not found in'.self::getSuffixError());
    }

    public static function __callStatic($name, $arguments) {
        //Variable::set('error', 'The static method &ldquo;::'.$name.'()&rdquo; was not found in'.self::getSuffixError());
    }

    public function __get($name) {
        //Variable::set('error', 'The property &ldquo;'.$name.'&rdquo; does not exist in'.self::getSuffixError());
    }

    public function __isset($name) {
        //Variable::set('error', 'The property &ldquo;'.$name.'&rdquo; does not exist in'.self::getSuffixError());
    }

    public function __unset($name) {
       // Variable::set('error', 'The property &ldquo;'.$name.'&rdquo; cannot be removed in'.self::getSuffixError());
    }

    public function __set($name, $value) {
       // Variable::set('error', 'Can not set &ldquo;'.$value.'&rdquo; to property &ldquo;'.$name.'&rdquo; in'.self::getSuffixError());
    }

    public function __clone() {
        //Variable::set('error', 'Cloning &ldquo;'.self::getSuffixError().'&rdquo; is not allowed');
    }

    public function __wakeup() {
        //Variable::set('error', 'Unserializing &ldquo;'.self::getSuffixError().'&rdquo; is not allowed');
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
	 * @throws SystemException
	 */
    static function get(string $name) {
        try {
            if (!isset(self::${$name})) {
                throw new SystemException('Неопределенная переменная имени: &ldquo;'.$name.'&rdquo;');
            }
	
			return self::${$name};
        } catch (SystemException $e) {
			
        }
        
        return;
    }
}