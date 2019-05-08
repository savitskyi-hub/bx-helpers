/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function(window) {
	'use strict';

	if (!!window.BX.SavitskyiHub) {
		return;
	}

	/**
	 * Создадим свою область видимости библиотеки в объекте "BX"
	 */
	BX.namespace("SavitskyiHub.BxHelpers.Helpers");
	BX.namespace("SavitskyiHub.BxHelpers.Helpers.Content");
	BX.namespace("SavitskyiHub.BxHelpers.Helpers.Main");
	BX.namespace('SavitskyiHub.BxHelpers.Helpers.Option');

	/**
	 * Глобальные параметры что будут переданы из PHP
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Option = {
		LANGUAGE_ID : "",
		SITE_DIR : "",
		SITE_ID : "",
		SITE_COOKIE_PREFIX : "",
		SITE_TEMPLATE_PATH : ""
	};
})(window);