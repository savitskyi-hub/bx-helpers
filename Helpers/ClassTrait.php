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



//	/**
//	 * Проверяет существует(ют) ли XML значение(я) в пользовательськом поле типа "список"
//	 *
//	 * @param array $arEnumList
//	 * @param string $enumCode
//	 * @param string $searchXml
//	 * @return bool
//	 */
//	public static function isExistXmlInEnumField(array $arEnumList, string $enumCode, string $searchXml): bool {
//		try {
//						$arEnumList = $arEnumList["XML2ID"];
//
//						if (!isset($arEnumList[$enumCode])) {
//			//				throw new SystemException("Пользовательськое поле с названием &ldquo;".$typeEnumName."&rdquo; не существует");
//						} elseif (is_array($checkName)) {
//			//
//			//				$listEnum = $arEnumList[$typeEnumName];
//
//				foreach ($checkName as $v) {
//					if (!array_key_exists($v, $listEnum)) {
//						$is_success = false;
//						break;
//			//					}
//			//				}
//			//
//						} else {
//			//
//			//				if (!array_key_exists($checkName, $arEnumList[$typeEnumName])) {
//			//					$is_success = false;
//			//				}
//			//
//						}
//		} catch (SystemException $e) {
//			//$e->getMessage()
//
//		}

//
//	}
//
//	/**
//	 * Метод проверяет существует(ют) ли ID значение(я) в пользовательськом поле типа "список"

//	 * @param $checkName
//	 * @param $typeEnumName
//	 * @return bool
//	 * @throws SystemException
//	 */
//	static function isExistIdValueInEnumField() {
//
//		try {
//
//			$is_success = true;
//			$arEnumList = self::$bxEnumFields["XML2ID"];
//
//			if (!isset($arEnumList[$typeEnumName])) {
//				throw new SystemException("Пользовательськое поле с названием &ldquo;".$typeEnumName."&rdquo; не существует");
//			} elseif (is_array($checkName)) {
//
//				$listEnum = $arEnumList[$typeEnumName];
//
//				foreach ($checkName as $v) {
//					if (!in_array($v, $listEnum)) {
//						$is_success = false;
//						break;
//					}
//				}
//
//			} else {
//
//				if (!in_array($checkName, $arEnumList[$typeEnumName])) {
//					$is_success = false;
//				}
//
//			}
//
//			return $is_success;
//
//		} catch (SystemException $e) {
//
//			if (self::get('exceptionGlobal')) {
//				Variable::set('error', $e->getMessage().self::getSuffixError());
//			} else {
//				throw $e;
//			}
//
//		}
//
//	}