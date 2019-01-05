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

use SavitskyiHub\BxHelpers\Helpers\IO\Dir;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class AfterEpilog
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
class AfterEpilog
{
	/**
	 * - инициализирует передачу данных в JS;
	 * - метод автоматически выполняется через обработчик в прологе ядра;
	 */
	public static function Init() {
		
		/**
		 * - заполняем глобальные параметры что будут доступны для дальнейшей работы с ними;
		 * - при Ajax запросе убираем вывод, чтобы небыло ошибок при отдачи;
		 */
		$OPTION = [
			"LANGUAGE_ID" => LANGUAGE_ID,
			"SITE_DIR" => \CUtil::JSEscape(SITE_DIR),
			"SITE_ID" => SITE_ID,
			"SITE_COOKIE_PREFIX" => mb_strtoupper(substr(Dir::getCacheDirectoryPrefixName(), 1)).'_',
			"SITE_TEMPLATE_PATH" => \CUtil::JSEscape(SITE_TEMPLATE_PATH)
		];
		
		if (!Variable::$bxRequest->isAjaxRequest()) {
			echo '
				<script type="text/javascript">
					BX.ready(function() {
						if (undefined != BX.SavitskyiHub) {
							BX.SavitskyiHub.BxHelpers.Helpers.Option = '.\CUtil::PhpToJSObject($OPTION).';
						}
					});
				</script>';
			
		}
	}
}