/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

BX.namespace('SavitskyiHub.BxHelpers.Helpers.Main.Variable');

(function() {
	'use strict';

	/**
	 *
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Main.Variable = {
		/**
		 *
		 *
		 * @param arr
		 */
		cleanArray : function(arr) {
			let newArray = new Array(),
				i = 0;

			for (i; i < arr.length; i++) {
				if (arr[i]) {
					newArray.push(arr[i]);
				}
			}

			return newArray;
		}
	};
});