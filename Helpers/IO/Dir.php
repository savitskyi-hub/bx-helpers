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
	 * Метод возвращает путь к директории пакета библиотеки
	 *
	 * @return string
	 */
	public static function getPackagePath(): string {
		$pathNameSpaceDir = __DIR__;
		$pathPackageDir = dirname($pathNameSpaceDir, 2);
	
		return $pathPackageDir;
	}
}