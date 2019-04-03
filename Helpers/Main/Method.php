<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Text\BinaryString;
use Bitrix\Main\Text\HtmlFilter;

/**
 * Class Base
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * Класс предназначен для создания базовых методов что нужны при реализации или поддержки проекта
 */
class Method
{
	/**
	 * Проверяет включена ли опция подключения минифицированных файлов на сайте
	 *
	 * @return bool
	 */
	public static function isUseMinifiedAssets(): bool {
		if ("Y" == Option::get("main", "use_minified_assets")) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Проверяет находимся ли мы на главной странице сайта
	 *
	 * @return bool
	 */
	public static function isMainPage(): bool {
		return preg_match("#^".SITE_DIR."index.php#ui", Application::getInstance()->getContext()->getRequest()->getRequestedPage());
	}
	
	/**
	 * Проверяет находимся ли мы в конкретном разделе сайта
	 *
	 * @param string $pathSecton
	 * @return bool
	 */
	public static function isSection(string $pathSecton): bool {
		return preg_match("#^".SITE_DIR.$pathSecton."#ui", Application::getInstance()->getContext()->getRequest()->getRequestedPage());
	}
	
	/**
	 * Проверяет есть ли текущий сайт продакшн версией сайта или тестовой
	 *
	 * @return bool
	 */
	public static function isProductionSite(): bool {
		$serverName = Application::getInstance()->getContext()->getServer()->get("SERVER_NAME");
		$serverName = ($serverName? $serverName : (new Directory(Application::getDocumentRoot()))->getName());
		
		if ("www" == BinaryString::getSubstring($serverName, 0, 3)) {
			$serverName = BinaryString::getSubstring($serverName, 4);
		}
		
		if ("dev-" == BinaryString::getSubstring($serverName, 0, 4)) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Генерация новых параметров для новой CAPTCHA
	 *
	 * @return array
	 */
	public static function getNewParamsCaptcha(): array {
		require_once(Application::getDocumentRoot()."/bitrix/modules/main/classes/general/captcha.php");
		
		$captcha = new \CCaptcha();
		$password = Option::get("main", "captcha_password", "");
		
		if (0 >= strlen($password)) {
			Option::set("main", "captcha_password", ($password = ApplicationPasswordTable::generatePassword()));
		}
		
		$captcha->SetCodeCrypt($password);
		$captchaCode = HtmlFilter::encode($captcha->GetCodeCrypt());
		
		return [
			'code' => $captchaCode,
			'path' => '/bitrix/tools/captcha.php?captcha_code='.$captchaCode
		];
	}
}