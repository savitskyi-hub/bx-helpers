/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Popup');

(function() {
	'use strict';

	/**
	 * Объект для работы с модальными окнами
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Popup = {
		/**
		 * - инициализирует работу вывода модальных окон при нажатии на соответствующий элемент;
		 * - работает совместно с FancyBox;
		 * - автоматизирует процесс получения модальных окон;
		 * - после вывода возможно выполнить работу своего метода (передать в атрибут строку на вызов функции);
		 */
		init : function() {
			BX.bindDelegate(HTML, 'click', {attribute : 'data-fancy-helpers'}, BX.delegate(function(e) {
				var obData = e.toElement.dataset, popup;

				/**
				 * Если попап отсутствует на странице
				 */
				if (undefined != obData.fancyCccstore && null == (popup = BX(obData.fancyCccstore))) {
					this.startLoader();

					setTimeout(BX.delegate(function(response) {
						this.get(obData);
						this.finishLoader();
					}, this), 75);
				} else {
					this.show(popup, obData.fanfyAfterInit, obData.fancyBeforeInit);
				}
			}, this));
		},

		/**
		 * Получение контента из шаблонов компонента
		 */
//		get : function(prop) {
//			BX.ajax({
//				url : '/bitrix/services/main/ajax.php?mode=class&c=cccstore:content.get&action=controller',
//				data : {
//					SITE_ID : BX.SavitskyiHub.BxHelpers.Helpers.Option.SITE_ID,
//					sessid : BX.bitrix_sessid(),
//					post : {
//						templateName : 'popup.' + prop.fancyCccstore,
//						logicMode : 'get',
//						prop : prop
//					}
//				},
//				method : 'POST',
//				dataType : 'json',
//				timeout : 30,
//				async : false,
//				cache : false,
//				onsuccess : BX.delegate(function(response) {
//					if ('success' === response.status) {
//						if (response.data.content) {
//							var doc, popup, popupsBlock;
//
//							doc = new DOMParser().parseFromString(response.data.content, "text/html");
//							popup = BX.findChild(doc, {attribute : {"id" : prop.fancyCccstore}}, true);
//							popupsBlock = BX.findChild(HTML, {attribute : {"data-content" : "POPUPS"}}, true);
//
//							/**
//							 * Добавляем в DOM чтобы следующий раз не делать запрос
//							 */
//							popupsBlock.appendChild(popup);
//
//							/**
//							 * Выводим попап
//							 */
//							this.show(popup, prop.fanfyAfterInit, prop.fanfyBeforeInit);
//						}
//					} else {
//						console.error(response.errors);
//					}
//				}, this)
//			});
//		},

		/**
		 * Показ контента в модальном окне
		 */
		show : function(content, afterInit, beforeInit) {
			$.fancybox.open(content, {
				animationEffect : "fade",
				autoFocus : false,
				trapFocus : false,
				touch : false,
				baseTpl : '' +
				'<div class="fancybox-container">' +
					'<div class="fancybox-bg"></div>' +
					'<div class="fancybox-inner"><div class="fancybox-stage"></div></div>' +
				'</div>',
				btnTpl : {
					smallBtn : '' +
					'<button class="fancy-cccstore-close" data-fancybox-close>' +
						'<div class="icon g-close"></div>' +
					'</button>'
				},
				beforeShow : function() {
					if (undefined != beforeInit) {
						eval(beforeInit);
					}
				},
				afterShow : function() {
					if (undefined != afterInit) {
						eval(afterInit);
					}
				}
			});
		},

		/**
		 * Возвращает ссылку на крутилку
		 */
		getLoader : function() {
			return BX.findChild(BX('bx-html'), {className : 'fancy-helpers-loader-screen'}, true);
		},

		/**
		 * Запускает крутилку
		 */
		startLoader : function() {
			var loader = this.getLoader();

			new BX.easing({
				duration : 150,
				start : {opacity : 0},
				finish : {opacity : 87},
				step : function(state) {
					loader.style.opacity = state.opacity / 100;
					loader.style.display = "block";
				}
			}).animate();
		},

		/**
		 * Останавливает крутилку
		 */
		finishLoader : function() {
			var loader = this.getLoader();

			new BX.easing({
				duration : 450,
				start : {opacity : 87},
				finish : {opacity : 0},
				step : function(state) {loader.style.opacity = state.opacity / 100;},
				complete : function() {loader.style.display = "none";}
			}).animate();
		}
	};
})();