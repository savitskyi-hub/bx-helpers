<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Text\BinaryString;
use SavitskyiHub\BxHelpers\Helpers\IO\Dir;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class BeforeEndBuffer
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
class BeforeViewContent
{
	/**
	 * - инициализирует передачу данных в JS;
	 * - метод автоматически выполняется через обработчик в прологе ядра;
	 */
	public static function Init() {
		$isBxRand = 0;
		$isAjax = Variable::$bxRequest->isAjaxRequest();
		$isAdminSection = Variable::$bxRequest->isAdminSection();
		
		if ($bxRand = Variable::$bxServer->get("REDIRECT_QUERY_STRING")) {
			$isBxRand = BinaryString::getLength(BinaryString::getPosition($bxRand, "bxrand"));
		}
		
		/**
		 * - заполняем глобальные параметры что будут доступны для дальнейшей работы с ними;
		 * - при Ajax, внутреннего редиректа, для админ части, убираем вывод чтобы небыло конфликтов;
		 */
		if (!$isBxRand && !$isAjax && !$isAdminSection) {
			$helpersOptionJS = '
			<script type="text/javascript">
				BX.ready(function() {
					if (undefined != BX.SavitskyiHub) {
						BX.SavitskyiHub.BxHelpers.Helpers.Option = '.\CUtil::PhpToJSObject([
							"LANGUAGE_ID" => LANGUAGE_ID,
							"SITE_DIR" => \CUtil::JSEscape(SITE_DIR),
							"SITE_ID" => SITE_ID,
							"SITE_COOKIE_PREFIX" => mb_strtoupper(BinaryString::getSubstring(Dir::getCacheDirectoryPrefixName(), 1)).'_',
							"SITE_TEMPLATE_PATH" => \CUtil::JSEscape(SITE_TEMPLATE_PATH),
							"HELPERS_LIBRARY_PATH" => Dir::getPackagePath(false)
						]).';
					}
				});
			</script>';
			
			/**
			 * Реализуем вставку перед подключением пользовательских скриптов
			 */
			Asset::getInstance()->addString($helpersOptionJS, true);
		}
	}
}