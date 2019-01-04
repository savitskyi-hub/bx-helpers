/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Text');

(function() {
	'use strict';

	/**
	 * Объект для работы с текстом (полезные методы для решения многих задач)
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Text = {
		/**
		 * Скопировать весь текст внутри элемента
		 *
		 * @param {node} el
		 * @returns {string}
		 */
		getFullTextInNode : function(el) {
			'use strict';

			let n, a = '', walk = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);

			while (n = walk.nextNode()) {
				a += n.textContent;
			}

			return a;
		},

		/**
		 * Задать любой текст в буфер обмена
		 *
		 * @param {string} text
		 */
		copyTextToClipboard : function(text) {
			'use strict';

			let textArea = document.createElement("textarea"), successful, msg;

			textArea.style.position = 'fixed';
			textArea.style.top = 0;
			textArea.style.left = 0;
			textArea.style.width = '2em';
			textArea.style.height = '2em';
			textArea.style.padding = 0;
			textArea.style.border = 'none';
			textArea.style.outline = 'none';
			textArea.style.boxShadow = 'none';
			textArea.style.background = 'transparent';
			textArea.value = text;

			document.body.appendChild(textArea);

			textArea.focus();
			textArea.select();

			try {
				successful = document.execCommand('copy');
				msg = successful ? 'successful' : 'unsuccessful';
			} catch (err) {
				console.log('Oops, unable to copy');
			}

			document.body.removeChild(textArea);
		}
	};
})();