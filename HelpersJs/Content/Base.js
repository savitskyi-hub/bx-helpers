/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Base');

(function() {
	'use strict';

	/**
	 * Объект для работы с базовыми методами что нужны для удобной и качественной роботы проекта
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Base = {
		/**
		 * Параметры объекта
		 */
		params : {
			useFlexContainerEditMode : true,
			useReplaceCallto2TellInLink : true,
			useReplaceCaptchaSections : true
		},

		/**
		 * Для отладки собираем информацию
		 */
		debugMessage : [],

		/**
		 * Для автоматической работы некоторых методов производим автозапуск;
		 */
		init : function() {
			if (this.params.useFlexContainerEditMode) {
				this.stylizeSystemBlock();
			}

			if (this.params.useReplaceCallto2TellInLink) {
				this.autoReplaceCallto2TellInLink();
			}

			if (this.params.useReplaceCaptchaSections) {
				this.autoReplaceCaptchaSections();
			}
		},

		/**
		 * При включении режима правки система автоматически набрасывает технические html блоки которые нужны для визуального
		 * представления или выделения компонентов. Если в верстке идет разметка методом Flex блоков, то при влючении режима правки все собьется,
		 * а дописывать везде дополнительные свойства лишняя робота и не нужная, для этого:
		 *
		 * - происходить поиск всех блоков за условием "areaCompBlocks";
		 * - для каждого найденего блока происходит поиск первого родителя, в которого будет скопированы свойства что указаны "editStyleList";
		 * - скопированные свойства рекурсивным методом для выбранных блоков переопределяются автоматически;
		 */
		stylizeSystemBlock : function() {
			'use strict';

			var areaCompBlocks = document.querySelectorAll('[id^="bx_incl_area_"], [id^="comp_"]'),
				editStyleList = ["display", "flex-grow", "flex-wrap", "align-items", "justify-content", "width", "max-width"],
				firstParentNotAreaComp, obParentStyle, areaCompStyle, transformName, i, j, node, styleName;

			if (areaCompBlocks.length) {
				for (i = 0; i < areaCompBlocks.length; ++i) {
					node = areaCompBlocks[i];
					firstParentNotAreaComp = node.closest((0 > node.id.indexOf('comp_')? ':not([id^="bx_incl_area_"])' : ':not([id^="comp_"])'));

					if (firstParentNotAreaComp != null) {
						obParentStyle = getComputedStyle(firstParentNotAreaComp);
						areaCompStyle = getComputedStyle(node);

						for (j = 0; j < editStyleList.length; ++j) {
							styleName = editStyleList[j];

							if (areaCompStyle[styleName] != obParentStyle[styleName]) {
								transformName = styleName.replace(/(-\w)/, function(match, p1, offset, string) {
									return p1.substr(1).toUpperCase();
								});

								node.style[transformName] = obParentStyle[styleName];
							}
						}
					}
				}
			}
		},

		/**
		 * Производит автозамену "callto" на "tel" в требуте "href" в случаи:
		 *
		 * - если пользователь зашел на сайт из телефона;
		 * - в атрибуте "href" присутствует "callto";
		 */
		autoReplaceCallto2TellInLink : function() {
			'use strict';

			if (!BX.browser.IsMobile()) {
				var links = BX.findChild(BX('bx-html'), {tag : 'A'}, true, true),
					href;

				if (null != links) {
					links.forEach(function(node) {
						href = node.getAttribute('href');

						if (href && "callto" == href.substr(0, 6)) {
							node.setAttribute('href', 'tel' + href.substr(6));
						}
					});
				}
			}
		},

		/**
		 * Производит автозамену DOM элемента на полноценный контент нового поля для ввода Captcha, для этого:
		 *
		 * - на странице должен быть определен елемент следующего вида: <div class="helpers-form-captcha-replace" data-id="ID_CAPTCHA"></div>;
		 * - необходимо обязательно определить идентификатор "ID_CAPTCHA";
		 */
		autoReplaceCaptchaSections : function() {
			'use strict';

			var arCaptchaSections = BX.findChildren(BX('bx-html'), {tag : 'DIV', className : 'helpers-form-captcha-replace'}, true),
				ajaxPath = BX.SavitskyiHub.BxHelpers.Helpers.Option['HELPERS_LIBRARY_PATH'] + '/HelpersAjax/Content/Captcha.php',
				arCaptchaReplaces = [], i, arData, captcha;

			if (arCaptchaSections.length) {
				for (i = 0; i < arCaptchaSections.length; ++i) {
					arData = arCaptchaSections[i].dataset;

					if (undefined == arData.id) {
						this.debugMessage.push('Missing Captcha dataset ID');
					} else {
						arCaptchaReplaces.push(arData.id);
					}
				}

				if (arCaptchaReplaces.length) {
					$.ajax({
						url : ajaxPath,
						type : "POST",
						data : {
							'mode' : "GET_LIST",
							'IDs' : arCaptchaReplaces
						},
						success : BX.delegate(function(response) {
							var response = JSON.parse(response);

							if ('success' == response.status) {

								for (i = 0; i < arCaptchaSections.length; ++i) {
									arData = arCaptchaSections[i].dataset;
									captcha = response.result[arData.id];

									if (undefined != captcha) {
										arCaptchaSections[i].parentNode.innerHTML = captcha;
									}
								}

								/**
								 * Обновление событий
								 */
								BX.SavitskyiHub.BxHelpers.Helpers.Content.Form.FormUpdateEvent();

							} else {
								this.debugMessage.push(response.message);
							}
						}, this)
					});
				}
			}
		},

		/**
		 * Производит обновление предыдущей Captcha
		 *
		 * @param node
		 */
		refreshCaptcha : function(node) {
			'use strict';

			var ajaxPath = BX.SavitskyiHub.BxHelpers.Helpers.Option['HELPERS_LIBRARY_PATH'] + '/HelpersAjax/Content/Captcha.php',
				oldCaptcha = node, newCaptcha;

			$.ajax({
				url : ajaxPath,
				type : "POST",
				data : {'mode' : "GET"},
				success : BX.delegate(function(response) {
					var response = JSON.parse(response);

					if ('success' == response.status) {
						newCaptcha = response.result;

						if (undefined != newCaptcha) {
							oldCaptcha.parentNode.innerHTML = newCaptcha;
						}

					} else {
						this.debugMessage.push(response.message);
					}
				}, this)
			});
		}
	};

	/**
	 * Запускаем инициализацию объекта
	 */
	BX.ready(function() {
		BX.SavitskyiHub.BxHelpers.Helpers.Content.Base.init();
	});
})();