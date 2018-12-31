<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Logs\File;

use Bitrix\Main\SystemException;
use Bitrix\Main\Diag\Debug;
use SavitskyiHub\BxHelpers\Helpers\Logs\LogsInterface;

/**
 * Class Logging
 * @package SavitskyiHub\BxHelpers\Helpers\Logs\File
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * ////////////////////////
 */
class Logging implements LogsInterface
{
    /**
	 * Значение что логируется
     * @var string
     */
    private $value = '';
	
	/**
	 * {@inheritdoc}
	 *
	 * @param string $value
	 */
	public function setValue(string $value) {
		try {
			if (2000 < mb_strlen($value)) {
				throw new SystemException('Значение для логирования не должно превышать 2000 символов в длину');
			}

			$this->value = $value;
		} catch (SystemException $e) {
			$caller = \debug_backtrace()[0];
			$caller["message"] = $e->getMessage();
			
			Debug::dumpToFile($caller);
		}
	}
	
	
	//            if (self::get('logging')) {
	//                $logging = Log::Initial('LogFile');
	//                $logging->setValue($e->getMessage().self::getSuffixError());
	//                $logging->setWhereLogging('email-error');
	//                $logging->push();
	//            }
	
	/**
	 * {@inheritdoc}
	 */
	public function push() {
		try {
			
			//$path2log = self::get('path2log');
			//$value = self::get('value');
			
			//if (!$path2log || !$value) {
			//	throw new SystemException('Отсутствуют обязательные свойства для операции логирования');
			//}
			
			//return file_put_contents($path2log, date("Y-m-d H:i:s")." ".$value."\r\n", FILE_APPEND);
			
		} catch (SystemException $e) {
			$caller = \debug_backtrace()[0];
			$caller["message"] = $e->getMessage();
			
			Debug::dumpToFile($caller);
		}
	}
	
	
	
	/**
	 * @param $where
	 * @throws SystemException
	 */
	    public function setFileLogName($name) {
	        
	        try {
	           
	            if (!is_string($name)) {
	                throw new SystemException('Value does not match &ldquo;string&rdquo; type');
	            } elseif (mb_strlen($name) > 20) {
	                throw new SystemException('The value must not exceed 20 characters in length');
	            } elseif (mb_strlen($name) < 3) {
	                throw new SystemException('The value must exceed 3 characters in length');
	            }
	            //HtmlFilter::
	            //$where
				self::set('path2log', dirname(__FILE__).'/logs/'.$name.'.log');
	        
	        } catch (SystemException $e) {
	    
	            if (self::get('exceptionGlobal')) {
	                Variable::set('error', $e->getMessage().self::getSuffixError());
	            } else {
	                throw $e;
	            }
	            
	        }
	        
	    }
    
    
    public function isCreatedPath() {
    
	}
    
}