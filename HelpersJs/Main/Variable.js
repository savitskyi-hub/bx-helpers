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

	if (!!window.BX.SavitskyiHub.BxHelpers.Helpers.Main.Variable) {
		return;
	}

	BX.namespace('SavitskyiHub.BxHelpers.Helpers.Main.Variable');

	/**
	 * Объект для удобной работы с свойствами или их обработки, которые нужны при реализации или поддержки проекта
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Main.Variable = {
		/**
		 * Очищает массив от всех ложных (что == false) значений
		 *
		 * @param arr
		 */
		cleanFalseInArray : function(arr) {
			var newArray = new Array(),
				i = 0;

			for (i; i < arr.length; i++) {
				if (arr[i]) {
					newArray.push(arr[i]);
				}
			}

			return newArray;
		}
	};
})(window);