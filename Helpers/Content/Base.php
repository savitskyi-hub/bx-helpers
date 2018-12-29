<?php

namespace Local\Helpers\Content;

use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\Authentication\ApplicationPasswordTable as Password;
use Local\Helpers\IO\File;

/**
 * Class Base
 * @package Local\Helpers\Content
 *
 * Класс предназначен для вывода участков статического контента который часто используется на сайте (с дополнительными проверками)
 */
class Base
{
	/**
	 * Для того чтобы не выводить лишний раз большое количество идентификаторов для элементов, которые при наведении будут отображаться как включаемая область, выполняем проверку на включение режима редактирования. И если он включен, возвращаем идентификатор включаемой области в виде сформированного атрибута со значением
	 * @param string $ariaID - идентификатор включаемой области
	 * @return string
	 */
	
	public static function getAriaID(string $ariaID): string {
		if ($GLOBALS["APPLICATION"]->GetShowIncludeAreas()) {
			$return = 'id="'.$ariaID.'"';
		}
		
		return $return ?? '';
	}
	
	/**
	 * Метод возвращает HTML тег <time>
	 * @param string $dateTime
	 * @param string $formate - формат вывода даты
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
	 * Метод генерирует путь к новой CAPTCHA
	 * @return string
	 */
	public static function getNewPath2Captcha(): string {
		require_once(Application::getDocumentRoot()."/bitrix/modules/main/classes/general/captcha.php");
		
		$captcha = new \CCaptcha();
		$password = Option::get("main", "captcha_password", "");
		
		if (strlen($password) <= 0) {
			Option::set("main", "captcha_password", ($password = Password::generatePassword()));
		}
		
		$captcha->SetCodeCrypt($password);
		
		return '/bitrix/tools/captcha.php?captcha_code='.HtmlFilter::encode($captcha->GetCodeCrypt());
	}
	
	/**
	 * Метод возвращает уведомление
	 * @param string $description
	 * @param string $header
	 * @param string $otherContent - использовать на свой страх и риск
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
	 * Метод возвращает уведомление об ошибке
	 * @param string $description
	 * @param string $header
	 * @param string $otherContent - использовать на свой страх и риск
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