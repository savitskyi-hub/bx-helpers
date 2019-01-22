/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function() {
	'use strict';

	/**
	 * matches
	 */
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.matchesSelector ||
			Element.prototype.webkitMatchesSelector ||
			Element.prototype.mozMatchesSelector ||
			Element.prototype.msMatchesSelector;
	}

	/**
	 * closest
	 */
	if (!Element.prototype.closest) {
		Element.prototype.closest = function(css) {
			var node = this;

			while (node) {
				if (node.matches(css)) {
					return node;
				} else {
					node = node.parentElement;
				}
			}

			return null;
		};
	}
})();