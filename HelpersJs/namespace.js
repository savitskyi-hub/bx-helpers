/**
 * Создадим свою область видимости библиотеки в объекте "BX"
 */
BX.namespace("SavitskyiHub.BxHelpers.Helpers");
BX.namespace('SavitskyiHub.BxHelpers.Helpers.Option');

(function() {
	'use strict';

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
})();