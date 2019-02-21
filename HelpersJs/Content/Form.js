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
		 * Получение всех значений из полей Web формы
		 *
		 * @param formNode
		 * @returns {}
		 */
		getFieldsValue : function(formNode) {
			var obResultFields = {}, tagName;

			BX.findChildren(formNode, function(el) {
				if (undefined == el.tagName || 'undefined' == el.tagName || '' == el.tagName) {
					return false;
				}

				tagName = el.tagName.toUpperCase();

				if ('INPUT' == tagName && 'submit' == el.type) {
					return false;
				} else if ('INPUT' == tagName && 'checkbox' == el.type && !el.checked) {
					return false;
				} else if ('INPUT' == tagName || 'SELECT' == tagName || 'TEXTAREA' == tagName) {
					obResultFields[el.name] = el.value;
					return true;
				}

				return false;
			}, true);

			return obResultFields;
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
//			$(document).on("click", '.form-section input[type="submit"]', function() {
//				$(this).closest("form").addClass("validation");
//			});
//
//			$(document).on("click", '.fancy-block input[type="submit"]', function() {
//				$(this).closest("form").addClass("validation");
//			});
//
//			$(document).find(".form-section input, .form-section textarea").on('keyup', function() {
//				if (this.value.length) {
//					$(this).addClass('filled');
//				} else {
//					$(this).removeClass('filled');
//				}
//			});
//
//			$(document).find(".form-section input, .form-section textarea").each(function() {
//				if (this.value.length) {
//					$(this).addClass('filled');
//				} else {
//					$(this).removeClass('filled');
//				}
//			});
		},

		/**
		 *
		 */
		submitAjaxControl : function () {
//			$(document).ajaxSend(function() {
//				disabledSubmit(true);
//			}).ajaxSuccess(function() {
//				disabledSubmit(false);
//			}).ajaxError(function() {
//				disabledSubmit(false);
//			});
		},

		/**
		 *
		 */
		submitDisabled : function() {
//			var submitFancySelector = $('.fancy-block input[type="submit"], .form-section input[type="submit"]');
//
//			submitFancySelector.each(function() {
//				if ($(this).data("disabled") === undefined) {
//					$(this).prop("disabled", val);
//				}
//			});
		}

//		/**
//		 * - инициализирует работу вывода модальных окон при нажатии на соответствующий элемент;
//		 * - автоматизирует процесс получения модальных окон;
//		 * - после вывода возможно выполнить работу своего метода (передать в атрибут строку на вызов функции);
//		 */
//		init : function() {
//		},


//		/**
//		 * Вывод сообщения об ошибке
//		 */
//		showError : function(message) {
//			var errorNode = BX.findChild(BX(this.namespace), {className : 'cccstore-form-error'}, true);
//
//			if (null != errorNode) {
//				errorNode.textContent = message;
//			}
//		},
//
//		/**
//		 * Очистка сообщения об ошибке
//		 */
//		cleanError : function() {
//			var errorNode = BX.findChild(BX(this.namespace), {className : 'cccstore-form-error'}, true);
//
//			if (null != errorNode) {
//				errorNode.textContent = '';
//			}
//		},
	};
})();