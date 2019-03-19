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
			BX.bindDelegate(BX('bx-html'), 'click', {attribute : 'data-fancy-helpers'}, BX.delegate(function(e) {
				var obData = e.toElement.dataset, popup;

				/**
				 * Если попап отсутствует на странице
				 */
				if (undefined != obData.fancyHelpers && null == (popup = BX(obData.fancyHelpers))) {
					this.startLoader();

					setTimeout(BX.delegate(function(response) {
						this.get(obData);
						this.finishLoader();
					}, this), 100);
				} else {
					this.show(popup, obData.fancyHelpersAfterShow, obData.fancyHelpersBeforeShow);
				}
			}, this));
		},

		/**
		 * Получение контента из шаблонов компонента (внимание, необходим специальный компонент!!!)
		 */
		get : function(prop) {
			BX.ajax({
				url : '/bitrix/services/main/ajax.php?mode=class&c=savitskyi.helpers:content.ajax&action=controller',
				data : {
					SITE_ID : BX.SavitskyiHub.BxHelpers.Helpers.Option.SITE_ID,
					sessid : BX.bitrix_sessid(),
					post : {
						templateName : 'popup.' + prop.fancyHelpers,
						logicMode : 'get',
						prop : prop
					}
				},
				method : 'POST',
				dataType : 'json',
				async : false,
				onsuccess : BX.delegate(function(response) {
					if ('success' === response.status) {
						if (response.data.content) {
							var doc, popup, popupsBlock;

							doc = new DOMParser().parseFromString(response.data.content, "text/html");
							popup = BX.findChild(doc, {attribute : {"id" : prop.fancyHelpers}}, true);
							popupsBlock = BX.findChild(BX('bx-html'), {attribute : {"data-content" : "POPUPS"}}, true);

							/**
							 * Добавляем в DOM чтобы следующий раз не делать запрос
							 */
							popupsBlock.appendChild(popup);

							/**
							 * Выводим попап
							 */
							this.show(popup, prop.fancyHelpersAfterShow, prop.fancyHelpersBeforeShow);
						}
					} else {
						console.error(response.errors);
					}
				}, this)
			});
		},

		/**
		 * Показ контента в модальном окне
		 */
		show : function(content, afterShowInit, beforeShowInit) {
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
					'<button class="fancybox-close" data-fancybox-close>' +
						'<div class="icon g-close-fancy"></div>' +
					'</button>'
				},
				beforeShow : function() {
					if (undefined != beforeShowInit) {
						eval(beforeShowInit);
					}

					if ('function' === typeof(helpersBeforeShowPopup)) {
						helpersBeforeShowPopup();
					}
				},
				afterShow : function() {
					if (undefined != afterShowInit) {
						eval(afterShowInit);
					}

					if ('function' === typeof(helpersAfterShowPopup)) {
						helpersAfterShowPopup();
					}
				}
			});
		},

		/**
		 * Возвращает ссылку на крутилку
		 */
		getLoader : function() {
			return BX.findChild(BX('bx-html'), {className : 'helpers-fancy-loader-screen'}, true);
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
		},

		/**
		 * Вспомогательный объект для наследования в дочерние экземпляры попапов, самостоятельно не используется
		 */
		obHelpers : {
			/**
			 * Изменение заголовка попапа
			 */
			setTitle : function(message) {
				var thisTitle = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-title'}, true);

				if (null != thisTitle) {
					if (undefined == thisTitle.dataset.helpersStopReplaceTitle) {
						thisTitle.textContent = message;
					}
				}
			},

			/**
			 * Устанавливает "лайк" стиль заголовка
			 */
			setTitleLikeStyle : function() {
				var thisTitle = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-title'}, true);

				if (null != thisTitle) {
					BX.addClass(thisTitle, 'like');
				}
			},

			/**
			 * Установка нового контента
			 */
			setContent : function(content) {
				var thisContent = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-content'}, true);

				if (null != thisContent) {
					thisContent.innerHTML = content;
				}
			},

			/**
			 * Вывод сообщения об ошибке
			 */
			showError : function(message) {
				var errorNode = BX.findChild(BX(this.namespace), {className : 'helpers-form-error'}, true);

				if (null != errorNode) {
					errorNode.textContent = message;
				}
			},

			/**
			 * Очистка сообщения об ошибке
			 */
			cleanError : function() {
				var errorNode = BX.findChild(BX(this.namespace), {className : 'helpers-form-error'}, true);

				if (null != errorNode) {
					errorNode.textContent = '';
				}
			},

			/**
			 * Обновление Captcha
			 */
			refreshCaptcha : function() {
				var oldCaptcha = BX.findChild(BX(this.namespace), {className : 'helpers-form-captcha'}, true);

				if (null != oldCaptcha) {
					BX.SavitskyiHub.BxHelpers.Helpers.Content.Base.refreshCaptcha(oldCaptcha);
				}
			},

			/**
			 * Обновление глобальных событий (для нового контента)
			 */
			updateGlobalEvents : function() {
				BX.SavitskyiHub.BxHelpers.Helpers.Content.Form.checkFields();
				BX.SavitskyiHub.BxHelpers.Helpers.Content.Form.checkWebForm();

				if ('function' === typeof(helpersAfterShowPopup)) {
					setTimeout(function() { helpersAfterShowPopup(); }, 250);
				}
			}
		}
	};
})();