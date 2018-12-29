<?php

namespace SavitskyiHub\BxHelpers\Helpers\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;
use SavitskyiHub\BxHelpers\Helpers\IO\File;

/**
 * Class Includes
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 *
 * Класс предназначен для подключения необходимых файлов, что нужны для работы пакета библиотеки на уровне JS и CCS
 */
class Includes
{
	/**
	 * Метод подключает все стили локальной библиотки с пространством имен что находятся в директории
	 
	   //"SITE_TEMPLATE_PATH./css/library/"
	 *
	 *
	 *
	 * @return void
	 */
	public static function libraryCss() {
		$startPath = Application::getDocumentRoot().SITE_TEMPLATE_PATH.'/css/library';
		$resultCssList = File::getRecursiveFilesInDir($startPath, (Base::isUseMinifiedAssets()? 'min.css' : 'css'));
		$resultCssList = ($resultCssList? array_reverse($resultCssList) : []);
		
		foreach ($resultCssList as $path2css) {
			Asset::getInstance()->addCss(explode(Application::getDocumentRoot(), $path2css)[1]);
		}
	}
	
	/**
	 * Метод подключает все скрипты локальной библиотки с пространством имен что находятся в директории
	
	 
	  
	    * //"SITE_TEMPLATE_PATH./js/library/"!!!!!!!!!!!!!!!!!!!!!!!!!
	 *
	 *
	 *
	 *
	 * @return void
	 */
	public static function libraryJs() {
		$startPath = Application::getDocumentRoot().SITE_TEMPLATE_PATH.'/js/library';
		$resultJsList = File::getRecursiveFilesInDir($startPath, (Base::isUseMinifiedAssets()? 'min.js' : 'js'));
		$resultJsList = ($resultJsList? array_reverse($resultJsList) : []);
		
		foreach ($resultJsList as $path2js) {
			Asset::getInstance()->addJs(explode(Application::getDocumentRoot(), $path2js)[1]);
		}
	}
}