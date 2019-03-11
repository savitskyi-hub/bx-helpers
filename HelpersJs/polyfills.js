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
	 * Element.matches
	 */
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.matchesSelector ||
			Element.prototype.webkitMatchesSelector ||
			Element.prototype.mozMatchesSelector ||
			Element.prototype.msMatchesSelector;
	}

	/**
	 * Element.closest
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

	/**
	 * Object.assign
	 */
	if ('function' != typeof(Object.assign)) {
		Object.assign = function(target, varArgs) {
			var to, index, nextKey, nextSource;

			if (target == null) {
				throw new TypeError('Cannot convert undefined or null to object');
			}

			to = Object(target);

			for (index = 1; index < arguments.length; index++) {
				nextSource = arguments[index];

				if (nextSource != null) {
					for (nextKey in nextSource) {
						if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
							to[nextKey] = nextSource[nextKey];
						}
					}
				}
			}

			return to;
		};
	}
})();