<?php

namespace Local\Helpers\Main;

use Bitrix\Main\Config\Option;

/**
 * Class Base
 * @package Local\Helpers\Main
 *
 * Класс предназначен для создания базовых методов и решения глобальных задач
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
	 *
	 * @return string
	 */
	public static function getCacheDirectoryPrefixName(): string {
		///explode(".", SERVER_NAME)[0];
		/// Variable::$bxRequest;
	}
}