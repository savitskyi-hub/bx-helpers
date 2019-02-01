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
	 * - возвращает префикс (из названия домена, без www) для указания названия директории хранения кеша;
	 * - если скрипт был запущен из под "cron" берем название корневой директории сайта (она совпадает с доменом)
	 *
	 * @return string
	 */
	public static function getCacheDirectoryPrefixName(): string {
		$serverName = Application::getInstance()->getContext()->getServer()->get("SERVER_NAME");
		$serverName = ($serverName? $serverName : (new Directory(Application::getDocumentRoot()))->getName());
		
		if ("www" == BinaryString::getSubstring($serverName, 0, 3)) {
			$serverName = BinaryString::getSubstring($serverName, 4);
		}
		
		return '/'.explode(".", $serverName)[0];
	}
}