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
			this.checkFields();
			this.checkWebForm();
			this.submitAjaxControl();

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
			var formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true),
				tagName;

			if (null != formsNode) {
				formsNode.forEach(function(parentFormNode) {
					BX.findChildren(parentFormNode, function(e) {
						tagName = e.tagName;

						if (tagName && "INPUT" == tagName && "submit" == e.type) {
							//BX.bind(e, 'click', BX.delegate(function(e) { this.enableValidation(e); }, this));
							//BX.bind(e, 'touch', BX.delegate(function(e) { this.enableValidation(e); }, this));

							console.log(this);

							return true;
						}

						if (tagName && ("INPUT" == tagName || "TEXTAREA" == tagName)) {
//							if (e.value.length) {
//								BX.addClass(e, 'hellpers-filled');
//							} else {
//								BX.removeClass(e, 'hellpers-filled');
//							}
							console.log(e.value);
							e.value.length? BX.addClass(e, 'hellpers-filled') : BX.removeClass(e, 'hellpers-filled');

							BX.bind(e, 'keyup', BX.delegate(function(e2) {
								e2.value.length? BX.addClass(e2, 'hellpers-filled') : BX.removeClass(e2, 'hellpers-filled');

//								if (e.value.length) {
//									BX.addClass(e2, 'hellpers-filled');
//								} else {
//									BX.removeClass(e2, 'hellpers-filled');
//								}
							}, this));
						}

						return false;
					}, true);
				});
			}
		},

		/**
		 *
		 */
		enableValidation : function(submitNode) {
			//			$(document).on("click", '.helpers-form input[type="submit"]', function() {
			//				$(this).closest("form").addClass("helpers-validation");
			//			});
		},

		/**
		 * Инициализирует обработчики при выполнении Ajax запроса, для блокировок и разблокировок кнопок отправки Web форм
		 * (чтобы, при медленном ответе не дублировать отправку нагружая на канал повторными запросами)
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
		 * Блокирование всех кнопок отправки данных из Web формы до получения результата (чтобы не отправлять дубли)
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
		 * Выполняет разблокировку ранее заблокированных кнопок для отправки данных Web формы
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