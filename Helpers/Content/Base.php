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
 * Class Base
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для вывода участков статического контента который часто используется на сайте (с дополнительными проверками)
 */
class Base
{
	/**
	 * Чтобы не выводить лишний раз большое количество идентификаторов для элементов, которые при наведении будут отображаться
	 * как включаемая область, выполняем проверку на включение режима редактирования. И если он включен, возвращаем идентификатор
	 * включаемой области в виде сформированного атрибута со значением
	 *
	 * @param string $ariaID - идентификатор включаемой области;
	 * @return string
	 */
	public static function getAriaID(string $ariaID): string {
		if ($GLOBALS["APPLICATION"]->GetShowIncludeAreas()) {
			$return = 'id="'.$ariaID.'"';
		}
		
		return $return ?? '';
	}
	
	/**
	 * Возвращает HTML тег <time>
	 *
	 * @param string $dateTime
	 * @param string $formate - формат вывода даты;
	 * @return string
	 */
	public static function getTimeTag(string $dateTime, string $formate = 'd.m.Y'): string {
		$timeStamp = MakeTimeStamp($dateTime);
		
		if ($timeStamp) {
			$return = '<time datetime="'.FormatDate("Y-m-d", $timeStamp).'">'.FormatDate($formate, $timeStamp).'</time>';
		}
		
		return $return ?? '';
	}
	
	/**
	 * Возвращает сформированный HTML контент в котором присутствует Captcha
	 *
	 * @param array $IDs - для множественной генерации;
	 * @param string $textError - сообщение при ошибке;
	 * @return array
	 */
	public static function getCaptcha(array $IDs = [0], string $textError = 'Введите слово на картинке'): array {
		$arReturn = [];
		$textError = HtmlFilter::encode($textError);
		
		foreach ($IDs as $id) {
			$arNewCaptcha = \SavitskyiHub\BxHelpers\Helpers\Main\Method::getNewParamsCaptcha();
			$arReturn[$id] = '
				<div class="helpers-form-captcha">
					<img src="'.$arNewCaptcha["path"].'" alt="CAPTCHA">
					<input name="captcha_code" type="hidden" value="'.$arNewCaptcha["code"].'">
					<input name="captcha_word" type="text" value="" placeholder="'.$textError.'" required autocomplete="off">
					<span class="helpers-form-captcha-error">'.$textError.'</span>
				</div>';
		}
		
		return $arReturn;
	}
	
	/**
	 * Возвращает оповещение
	 *
	 * @param string $description
	 * @param string $header
	 * @param string $otherContent - использовать на свой страх и риск;
	 * @return string
	 */
	public static function showNotice(string $description, string $header = '', string $otherContent = ''): string {
		return '
			<div class="notice-massage">
				<div class="notice-massage-header">'.HtmlFilter::encode($header).'</div>
				<div class="notice-massage-text">'.HtmlFilter::encode($description).'</div>
				'.$otherContent.'
			</div>';
	}
	
	/**
	 * Возвращает уведомление об ошибке
	 *
	 * @param string $description
	 * @param string $header
	 * @param string $otherContent - использовать на свой страх и риск;
	 * @return string
	 */
	public static function showError(string $description, string $header = '', string $otherContent = ''): string {
		return '
			<div class="error-message">
				<div class="error-message-header">'.HtmlFilter::encode($header).'</div>
				<div class="error-message-text">'.HtmlFilter::encode($description).'</div>
				'.$otherContent.'
			</div>';
	}
}