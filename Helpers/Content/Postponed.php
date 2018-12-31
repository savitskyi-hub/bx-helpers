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

use Bitrix\Main\Text\HtmlFilter;

/**
 * Class Postponed
 * @package SavitskyiHub\BxHelpers\Helpers\Content
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для добавления контента методом отложенного вызова
 */
class Postponed
{
	/**
	 * Добавляет тегу <main> атрибут "class" со значением
	 *
	 * @param string $pagePropName - название свойства страницы в котором указано название класса для тега <main>;
	 * @return string
	 */
	public static function getMainClassName(string $pagePropName): string {
		$mainClassName = $GLOBALS["APPLICATION"]->GetPageProperty($pagePropName);
		
		if ($mainClassName) {
			return 'class="'.HtmlFilter::encode($mainClassName).'"';
		} else {
			return '';
		}
	}
}