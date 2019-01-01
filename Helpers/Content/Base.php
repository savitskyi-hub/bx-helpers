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
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Text\HtmlFilter;
use SavitskyiHub\BxHelpers\Helpers\IO\File;

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
	public static function getTimeTag(string $dateTime,  string $formate = 'd.m.Y'): string {
		$timeStamp = MakeTimeStamp($dateTime);
		
		if ($timeStamp) {
			$return = '<time datetime="'.FormatDate("Y-m-d", $timeStamp).'">'.FormatDate($formate, $timeStamp).'</time>';
		}
		
		return $return ?? '';
	}
	
	/**
	 * Генерирует путь к новой CAPTCHA
	 *
	 * @return string
	 */
	public static function getNewPath2Captcha(): string {
		require_once(Application::getDocumentRoot()."/bitrix/modules/main/classes/general/captcha.php");
		
		$captcha = new \CCaptcha();
		$password = Option::get("main", "captcha_password", "");
		
		if (strlen($password) <= 0) {
			Option::set("main", "captcha_password", ($password = ApplicationPasswordTable::generatePassword()));
		}
		
		$captcha->SetCodeCrypt($password);
		
		return '/bitrix/tools/captcha.php?captcha_code='.HtmlFilter::encode($captcha->GetCodeCrypt());
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