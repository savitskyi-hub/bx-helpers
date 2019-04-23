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

		/**
		 * Инициализирует работу валидации и контролем поляй у Web формах
		 */
		FormInit : function() {
			this.FormCheckFields();
			this.FormCheckWebForm();
			this.FormSubmitAjaxControl();

			/**
			 * После подгрузки данных с помощью композита события на элементы будут потеряны, нужно их перезапустить
			 */
			BX.addCustomEvent('onajaxsuccessfinish', BX.delegate(function() {
				this.FormCheckFields();
				this.FormCheckWebForm();
			}, this));
		},

		/**
		 * Получение всех значений из полей Web формы
		 *
		 * @param formNode
		 * @returns {}
		 */
		FormGetFieldsValue : function(formNode) {
			return BX.ajax.prepareForm(formNode).data;
		},

		/**
		 * Получение формы как элемента DOM дерева
		 */
		FormGetFormNode : function() {
			if (undefined != this.namespace) {
				var formNode = BX.findChild(BX('bx-html'), {tag : 'FORM', attrs : {'name' : this.namespace}}, true);

				if (null != formNode) {
					return formNode;
				}
			}

			return null;
		},

		/**
		 * Проверка полей Web формы на актуальность данных
		 */
		FormCheckFields : function() {
			var arFields = [], tagName, formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true);

			if (null != formsNode) {
				formsNode.forEach(BX.delegate(function(parentFormNode) {
					BX.findChildren(parentFormNode, BX.delegate(function(e) {
						tagName = e.tagName;

						if (undefined != tagName) {
							if ("INPUT" == tagName || "TEXTAREA" == tagName || "SELECT" == tagName) {
								if (e.required || (e.pattern && e.pattern.length)) {
									if ("INPUT" == tagName && 0 > ["text", "email", "number", "password", "hidden", "radio"].indexOf(e.type)) {
										return false;
									}

									arFields.push(e);
								}
							}
						}

					}, this), true);
				}, this));

				arFields.forEach(function(e) {
					/**
					 * Отключаем автодополнение
					 */
					if ("SELECT" != e.tagName) {
						$(e).attr('autocomplete', 'off');
					}

					/**
					 * Избавляемся от браузерных HTML5 подсказок
					 */
					BX.bind(e, 'invalid', BX.delegate(function(ev) {
						return ev.preventDefault();
					}, this));

					/**
					 * Отмечаем валидацию
					 */
					if ("Y" != BX.data(e, 'helpers-filled')) {
						BX.data(e, 'helpers-filled', "Y");
						BX.bind(e, 'keyup', BX.delegate(function() {
							if (!BX.hasClass(e, 'helpers-filled') && e.value.length) {
								BX.addClass(e, 'helpers-filled');
							} else if (!e.value.length) {
								BX.removeClass(e, 'helpers-filled');
							}
						}, this));
					}
				});
			}
		},

		/**
		 * Проверка полной Web формы
		 */
		FormCheckWebForm : function () {
			var tagName, formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true);

			if (null != formsNode) {
				formsNode.forEach(BX.delegate(function(parentFormNode) {
					BX.findChildren(parentFormNode, BX.delegate(function(e) {
						tagName = e.tagName;

						/**
						 * При отправке осуществляем валидацию всех полей
						 */
						if (undefined != tagName && "INPUT" == tagName && "submit" == e.type) {
							BX.bind(e, 'click', BX.delegate(function() { this.FormEnableValidation(e); }, this));
							BX.bind(e, 'touch', BX.delegate(function() { this.FormEnableValidation(e); }, this));
						}

						/**
						 * Производим добавление отметки что поле было проверено
						 */
						if (undefined != tagName && ("INPUT" == tagName || "TEXTAREA" == tagName)) {
							e.value.length? BX.addClass(e, 'helpers-filled') : BX.removeClass(e, 'helpers-filled');

							BX.bind(e, 'keyup', BX.delegate(function() {
								e.value.length? BX.addClass(e, 'helpers-filled') : BX.removeClass(e, 'helpers-filled');
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
		FormEnableValidation : function(submitNode) {
			var formNode = BX.findParent(submitNode, {tag : 'FORM'}, true),
				errorField = this.FormGetFieldError(formNode);

			if (undefined != errorField) {
				if (this.FormIsEnabledToScrollFieldError(formNode)) {
					this.FormScrollToErrorField(errorField);
				}
			}

			if (null != formNode) {
				BX.addClass(formNode, 'helpers-validation');
			}
		},

		/**
		 * Получение поля с ошибкой
		 */
		FormGetFieldError : function(formNode) {
			var errorFields = BX.findChildren(formNode, {className : 'helpers-form-field-error'}, true),
				returnField;

			errorFields.forEach(function(e){
				if ('none' != getComputedStyle(e).display) {
					returnField = e;
				}
			});

			return returnField;
		},

		/**
		 * Нужно ли производить скролл
		 */
		FormIsEnabledToScrollFieldError : function(formNode) {
			return "Y" == BX.data(formNode, 'helpers-form-scroll2error')? true : false;
		},

		/**
		 * Выполняет плавный скролл к полю с ошибкой
		 */
		FormScrollToErrorField : function(errorField) {
			var errorFieldPost = errorField.parentNode.getBoundingClientRect(),
				windowPos = BX.GetWindowScrollPos(),
				lastPosition = errorFieldPost.top + windowPos.scrollTop - (errorFieldPost.height  / 2);

			(new BX.easing({
				duration : 700,
				start : {scroll : windowPos.scrollTop},
				finish : {scroll : lastPosition},
				step : function(state) {window.scrollTo(0, state.scroll);}
			})).animate();
		},

		/**
		 * Инициализирует обработчики при выполнении Ajax запроса, для блокировок и разблокировок кнопок отправки Web форм
		 * (чтобы, при медленном ответе не дублировать отправку нагружая на канал повторными запросами)
		 */
		FormSubmitAjaxControl : function () {
			$(document).ajaxSend(BX.delegate(function() {
				this.FormSubmitDisabled();
			}, this)).ajaxSuccess(BX.delegate(function() {
				this.FormSubmitEnabled();
			}, this)).ajaxError(BX.delegate(function() {
				this.FormSubmitEnabled();
			}, this));
		},

		/**
		 * Блокирование всех кнопок отправки данных из Web формы до получения результата (чтобы не отправлять дубли)
		 */
		FormSubmitDisabled : function() {
			var formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true),
				submitsNode;

			if (null != formsNode) {
				formsNode.forEach(function(parentFormNode) {
					submitsNode = BX.findChild(parentFormNode, {tag : 'INPUT', attrs : {'type' : 'submit'}}, true);

					if (null != submitsNode) {
						submitsNode.disabled = true;
					}
				});
			}
		},

		/**
		 * Выполняет разблокировку ранее заблокированных кнопок для отправки данных Web формы
		 */
		FormSubmitEnabled : function() {
			var formsNode = BX.findChildren(BX('bx-html'), {className : 'helpers-form'}, true),
				submitsNode;

			if (null != formsNode) {
				formsNode.forEach(function(parentFormNode) {
					submitsNode = BX.findChild(parentFormNode, {tag : 'INPUT', attrs : {'type' : 'submit'}}, true);

					if (null != submitsNode) {
						submitsNode.disabled = false;
					}
				});
			}
		},

		/**
		 * Вывод сообщения об ошибке
		 */
		FormShowError : function(message) {
			var errorNode, formNode = this.FormGetFormNode();

			if (null != formNode) {
				errorNode = BX.findChild(formNode, {className : 'helpers-form-error'}, true);

				if (null != errorNode) {
					errorNode.textContent = message;
				}
			}
		},

		/**
		 * Очистка сообщения об ошибке
		 */
		FormCleanError : function() {
			var errorNode, formNode = this.FormGetFormNode();

			if (null != formNode) {
				errorNode = BX.findChild(formNode, {className : 'helpers-form-error'}, true);

				if (null != errorNode) {
					errorNode.textContent = '';
				}
			}
		},

		/**
		 * Обновление Captcha
		 */
		FormRefreshCaptcha : function() {
			var oldCaptcha, formNode = this.FormGetFormNode();

			if (null != formNode) {
				oldCaptcha = BX.findChild(formNode, {className : 'helpers-form-captcha'}, true);

				if (null != oldCaptcha) {
					BX.SavitskyiHub.BxHelpers.Helpers.Content.Base.refreshCaptcha(oldCaptcha);
				}
			}
		},

		/**
		 * Замена Web формы на новый контент
		 */
		FormReplaceContent : function(content) {
			var tmpNode, parentNode, formNode = this.FormGetFormNode();

			if (formNode.outerHTML) {
				formNode.outerHTML = content;
			} else {
				tmpNode = BX.create("DIV", {html : '<!--THIS DATA SHOULD BE REPLACED-->'});
				parentNode = formNode.parentNode;

				parentNode.replaceChild(tmpNode, formNode);
				parentNode.innerHTML = parentNode.innerHTML.replace('<div><!--THIS DATA SHOULD BE REPLACED--></div>', content);
			}
		},

		/**
		 * Обновление глобальных событий (для нового контента)
		 */
		FormUpdateEvent : function() {
			setTimeout(BX.delegate(function() {
				this.FormCheckWebForm();
				this.FormCheckFields();

				if ('function' === typeof(helpersContenFormUpdateEvent)) {
					helpersContenFormUpdateEvent();
				}
			}, this), 250);
		}
	};

	/**
	 * Запускаем инициализацию объекта
	 */
	BX.ready(function() {
		BX.SavitskyiHub.BxHelpers.Helpers.Content.Form.FormInit();
	});
})();