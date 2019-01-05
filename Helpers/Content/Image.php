<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Content;

use Bitrix\Main\Application;
use Bitrix\Main\Text\HtmlFilter;

/**
 * Class Image
 * @package SavitskyiHub\BxHelpers\Helpers\Content
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для вывода изображений в формате HTML тега
 */
class Image
{
	/**
	 * Версия подзагрузки файлов
	 * @var string
	 */
	protected static $sxFileVersion = "?fv=00001";
	
	/**
	 * Путь до дефолтного изображения, начиная с шаблонной директории сайта
	 * @var string
	 */
	protected static $pathNoImg = '/img/no-image.png';
	protected static $pathNoAvatar = '/img/no-avatar.png';
	
	/**
	 * Дефолтное название атрибута "alt" для тега <img>
	 * @var string
	 */
	protected static $defaultImgAlt = 'No Image';
	
	/**
	 * Дефолтное название атрибута "class" для тега <img>
	 * @var string
	 */
	protected static $defaultImgClass = '';
	
	/**
	 * Значение максимальной ширины от которой будет работать дефолтное значение технологии гибких (responsive) изображений.
	 * ВАЖНО!!! Нужно следить в проекте чтобы небыло большего значения, иначе испортится порядок отображения разных размеров изображений
	 *
	 * @var string
	 */
	protected static $srcsetMaxSize = '1680';
	
	/**
	 * Вывод изображения в виде сформированного HTML тега, в случае если нет изображения будет подставляться дефолтное
	 *
	 * @param $img - может быть как массив с параметрами, так и простая строка в которой расположен путь к изображению;
	 * @param string $alt
	 * @param string $class
	 * @param bool $srcset - при включенном параметре изображение будет формироваться с помощью технологии гибких (responsive) изображений;
	 * @param bool $async - сохраняет в блочном теге, чтобы не загружать изображения сразу после открытия страницы, а подгрузить через события JS;
	 * @param array $attrs - перечисленные в строке атрибуты с их значением;
	 * @return string
	 */
	public static function show($img, string $alt = '', string $class = '', bool $srcset = false, bool $async = false, array $attrs = []): string {
		$alt = 'alt="'.($alt? HtmlFilter::encode($alt) : self::$defaultImgAlt).'"';
		$class = 'class="'.($class? HtmlFilter::encode($class) : self::$defaultImgClass).'"';
		$attrs = ($attrs? self::parseAttrs($attrs) : '');
		
		$fileVersion = self::$sxFileVersion;
		
		if (!$img) {
			return '<img src="'.self::getPathNoImg().'" '.$alt.' '.$class.' '.$attrs.'>';
		} elseif (!is_array($img)) {
			
			/**
			 * Если передано один лишь путь к изображению
			 */
			if (self::isUseUriParam($img)) {
				$fileVersion .= '&'.substr(self::getUriParam($img), 1);
				$img = self::cleanUriParam($img);
			}
			
			if (file_exists(Application::getDocumentRoot().$img)) {
				return '<img src="'.HtmlFilter::encode($img).$fileVersion.'" '.$alt.' '.$class.' '.$attrs.'>';
			} else {
				return '<img src="'.self::getPathNoImg().'" '.$alt.' '.$class.' '.$attrs.'>';
			}
			
		} else {
			
			/**
			 * Если не включена технология гибких изображений, формируется обычное из переданных параметров изображение
			 */
			if (!$srcset) {
				
				if (self::isUseUriParam($img["SRC"])) {
					$fileVersion .= '&'.substr(self::getUriParam($img["SRC"]), 1);
					$img["SRC"] = self::cleanUriParam($img["SRC"]);
				}
				
				$src = ($img["SRC"]? HtmlFilter::encode($img["SRC"]).$fileVersion : self::getPathNoImg());
				$alt = ($img["ALT"]? 'alt="'.HtmlFilter::encode($img["ALT"]).'"' : $alt);
				
				return '<img src="'.$src.'" '.$alt.' '.$class.' '.$attrs.'>';
				
			} else {
				
				/**
				 * Технология гибких изображений работает только в том случаи, если правильно сформировать массив данных, пример:
				 *
				 * $img = [
				 *        "767" => $arFileTmpMoB["src"],
				 *        "default" => $arFileTmpPC["src"]
				 * ];
				 *
				 * - значение хранит путь к изображению в соответствующем размере экрана, что указан в ключе массива (max-width: KEY)
				 * - ВАЖНО! массив должен быть сформирован в формате сортировки: от наименьшего до дефолтного состояния иначе будут ошибки
				 */
				if (!array_key_exists("default", $img) || !$img["default"]) {
					return '';
				} else {
					if (self::isUseUriParam($img["default"])) {
						$fileVersion .= '&'.substr(self::getUriParam($img["default"]), 1);
						$img["default"] = self::cleanUriParam($img["default"]);
					}
					
					if (!file_exists(Application::getDocumentRoot().$img["default"])) {
						return '';
					}
				}
				
				/**
				 * При включенной технологиии асинхронной загрузки, подразумевается, что разработчик самостоятельно спомощу JS конвертнет
				 * в необходимый момент блок в натуральное изображение, которое будет использовать технологию гибких изображений.
				 *
				 * Пример метода можно подсмотреть тут: SavitskyiHub.BxHelpers.Helpers.Content.Image
				 */
				if (!$async) {
					$createImg = '<img '.$alt.' '.$class.' '.$attrs.' ';
					$prefixData = '';
					
					if (1 == count($img)) {
						$createImg .= 'src="'.HtmlFilter::encode($img["default"]).$fileVersion.'"';
					}
				} else {
					$createImg = '<div data-upload-image="Y" data-'.$alt.' data-'.$class.' data-attrs="'.str_replace('"', '\'', $attrs).'"';
					$prefixData = 'data-';
					
					if (1 == count($img)) {
						$createImg .= 'data-src="'.HtmlFilter::encode($img["default"]).$fileVersion.'"';
					}
				}
				
				if (1 < count($img)) {
					$i = $j = 0;
					
					// srcset
					foreach ($img as $maxSize => $path) {
						
						if (self::isUseUriParam($path)) {
							$fileVersion2 = self::$sxFileVersion.'&'.substr(self::getUriParam($path), 1);
							$path = self::cleanUriParam($path);
						} else {
							$fileVersion2 = self::$sxFileVersion;
						}
						
						$path = HtmlFilter::encode($path).$fileVersion2;
						$maxSize = HtmlFilter::encode($maxSize);
						
						if (!$i++) {
							$createImg .= $prefixData.'src="'.HtmlFilter::encode($img["default"]).$fileVersion.'" '.$prefixData.'srcset="';
							$createImg .= ("default" == $maxSize? $path.' '.self::$srcsetMaxSize.'w' : $path.' '.$maxSize.'w');
						} else {
							$createImg .= ("default" == $maxSize? ', '.$path.' '.self::$srcsetMaxSize.'w' : ', '.$path.' '.$maxSize.'w');
						}
					}
					
					$createImg .= '" ';
					
					// sizes
					foreach ($img as $maxSize => $path) {
						$maxSize = HtmlFilter::encode($maxSize);
						
						if (!$j++) {
							$createImg .= $prefixData.'sizes="';
							$createImg .= ("default" == $maxSize? self::$srcsetMaxSize.'px' : '(max-width: '.$maxSize.'px) '.$maxSize.'px');
						} else {
							$createImg .= ("default" == $maxSize? ', '.self::$srcsetMaxSize.'px' : ', (max-width: '.$maxSize.'px) '.$maxSize.'px');
						}
					}
					
					$createImg .= '" ';
				}
				
				return $createImg.(!$async? '>' : '></div>');
			}
		}
	}
	
	/**
	 * Возвращает путь к дефолтному изображению
	 *
	 * @return string
	 */
	public static function getPathNoImg(): string {
		$path = SITE_TEMPLATE_PATH.self::$pathNoImg;
		
		if (!file_exists(Application::getDocumentRoot().$path)) {
			$path = "#";
		} else {
			$path .= self::$sxFileVersion;
		}
		
		return $path;
	}
	
	/**
	 * Возвращает путь к дефолтному изображению аватара
	 *
	 * @return string
	 */
	public static function getPathNoAvatar(): string {
		$path = SITE_TEMPLATE_PATH.self::$pathNoAvatar;
		
		if (!file_exists(Application::getDocumentRoot().$path)) {
			$path = "#";
		} else {
			$path .= self::$sxFileVersion;
		}
		
		return $path;
	}
	
	/**
	 * Возвращает альтернативный текст для изображения методом перебора, находя первое истинное значение
	 *
	 * @param array $listNames - в массив должны присваивать значения в уже отсортировано-приоритетном порядке;
	 * @return string
	 */
	public static function getFirstNotEmptyAlt(array $listNames): string {
		if (0 < count($listNames)) {
			foreach ($listNames as $alt) {
				if ('string' == gettype($alt) && 0 < strlen($alt)) {
					return $alt;
				}
			}
		}
		
		return '';
	}
	
	/**
	 * Возвращает строку из перечисленных атрибутов HTML тега
	 *
	 * @param array $arAttrs
	 * @return string
	 */
	public static function parseAttrs(array $arAttrs): string {
		$output = implode(' ', array_map(
			function($v, $k) { return sprintf("%s=\"%s\"", HtmlFilter::encode($k), HtmlFilter::encode($v)); },
			$arAttrs,
			array_keys($arAttrs)
		));
		
		return $output;
	}
	
	/**
	 * Проверяет указаный путь к изображению на наличие GET параметров
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function isUseUriParam(string $path): bool {
		if (stripos($path, '?')) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Возвращает GET параметры что были указаны в пути к изображению
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getUriParam(string $path): string {
		$lenStart = strrpos($path, '?');
		
		if ($lenStart) {
			return substr($path, $lenStart);
		}
		
		return '';
	}
	
	/**
	 * Возвращает путь к изображению удаляя из него GET параметры
	 *
	 * @param string $path
	 * @return string
	 */
	public static function cleanUriParam(string $path): string {
		$lenStart = strrpos($path, '?');
		
		if ($lenStart) {
			$path = substr($path, 0, $lenStart);
		}
		
		return $path;
	}
}