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
		PopupInit : function() {
			BX.bindDelegate(BX('bx-html'), 'click', {attribute : 'data-fancy-helpers'}, BX.delegate(function(e) {
				var obData = e.srcElement.dataset, popup;

				/**
				 * Если попап отсутствует на странице
				 */
				if (undefined != obData.fancyHelpers && null == (popup = BX(obData.fancyHelpers))) {
					this.PopupGet(obData);
				} else {
					this.PopupShow(popup, obData.fancyHelpersAfterShow, obData.fancyHelpersBeforeShow);
				}
			}, this));
		},

		/**
		 * Проверяет загружен ли уже попал в тело страницы
		 */
		PopupIsLoad : function() {
			return (null == this.PopupGetNode()? false : true);
		},

		/**
		 * Получение попапа как DOM елемента
		 */
		PopupGetNode : function() {
			return BX(this.namespace);
		},

		/**
		 * Получение контента из шаблонов компонента (внимание, необходим специальный компонент!!!)
		 */
		PopupGet : function(prop) {
			if (undefined == prop) {
				prop = {
					fancyHelpers : this.namespace,
					fancyHelpersAfterShow : undefined,
					fancyHelpersBeforeShow : undefined
				};
			}

			if (this.PopupIsLoad()) {
				this.PopupShow(this.PopupGetNode(), prop.fancyHelpersAfterShow, prop.fancyHelpersBeforeShow);
			} else {
				this.PopupStartLoader();

				setTimeout(BX.delegate(function(response) {
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
									popupsBlock = BX.findChild(BX('bx-html'), {attribute : {"data-content-helpers" : "POPUPS"}}, true);

									/**
									 * Добавляем блок в котором будут хранится подгружаемые попапы
									 */
									if (null == popupsBlock) {
										popupsBlock = BX.create("DIV", {attrs : {'class' : 'hide', 'data-content-helpers' : 'POPUPS'}});
										document.body.appendChild(popupsBlock);
									}

									/**
									 * Добавляем в DOM чтобы следующий раз не делать запрос
									 */
									popupsBlock.appendChild(popup);

									/**
									 * Выводим попап
									 */
									this.PopupFinishLoader();
									this.PopupShow(popup, prop.fancyHelpersAfterShow, prop.fancyHelpersBeforeShow);
								}
							} else {
								console.error(response.errors);
							}
						}, this)
					});

					this.PopupFinishLoader();
				}, this), 150);
			}
		},

		/**
		 * Показ контента в модальном окне
		 */
		PopupShow : function(content, afterShowInit, beforeShowInit) {
			if (undefined != content) {
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
						'<button class="helpers-fancy-close" data-fancybox-close>' +
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
			}
		},

		/**
		 * Закрытие модального окна
		 */
		PopupClose : function() {
			var thisClose = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-close'}, true);

			if (null != thisClose) {
				thisClose.click();
			}
		},

		/**
		 * Возвращает ссылку на крутилку
		 */
		PopupGetLoader : function() {
			return BX.findChild(BX('bx-html'), {className : 'helpers-fancy-loader-screen'}, true);
		},

		/**
		 * Запускает крутилку
		 */
		PopupStartLoader : function() {
			var loader = this.PopupGetLoader();

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
		PopupFinishLoader : function() {
			var loader = this.PopupGetLoader();

			new BX.easing({
				duration : 450,
				start : {opacity : 87},
				finish : {opacity : 0},
				step : function(state) {loader.style.opacity = state.opacity / 100;},
				complete : function() {loader.style.display = "none";}
			}).animate();
		},

		/**
		 * Изменение заголовка попапа
		 */
		PopupSetTitle : function(message) {
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
		PopupSetTitleLikeStyle : function() {
			var thisTitle = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-title'}, true);

			if (null != thisTitle) {
				BX.addClass(thisTitle, 'like');
			}
		},

		/**
		 * Установка нового контента
		 */
		PopupSetContent : function(content) {
			var thisContent = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-content'}, true);

			if (null != thisContent) {
				thisContent.innerHTML = content;
			}
		},

		/**
		 * Обновление глобальных событий (для нового контента)
		 */
		PopupUpdateEvent: function() {
			if ('function' === typeof(helpersContentPopupUpdateEvent)) {
				setTimeout(function() { helpersContentPopupUpdateEvent(); }, 250);
			}
		}
	};
})();