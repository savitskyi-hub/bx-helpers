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
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

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
			<div class="helpers-notice">
				<div class="helpers-notice-header">'.HtmlFilter::encode($header).'</div>
				<div class="helpers-notice-text">'.HtmlFilter::encode($description).'</div>
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
			<div class="helpers-error">
				<div class="helpers-error-header">'.HtmlFilter::encode($header).'</div>
				<div class="helpers-error-text">'.HtmlFilter::encode($description).'</div>
				'.$otherContent.'
			</div>';
	}
	
	/**
	 * Возвращает тег <link> со значением атрибута rel="canonical"
	 *
	 * @param bool $lastSlash
	 * @return string
	 */
	public static function getCanonicalTag(bool $lastSlash = true): string {
		$protocol = Variable::$bxRequest->isHttps()? 'https://' : 'http://';
		$url = $protocol.Variable::$bxServer->getServerName().Variable::$bxServer->getRequestUri();
		
		if (preg_match("#^.+?(\?.+)#u", $url, $queryString, PREG_OFFSET_CAPTURE)) {
			$url = substr($url, 0, $queryString[1][1]);
		}
		
		if (!$lastSlash) {
			if ('/' == mb_substr($url, -1)) {
				$url = substr($url, 0, -1);
			}
		}
		
		return '<link rel="canonical" href="'.HtmlFilter::encode($url).'">';
	}
	
	/**
	 * Возвращает полноценную 404 страницу
	 *
	 * @return bool
	 */
	public static function getPage404() {
		if (!array_key_exists("APPLICATION", $GLOBALS)) {
			return false;
		} elseif (!defined("SITE_TEMPLATE_PATH")) {
			return false;
		}
		
		$GLOBALS["APPLICATION"]->RestartBuffer();

		if (!defined("ERROR_404")) {
			define("ERROR_404", "Y");
		}
		
		require_once(Application::getDocumentRoot().constant("SITE_TEMPLATE_PATH").'/header.php');
		require_once(Application::getDocumentRoot().'/404.php');
		require_once(Application::getDocumentRoot().constant("SITE_TEMPLATE_PATH").'/footer.php');
	}
}