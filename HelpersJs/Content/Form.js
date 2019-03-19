/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Form');

(function() {
	'use strict';

	/**
	 * Объект для работы с Web формами
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Form = {
		/**
		 * Для отладки собираем информацию
		 */
		debugMessage : [],

		//		/**
		//		 * - инициализирует работу вывода модальных окон при нажатии на соответствующий элемент;
		//		 * - автоматизирует процесс получения модальных окон;
		//		 * - после вывода возможно выполнить работу своего метода (передать в атрибут строку на вызов функции);
		//		 */
		init : function() {

			//this.checkFields();
			//this.checkWebForm();
			//this.submitAjaxControl();

			//			BX.addCustomEvent('onajaxsuccessfinish', function() {

			// После композита JS события на елементы будут утрачены, нужно их перезапустить самостоятельно
			//				setTimeout(function() {
			//					this.checkFields();
			//                  this.checkWebForm();
			//				}, 250);

			//			});
		},

		/**
		 * Получение всех значений из полей Web формы
		 *
		 * @param formNode
		 * @returns {}
		 */
		getFieldsValue : function(formNode) {
			return BX.ajax.prepareForm(formNode).data;
		},

		/**
		 *
		 */
		checkFields : function() {
			//			var typeFields = ["text", "number", "email", "password", "url"],
			//				selectors = "textarea:required, textarea[pattern], ";
			//
			//			for (var i = 0; i <= type_sybmit.length; ++i) {
			//				selectors += 'input[type="' + type_sybmit[i] + '"]:required, ';
			//				selectors += 'input[type="' + type_sybmit[i] + '"][pattern], ';
			//			}
			//
			//			selectors = selectors.substring(0, selectors.length - 2);
			//
			//			if ($(selectors).length) {
			//				$(selectors).off();
			//			}
			//
			//			$(selectors).each(function(i, el) {
			//
			//				// Remove html5 message
			//				$(el).on("invalid", function(event) { return false; });
			//
			//				$(el).keyup(function() {
			//					if (!$(this).hasClass("validation") && $(this).val()) {
			//						$(this).addClass("validation");
			//					} else {
			//
			//						if (!$(this).val()) {
			//							$(this).removeClass("validation");
			//						}
			//
			//					}
			//				});
			//			});
		},

		/**
		 *
		 */
		checkWebForm : function () {
			//			$(document).on("click", '.helpers-form input[type="submit"]', function() {
			//				$(this).closest("form").addClass("helpers-validation");
			//			});
			//
			//			$(document).on("click", '.helpers-form input[type="submit"]', function() {
			//				$(this).closest("form").addClass("helpers-validation");
			//			});
			//
			//			$(document).find(".helpers-form input, .helpers-form textarea").on('keyup', function() {
			//				if (this.value.length) {
			//					$(this).addClass('helpers-filled');
			//				} else {
			//					$(this).removeClass('helpers-filled');
			//				}
			//			});
			//
			//			$(document).find(".helpers-form input, .helpers-form textarea").each(function() {
			//				if (this.value.length) {
			//					$(this).addClass('hellpers-filled');
			//				} else {
			//					$(this).removeClass('hellpers-filled');
			//				}
			//			});
		},

		/**
		 * !!!!!!!!!!!!!При выполнении Ajax запроса блокируем все кнопки в web формах до получения результата, чтобы при медленном ответе не дублировать отправку нагружая на канал повторными запросами, ну и чтобы повторною отправку запретить
		 */
		submitAjaxControl : function () {
			$(document).ajaxSend(BX.delegate(function() {
				this.submitDisabled();
			}, this)).ajaxSuccess(BX.delegate(function() {
				this.submitEnabled();
			}, this)).ajaxError(BX.delegate(function() {
				this.submitEnabled();
			}, this));
		},

		/**
		 * !!!!!!!!!!!!!Блокирование
		 */
		submitDisabled : function() {
			var formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true),
				submitsNode;

			if (null != formsNode) {
				formsNode.forEach(function(parentFormNode) {
					submitsNode = BX.findChild(parentFormNode, {tag : 'INPUT', attrs : {'type' : 'submit'}}, true);

					if (null != submitsNode) {
						submitsNode.forEach(function(e) {
							e.disabled = true;
						});
					}
				});
			}
		},

		/**
		 * !!!!!!!!!!!Разблокирование
		 */
		submitEnabled : function() {
			var formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true),
				submitsNode;

			if (null != formsNode) {
				formsNode.forEach(function(parentFormNode) {
					submitsNode = BX.findChild(parentFormNode, {tag : 'INPUT', attrs : {'type' : 'submit'}}, true);

					if (null != submitsNode) {
						submitsNode.forEach(function(e) {
							e.disabled = false;
						});
					}
				});
			}
		},

		/**
		 * Вспомогательный объект для наследования в дочерние экземпляры Web форм, самостоятельно не используется
		 */
		obHelpers : {

			// вместо BX(this.namespace) сделать поиск на форму по name
			//			/**
			//			 * Установка нового контента
			//			 */
			//			setContent : function(content) {
			//				var thisContent = BX.findChild(BX(this.namespace), {className : 'helpers-fancy-content'}, true);
			//
			//				if (null != thisContent) {
			//					thisContent.innerHTML = content;
			//				}
			//			},

			/**
			 * Вывод сообщения об ошибке
			 */
			showError : function(message) {
//				var errorNode = BX.findChild(BX('bx-html'), {className : 'helpers-form-error'}, true);
//
//				if (null != errorNode) {
//					errorNode.textContent = message;
//				}
			},

			//			/**
			//			 * Очистка сообщения об ошибке
			//			 */
			//			cleanError : function() {
			//				var errorNode = BX.findChild(BX(this.namespace), {className : 'helpers-form-error'}, true);
			//
			//				if (null != errorNode) {
			//					errorNode.textContent = '';
			//				}
			//			},

			//			/**
			//			 * Обновление Captcha
			//			 */
			//			refreshCaptcha : function() {
			//				var oldCaptcha = BX.findChild(BX(this.namespace), {className : 'helpers-form-captcha'}, true);
			//
			//				if (null != oldCaptcha) {
			//					BX.SavitskyiHub.BxHelpers.Helpers.Content.Base.refreshCaptcha(oldCaptcha);
			//				}
			//			}
		}
	};

	/**
	 * Запускаем инициализацию объекта
	 */
	BX.ready(function() {
		BX.SavitskyiHub.BxHelpers.Helpers.Content.Form.init();
	});
})();