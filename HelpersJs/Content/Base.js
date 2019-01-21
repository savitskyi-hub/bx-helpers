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
				this.autoReplaceCallto2TellInLink();
			}
		},

		/**
		 * При включении режима правки система автоматически набрасывает технические html блоки которые нужны для визуального
		 * представления или выделения компонентов. Если в верстке идет разметка методом Flex блоков, то при влючении режима правки все собьется,
		 * а дописывать везде дополнительные свойства лишняя робота и не нужная, для этого:
		 * - происходить поиск всех блоков за условием "areaCompBlocks";
		 * - для каждого найденего блока происходит поиск первого родителя, в которого будет скопированы свойства что указаны "editStyleList";
		 * - скопированные свойства рекурсивным методом для выбранных блоков переопределяются автоматически;
		 */
		stylizeSystemBlock : function() {
			'use strict';

			let areaCompBlocks = document.querySelectorAll('[id^="bx_incl_area_"], [id^="comp_"]'),
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
		 * - если пользователь зашел на сайт из телефона;
		 * - в атрибуте "href" присутствует "callto";
		 */
		autoReplaceCallto2TellInLink : function() {
			if (!BX.browser.IsMobile()) {
				let links = BX.findChild(BX('bx-html'), {tag : 'A'}, true, true),
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
		}
	};

	/**
	 * Запускаем инициализацию объекта
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Base.init();
})();