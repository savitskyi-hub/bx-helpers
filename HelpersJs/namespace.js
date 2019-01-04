/**
 * Создадим свою область видимости библиотеки в объекте "BX"
 */
BX.namespace("SavitskyiHub.BxHelpers.Helpers");
BX.namespace('SavitskyiHub.BxHelpers.Helpers.Option');

(function() {
	'use strict';

	/**
	 * Глобальные параметры что будут переданы из PHP
	 *
	 * @type {{
	 * 		LANGUAGE_ID: string,
	 * 		SITE_DIR: string,
	 * 		SITE_ID: string,
	 * 		SITE_COOKIE_PREFIX: string,
	 * 		SITE_TEMPLATE_PATH: string,
	 * 		init: SavitskyiHub.BxHelpers.Helpers.Option.init
	 * }}
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Option = {
		LANGUAGE_ID : "",
		SITE_DIR: "",
		SITE_ID: "",
		SITE_COOKIE_PREFIX : "",
		SITE_TEMPLATE_PATH: "",
	};
})();