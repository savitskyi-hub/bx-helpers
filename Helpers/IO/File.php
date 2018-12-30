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
 * Class File
 * @package SavitskyiHub\BxHelpers\Helpers\IO
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для работы/получения файлов
 */
class File
{
	/**
	 * Метод рекурсивно обходит все директории/под директории и возвращает соответствующие файлы за шаблоном
	 * @param $path - начальный путь от которого будет происходить поиск файлов
	 * @param $fileTypePattern - тип расширения файла, по нему будет происходить отбор файлов, например ("css", "min.js")
	 * @return array
	 */
	public static function getRecursiveFilesInDir($path, $fileTypePattern): array {
		$handle = opendir($path) or die("Can't open directory ".$path);
		$files = $subFiles = [];
		
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				
				if (is_dir($path."/".$file)) {
					$subFiles = self::getRecursiveFilesInDir($path."/".$file, $fileTypePattern);
					$files = array_merge($files, $subFiles);
				} else {
					
					if (preg_match("#\w+\.".preg_quote($fileTypePattern)."#", $file)) {
						$files[] = $path."/".$file;
					}
					
				}
			}
		}
		
		closedir($handle);
		
		return $files;
	}
}