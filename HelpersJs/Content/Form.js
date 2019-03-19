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
			//			this.checkFields();
			//			this.checkWebForm();
			//			this.submitAjaxControl();

			/**
			 *
			 */
			//			BX.addCustomEvent('onajaxsuccessfinish', BX.delegate(function() {

			// После композита JS события на елементы будут утрачены, нужно их перезапустить самостоятельно
			//				setTimeout(BX.delegate(function() {
			//					this.checkFields();
			//                  this.checkWebForm();
			//				}, this) 250);

			//			}, this));
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
		 * Проверка полей Web формы на актуальность данных
		 */
		checkFields : function() {
			var arFields = [], tagName, formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true);

			if (null != formsNode) {
				formsNode.forEach(BX.delegate(function(parentFormNode) {
					BX.findChildren(parentFormNode, BX.delegate(function(e) {
						tagName = e.tagName;

						if (undefined != tagName) {
							if ("INPUT" == tagName || "TEXTAREA" == tagName) {
								if (0 > ["text", "number", "password", "hidden"].indexOf(e.type)) {
									return false;
								}

								if (e.required || e.pattern.length) {
									arFields.push(e);
								}
							}
						}

					}, this), true);
				}, this));

				arFields.forEach(function(e) {
					/**
					 * Удаляем все обработчики
					 */
					$(e).off();

					/**
					 * Избавляемся от браузерных HTML5 подсказок
					 */
					BX.bind(e, 'invalid', BX.delegate(function(ev) {
						return ev.preventDefault();
					}, this));

					/**
					 * Отмечаем валидацию
					 */
					BX.bind(e, 'keyup', BX.delegate(function() {
						if (!BX.hasClass(e, 'hellpers-filled') && e.value.length) {
							BX.addClass(e, 'hellpers-filled');
						} else if (!e.value.length) {
							BX.removeClass(e, 'hellpers-filled');
						}
					}, this));
				});
			}
		},

		/**
		 * Проверка полной Web формы
		 */
		checkWebForm : function () {
			var tagName, formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true);

			if (null != formsNode) {
				formsNode.forEach(BX.delegate(function(parentFormNode) {
					BX.findChildren(parentFormNode, BX.delegate(function(e) {
						tagName = e.tagName;

						/**
						 * При отправке осуществляем валидацию всех полей
						 */
						if (undefined != tagName && "INPUT" == tagName && "submit" == e.type) {
							BX.bind(e, 'click', BX.delegate(function() { this.enableValidation(e); }, this));
							BX.bind(e, 'touch', BX.delegate(function() { this.enableValidation(e); }, this));
						}

						/**
						 * Производим добавление отметки что поле было проверено
						 */
						if (undefined != tagName && ("INPUT" == tagName || "TEXTAREA" == tagName)) {
							e.value.length? BX.addClass(e, 'hellpers-filled') : BX.removeClass(e, 'hellpers-filled');

							BX.bind(e, 'keyup', BX.delegate(function() {
								e.value.length? BX.addClass(e, 'hellpers-filled') : BX.removeClass(e, 'hellpers-filled');
							}, this));
						}

						return false;
					}, this), true);
				}, this));
			}
		},

		/**
		 * Активирует валидацию всех полей Web формы
		 */
		enableValidation : function(submitNode) {
			var formNode = BX.findParent(submitNode, {tag : 'FORM'}, true);

			if (null != formNode) {
				BX.addClass(formNode, 'helpers-validation');
			}
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

			/**
			 * Очистка сообщения об ошибке
			 */
			cleanError : function() {
				//				var errorNode = BX.findChild(BX(this.namespace), {className : 'helpers-form-error'}, true);
				//
				//				if (null != errorNode) {
				//					errorNode.textContent = '';
				//				}
			},

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