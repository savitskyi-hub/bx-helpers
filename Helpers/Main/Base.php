<?php

namespace SavitskyiHub\BxHelpers\Helpers\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

/**
 * Class Base
 * @package SavitskyiHub\BxHelpers\Helpers\Main
 *
 * Класс предназначен для создания базовых методов что нужны при реализации или поддержки проекта
 */
class Base
{
	/**
	 * Метод проверяет включена ли опция подключения минифицированных файлов на сайте
	 * @return bool
	 */
	public static function isUseMinifiedAssets(): bool {
		if (Option::get("main", "use_minified_assets") == "Y") {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Метод проверяет находимся ли мы на главной странице сайта
	 * @return bool
	 */
	public static function isMainPage(): bool {
		return preg_match("#^".SITE_DIR."index.php#ui", Application::getInstance()->getContext()->getRequest()->getRequestedPage());
	}
	
	/**
	 * Метод проверяет находимся ли мы в конкретном разделе сайта
	 * @param string $pathSecton
	 * @return bool
	 */
	public static function isSection(string $pathSecton): bool {
		return preg_match("#^".SITE_DIR.$pathSecton."#ui", Application::getInstance()->getContext()->getRequest()->getRequestedPage());
	}
	
	/**
	 * Метод возвращает префикс (из названия домена) для указания в названии директории хранения кеша
	 * @return string
	 */
	public static function getCacheDirectoryPrefixName(): string {
		return explode(".", Application::getInstance()->getContext()->getServer()->get("SERVER_NAME"))[0];
	}
}