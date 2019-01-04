BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Base');

(function() {
	'use strict';

	/**
	 * Объект для работы с базовыми методами что нужны для удобной и качественной роботы проекта
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Base = {
		params : {
			useFlexContainerEditMode : true,
			useReplaceCallto2TellInLink : true
		},

		/**
		 * Для автоматической работы некоторых методов производим автозапуск
		 */
		init : function() {
			if (this.params.useFlexContainerEditMode) {
				this.stylizeSystemBlock();
			}

			if (this.params.useReplaceCallto2TellInLink) {

			}
		},

		/**
		 *
		 */
		stylizeSystemBlock : function() {
			'use strict';

			let areaCompBlocks = document.querySelectorAll('[id^="bx_incl_area_"], [id^="comp_"]'),
				editStyleList = ["display", "flex-grow", "flex-wrap", "align-items", "justify-content", "width", "max-width"],
				firstParentNotAreaComp, obParentStyle, areaCompStyle, transformName;

			if (areaCompBlocks.length) {
				areaCompBlocks.forEach(function(node) {
					firstParentNotAreaComp = node.closest((0 > node.id.indexOf('comp_')? ':not([id^="bx_incl_area_"])' : ':not([id^="comp_"])'));

					if (firstParentNotAreaComp != null) {
						obParentStyle = getComputedStyle(firstParentNotAreaComp);
						areaCompStyle = getComputedStyle(node);

						editStyleList.forEach(function(styleName) {
							if (areaCompStyle[styleName] != obParentStyle[styleName]) {
								transformName = styleName.replace(/(-\w)/, function(match, p1, offset, string) {
									return p1.substr(1).toUpperCase();
								});

								node.style[transformName] = obParentStyle[styleName];
							}
						});
					}
				});
			}
		}


		// Replace callto to tel in tag "a"
		//if (BX.browser.IsMobile()) {
		//	var findCallto = $(".main-work-phone a"),
		//		oldHref;
		//
		//	if (findCallto.length) {
		//		findCallto.each(function() {
		//			oldHref = $(this).attr("href");
		//			$(this).attr("href", 'tel' + oldHref.substr(6));
		//		});
		//	}
		//}



		//function replaceCapchaContain() {
		//	var containsCapcha = $(".capcha_replace"),
		//		arReplaceCaptcha = {mode : 2, ids : []};
		//
		//	if (containsCapcha.length) {
		//		containsCapcha.each(function() {
		//			if (!$(this).data("id")) {
		//				console.error("Идентификатор для Capcha отсутствует");
		//			} else {
		//				arReplaceCaptcha.ids.push($(this).data("id"));
		//			}
		//		});
		//
		//		if (arReplaceCaptcha.ids.length) {
		//			$.ajax({
		//				url : SITE_DIR + 'ajax/captcha.php',
		//				type : "POST",
		//				data : arReplaceCaptcha,
		//				success : function(msg) {
		//					var response = JSON.parse(msg),
		//						newContainsCapcha = $(".capcha_replace");
		//
		//					newContainsCapcha.each(function() {
		//						if (response.result[$(this).data("id")]) {
		//							$(this).closest('.input-section').html(response.result[$(this).data("id")]);
		//						}
		//					});
		//				}
		//			});
		//		}
		//	}
		//}


		//function resetCaptcha(containerReplace) {
		//	$.ajax({
		//		url : SITE_DIR + "ajax/captcha.php",
		//		type : "POST",
		//		data : {mode : 1},
		//		success : function(msg) {
		//			var response = JSON.parse(msg);
		//
		//			if (response.status) {
		//				containerReplace.html($(response.content).html());
		//			}
		//		}
		//	});
		//}

	};

	/**
	 * Запускаем инициализацию объекта
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Base.init();
})();