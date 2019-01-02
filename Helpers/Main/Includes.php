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
use Bitrix\Main\Page\Asset;
use SavitskyiHub\BxHelpers\Helpers\IO\Dir;
use SavitskyiHub\BxHelpers\Helpers\IO\File;

/**
 * Class Includes
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для подключения JS и CCS файлов, что нужны для работы библиотеки или проекта на уровне
 */
class Includes
{
	/**
	 * Подключает все стили библиотки
	 *
	 * @return void
	 */
	public static function libraryCss() {
		$startPath = Dir::getPackagePath().'/HelpersCss';
		$resultCssList = File::getRecursiveFilesInDir($startPath, (Method::isUseMinifiedAssets()? 'min.css' : 'css'));
		$resultCssList = ($resultCssList? array_reverse($resultCssList) : []);
		
		foreach ($resultCssList as $path2css) {
			Asset::getInstance()->addCss(explode(Application::getDocumentRoot(), $path2css)[1]);
		}
	}
	
	/**
	 * Подключает все скрипты библиотки
	 *
	 * @return void
	 */
	public static function libraryJs() {
		$startPath = Dir::getPackagePath().'/HelpersJs';
		$resultJsList = File::getRecursiveFilesInDir($startPath, (Method::isUseMinifiedAssets()? 'min.js' : 'js'));
		$resultJsList = ($resultJsList? array_reverse($resultJsList) : []);
		
		foreach ($resultJsList as $path2js) {
			Asset::getInstance()->addJs(explode(Application::getDocumentRoot(), $path2js)[1]);
		}
	}
}