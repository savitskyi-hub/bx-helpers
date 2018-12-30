<?php
//
///**
// * This file is part of the savitskyi-hub/bx-helpers package.
// *
// * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
// *
// * For the full copyright and license information, please view the LICENSE
// * file that was distributed with this source code.
// */
//
//namespace SiteApi\Log;
//
//use Bitrix\Main\SystemException;
//use SiteApi\MainTrait;
//use SiteApi\Variable;
//
///**
// * Class LogFile
// * @package SiteApi
// * @author Andrew Savitskyi <admin@savitskyi.com.ua>
// */
//class Logging implements LogInterface
//{
//
//    use MainTrait;
//
//    /**
//     * @var string - путь к файлу логирования;
//     */
//    private static $path2log = '';
//
//    /**
//     * @var string - значение что логируется;
//     */
//    private static $value = '';
//
//    /**
//     * Запись значения в лог файл.
//     * @return bool|int
//     * @throws SystemException
//     */
//    public function push() {
//
//        try {
//
//            $path2log = self::get('path2log');
//            $value = self::get('value');
//
//            if (!$path2log || !$value) {
//                throw new SystemException('Missing required properties');
//            }
//
//            return file_put_contents($path2log, date("Y-m-d H:i:s")." ".$value."\r\n", FILE_APPEND);
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
//     * @param $value
//     * @throws SystemException
//     */
//    public function setValue($value) {
//
//        try {
//
//            if (!is_string($value)) {
//                throw new SystemException('Value does not match &ldquo;string&rdquo; type');
//            } elseif (mb_strlen($value) > 1000) {
//                throw new SystemException('The value must not exceed 1000 characters in length');
//            }
//
//            self::set('value', $value);
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
//     * @param $where
//     * @throws SystemException
//     */
//    public function setWhereLogging($where) {
//
//        try {
//
//            if (!is_string($where)) {
//                throw new SystemException('Value does not match &ldquo;string&rdquo; type');
//            } elseif (mb_strlen($where) > 20) {
//                throw new SystemException('The value must not exceed 20 characters in length');
//            } elseif (mb_strlen($where) < 3) {
//                throw new SystemException('The value must exceed 3 characters in length');
//            } elseif (preg_match("#[^a-z-]{1}#ui", $where)) {
//                throw new SystemException('Value has invalid characters');
//            }
//
//            self::set('path2log', dirname(__FILE__).'/logs/'.$where.'.log');
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
//}