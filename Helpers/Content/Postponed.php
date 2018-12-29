<?php

namespace SavitskyiHub\BxHelpers\Helpers\Content;

use Bitrix\Main\Text\HtmlFilter;

/**
 * Class Postponed
 * @package SavitskyiHub\BxHelpers\Helpers\Content
 *
 * Класс предназначен для добавления контента методом отложенного вызова
 */
class Postponed
{
	/**
	 * Метод добавляет тегу <main> атрибут "class" со значением
	 * @param string $pagePropName - название свойства страницы в котором указано название класса для тега <main>
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