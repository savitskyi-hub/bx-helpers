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
    /**
     * @var bool - тип перехвата исключения.
     * @info - eсли false, перехват будет возращатся в родительський "catch", иначе в свойство класса Variable::$error (а там уже перехват как ошибку);
     */
//    private static $exceptionGlobal = true;
//
//    public function __call($name, $arguments) {
//        //Variable::set('error', 'Method &ldquo;'.$name.'()&rdquo; was not found in'.self::getSuffixError());
//    }
//
//    public static function __callStatic($name, $arguments) {
//        //Variable::set('error', 'The static method &ldquo;::'.$name.'()&rdquo; was not found in'.self::getSuffixError());
//    }
//
//    public function __get($name) {
//        //Variable::set('error', 'The property &ldquo;'.$name.'&rdquo; does not exist in'.self::getSuffixError());
//    }
//
//    public function __isset($name) {
//        //Variable::set('error', 'The property &ldquo;'.$name.'&rdquo; does not exist in'.self::getSuffixError());
//    }
//
//    public function __unset($name) {
//       // Variable::set('error', 'The property &ldquo;'.$name.'&rdquo; cannot be removed in'.self::getSuffixError());
//    }
//
//    public function __set($name, $value) {
//       // Variable::set('error', 'Can not set &ldquo;'.$value.'&rdquo; to property &ldquo;'.$name.'&rdquo; in'.self::getSuffixError());
//    }
//
//    public function __clone() {
//        //Variable::set('error', 'Cloning &ldquo;'.self::getSuffixError().'&rdquo; is not allowed');
//    }
//
//    public function __wakeup() {
//        //Variable::set('error', 'Unserializing &ldquo;'.self::getSuffixError().'&rdquo; is not allowed');
//    }
//
//    /**
//     * Метод возращает название класа из которого делаем вызов.
//     * @return mixed
//     */
//    static function getCalledClassName() {
//        return get_called_class();
//    }
//
//    /**
//     * Метод возращает окончание строки ошибки (откуда она возникла).
//     * @return string
//     */
//    static function getSuffixError() {
//        return ' in method: '.self::getCalledClassName();
//    }
//
//    /**
//     * @param string $name
//     * @param mixed $val
//     * @throws SystemException
//     */
//    static function set($name, $val) {
//
//        try {
//
//            if (!isset(self::${$name})) {
//                throw new SystemException('Undefined &ldquo;'.$name.'&rdquo; name varaible');
//            }
//
//            self::${$name} = $val;
//
//        } catch (SystemException $e) {
//
//            if (self::get('exceptionGlobal')) {
//                Variable::set('error', $e->getMessage().self::getSuffixError());
//            } else {
//                throw $e;
//            }
//
//        }
//
//    }
//
//    /**
//     * @param string $name
//     * @param boolean $nameKey
//     * @param mixed $val
//     * @throws SystemException
//     */
//    static function push($name, $val, $nameKey = false) {
//
//        try {
//
//            if (!isset(self::${$name})) {
//                throw new SystemException('Undefined &ldquo;'.$name.'&rdquo; name varaible');
//            } elseif (!is_array(self::${$name})) {
//                throw new SystemException('This variable &ldquo;'.$name.'&rdquo; is not type array');
//            }
//
//            if ($nameKey) {
//                self::${$name}[$nameKey] = $val;
//            } else {
//                self::${$name} = $val;
//            }
//
//        } catch (SystemException $e) {
//
//            if (self::get('exceptionGlobal')) {
//                Variable::set('error', $e->getMessage().self::getSuffixError());
//            } else {
//                throw $e;
//            }
//
//        }
//
//    }
	
	
	/**
	 * Геттер для классов
	 *
	 * @param sting $name - название свойства;
	 *
	 * @return mixed
	 * @throws \Exception
	 */
    static function get(string $name) {
        try {
            if (!isset(self::${$name})) {
                throw new SystemException('Undefined name varaible: &ldquo;'.$name.'&rdquo;');
            }
	
			return self::${$name};
        } catch (SystemException $e) {
			throw $e;
        }
        
        return '';
    }
}