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

/**
 * Class AfterEpilog
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
class AfterEpilog
{
	/**
	 * - инициализирует передачу данных в JS;
	 * - метод автоматически выполняться через обработчик в прологе ядра;
	 */
	public static function Init() {
		
		/**
		 * Заполняем глобальные параметры что будут доступны для дальнейшей работы с ними
		 */
		echo '
			<script>
				BX.ready(function() {
					"use strict";
					
					let Option = BX.SavitskyiHub.BxHelpers.Helpers.Option;
					
					if (Option != undefined) {
						Option.LANGUAGE_ID = "'.LANGUAGE_ID.'";
						Option.SITE_DIR = "'.\CUtil::JSEscape(SITE_DIR).'";
						Option.SITE_ID = "'.SITE_ID.'";
						Option.SITE_COOKIE_PREFIX = "'.mb_strtoupper(substr(Dir::getCacheDirectoryPrefixName(), 1)).'_";
						Option.SITE_TEMPLATE_PATH = "'.\CUtil::JSEscape(SITE_TEMPLATE_PATH).'";
					}
				});
			</script>';
	}
}