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

	if (!!window.BX.SavitskyiHub.BxHelpers.Helpers.Content.FixedBlockOnScroll) {
		return;
	}

	BX.namespace("SavitskyiHub.BxHelpers.Helpers.Content.FixedBlockOnScroll");

	/**
	 * Реализация фиксирования блоков при скроллинге, само фиксирование начинается от момента:
	 * - когда верхняя черта экрана доходит к блоку который должен быть зафиксирован;
	 * - когда есть достаточно места для вертикального скроллинга;
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.FixedBlockOnScroll = {
		/**
		 * Свойство содержит параметры которые необходимы для реализации фиксирования блоков при скроллинге:
		 *
		 * @param string container - селектор родительського блока от которого будет выполнены необходимые вычисления (авто определение ширины блоков и высоты основного родителя);
		 * @param string scrollBlock - селектор блока который будет фиксироватся при скроллинге (название класса должно включать - "helpers-scroller-y");
		 * @param string parentStaticBlock - селектор статического блока что остается на месте при скроллинге (используется для получения первоначальных значений);
		 * @param string who
		 * @param string who2to - селектор, относительно которого будет происходить фиксирование;
		 * @param bool|string scrollingTag - селектор, для вычисления позиции скроллинга (бывают задачи когда нужно реализовать в самом попапе или в блоке из своим активным скроллбаре);
		 * @param int stopOnWidth - минимально возможная ширина экрана до которой будет работать функционал;
		 */
		obListFixedBlock : {},

		/**
		 * Для отладки собираем информацию
		 */
		debugMessage : [],

		/**
		 * Инициализирует работу фиксированых блоков которые будут фиксироваться при достижении условия, что определены в свойстве "obListFixedBlock"
		 *
		 * @param obInitItem - если нужно инициализировать работу отдельного блока, можно передать его с помощью параметра
		 */
		init : function(obInitItem) {
			'use strict';

			var obList = {},
				obFixedListBlock = {},
				arFunction = [],
				arFuncProp = {},
				j, p, prop,
				containerNode, scrollBlockNode, parentStaticBlockNode, whoNode, who2toNode, scrollingTagNode;

			if (undefined !== obInitItem && BX.type.isPlainObject(obInitItem)) {
				obList = obInitItem;
			} else if (BX.type.isPlainObject(this.obListFixedBlock) && BX.type.isNotEmptyObject(this.obListFixedBlock)) {
				obList = this.obListFixedBlock;
			}

			if (BX.type.isNotEmptyObject(obList)) {
				for (p in obList) {
					prop = obList[p];

					containerNode = document.querySelector(prop.container);
					scrollBlockNode = document.querySelector(prop.scrollBlock);
					parentStaticBlockNode = document.querySelector(prop.parentStaticBlock);
					whoNode = document.querySelector(prop.who);
					who2toNode = document.querySelector(prop.who2to);
					scrollingTagNode = document.querySelector(prop.scrollingTag);

					if (!obList.hasOwnProperty(p)) {
						continue;
					} else if (!containerNode) {
						this.debugMessage.push('Selector "' + prop.container + '" is not find');
						continue;
					} else if (!scrollBlockNode) {
						this.debugMessage.push('Selector "' + prop.scrollBlock + '" is not find');
						continue;
					} else if (!parentStaticBlockNode) {
						this.debugMessage.push('Selector "' + prop.parentStaticBlock + '" is not find');
						continue;
					} else if (!whoNode) {
						this.debugMessage.push('Selector "' + prop.who + '" is not find');
						continue;
					} else if (!who2toNode) {
						this.debugMessage.push('Selector "' + prop.who2to + '" is not find');
						continue;
					} else if (typeof(prop.scrollingTag) == "boolean" && prop.scrollingTag) {
						this.debugMessage.push('Param "scrollingTag" in method must be empty');
						continue;
					} else if (typeof(prop.scrollingTag) == "string" && !scrollingTagNode) {
						this.debugMessage.push('Selector "' + prop.scrollingTag + '" is not find');
						continue;
					}

					/**
					 * Устанавливаем минимальную высоту родительскому блоку
					 */
					containerNode.style.minHeight = scrollBlockNode.offsetHeight + 'px';

					/**
					 * Создадим анонимные функции для каждого фиксированного блока, что будут выполнятся при сколлинге страницы
					 */
					arFunction[p] = function(key) {
						arFuncProp = obList[key];

						var scrollBlockNode = document.querySelector(arFuncProp.scrollBlock),
							containerNode = document.querySelector(arFuncProp.container),
							parentStaticBlockNode = document.querySelector(arFuncProp.parentStaticBlock),
							whoNode = document.querySelector(arFuncProp.who),
							who2toNode = document.querySelector(arFuncProp.who2to);

						var namespace = BX.SavitskyiHub.BxHelpers.Helpers.Content.FixedBlockOnScroll,
							scrollTop, scrollBlockCompensateMargin, scrollBlockHight, scrollBlockOffetTop, scrollBlockOffetBottom,
							containerCompensateMargin, containerHeight, containerOffsetTop, containerOffsetBottom,
							parentStaticBlockWidth, parentStaticOffsetTop,
							isGoodStructure;

						/**
						 * Останавливаем работу
						 */
						if (window.innerWidth < arFuncProp.stopOnWidth) {
							return;
						} else if (!scrollBlockNode) {
							return;
						}

						// Scroll block
						scrollBlockCompensateMargin = namespace.getMarginCompensate(scrollBlockNode);
						scrollBlockHight = scrollBlockNode.offsetHeight;
						scrollBlockOffetTop = Math.floor(scrollBlockNode.getBoundingClientRect().top + scrollBlockCompensateMargin);
						scrollBlockOffetBottom = scrollBlockOffetTop + scrollBlockHight;

						// Container
						containerCompensateMargin = namespace.getMarginCompensate(containerNode);
						containerHeight = containerNode.offsetHeight;
						containerOffsetTop = Math.floor(containerNode.getBoundingClientRect().top + containerCompensateMargin);
						containerOffsetBottom = parseInt(containerOffsetTop + containerHeight);

						// Static position
						parentStaticBlockWidth = parentStaticBlockNode.offsetWidth;
						parentStaticOffsetTop = Math.floor(parentStaticBlockNode.getBoundingClientRect().top + containerCompensateMargin);

						// Interactive
						isGoodStructure = (containerHeight > scrollBlockHight && whoNode.offsetHeight < who2toNode.offsetHeight);

						// To fixed
						if (0 >= scrollBlockOffetTop && isGoodStructure) {
							scrollBlockNode.classList.add("fixed-start");
						}

						// Return default Top stop
						if (parentStaticOffsetTop > scrollBlockOffetTop) {
							scrollBlockNode.classList.remove("fixed-start");
							scrollBlockNode.removeAttribute("style");
						}

						// Stop bottom
						if (scrollBlockOffetBottom >= containerOffsetBottom && isGoodStructure) {
							scrollBlockNode.classList.add("fixed-stop");
							scrollBlockNode.style.top = parseInt(containerHeight - scrollBlockHight) + "px";
						} else if (!scrollBlockNode.classList.contains("fixed-stop")) {

							if (scrollBlockHight > containerOffsetBottom) {
								scrollBlockNode.classList.add("fixed-stop");
								scrollBlockNode.style.top = parseInt(containerHeight - scrollBlockHight) + "px";
							}

						}

						// Remove stop bottom
						if (scrollBlockNode.classList.contains("fixed-stop") && 0 < scrollBlockOffetTop) {
							scrollBlockNode.classList.remove("fixed-stop");
							scrollBlockNode.removeAttribute("style");
						}

						// Width fixed
						if (scrollBlockNode.classList.contains("fixed-start") && !scrollBlockNode.classList.contains("fixed-stop")) {
							scrollBlockNode.style.width = parentStaticBlockWidth + "px";
						}
					};
				}

				/**
				 * Регистрируем обработчик события для каждого блока на прокрутку и ресайз страницы
				 */
				if (typeof(arFunction) !== "undefined") {
					for (j in arFunction) {
						if (!obList[j].scrollingTag) {
							window.addEventListener("scroll", function() { arFunction[j](j); });
						} else {
							document.querySelector(obList[j].scrollingTag).onscroll = function() { arFunction[j](j); };
						}

						window.addEventListener("resize", function() { arFunction[j](j); });
					}
				}
			}
		},

		/**
		 * Компенсация позиционирования если есть верхний глобальный отступ
		 *
		 * @param elNode
		 */
		getMarginCompensate : function(elNode) {
			var marginTop = parseInt(getComputedStyle(elNode)['margin-top']);

			if (marginTop) {

				if (marginTop > 0) {
					return marginTop * -1;
				} else if (marginTop < 0) {
					return marginTop;
				} else {
					return 0;
				}

			} else {
				return 0;
			}
		}
	};
})(window);