<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\IO;

use Bitrix\Main\Application;

/**
 * Class Dir
 * @package SavitskyiHub\BxHelpers\Helpers\IO
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для работы/получения директорий
 */
class Dir
{
	/**
	 * Возвращает путь к директории пакета библиотеки
	 *
	 * @return string
	 */
	public static function getPackagePath(): string {
		$pathNameSpaceDir = __DIR__;
		$pathPackageDir = dirname($pathNameSpaceDir, 2);
	
		return $pathPackageDir;
	}
	
	/**
	 * Возвращает путь к директории логов
	 *
	 * @return string
	 */
	public static function getLogsPath(): string {
		return Application::getDocumentRoot().'/local/logs';
	}
	
	/**
	 * Возвращает префикс (из названия домена) для указания в названии директории хранения кеша
	 *
	 * @return string
	 */
	public static function getCacheDirectoryPrefixName(): string {
		return '/'.explode(".", Application::getInstance()->getContext()->getServer()->get("SERVER_NAME"))[0];
	}
}