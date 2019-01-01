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
		if (Option::get("main", "use_minified_assets") == "Y") {
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
}