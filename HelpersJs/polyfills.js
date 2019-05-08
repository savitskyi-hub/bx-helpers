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
	 * Element.replaceWith
	 */
	function ReplaceWithPolyfill() {
		'use-strict';

		var parent = this.parentNode,
			i = arguments.length,
			currentNode;

		if (!parent) {
			return;
		}

		if (!i) {
			parent.removeChild(this);
		}

		while (i--) {
			currentNode = arguments[i];

			if (typeof currentNode !== 'object') {
				currentNode = this.ownerDocument.createTextNode(currentNode);
			} else if (currentNode.parentNode) {
				currentNode.parentNode.removeChild(currentNode);
			}

			if (!i) {
				parent.replaceChild(currentNode, this);
			} else {
				parent.insertBefore(this.previousSibling, currentNode);
			}
		}
	}

	if (!Element.prototype.replaceWith) {
		Element.prototype.replaceWith = ReplaceWithPolyfill;
	}

	if (!CharacterData.prototype.replaceWith) {
		CharacterData.prototype.replaceWith = ReplaceWithPolyfill;
	}

	if (!DocumentType.prototype.replaceWith) {
		DocumentType.prototype.replaceWith = ReplaceWithPolyfill;
	}

	/**
	 * Element.remove
	 */
	(function() {
		var arr = [window.Element, window.CharacterData, window.DocumentType], args = [];

		arr.forEach(function (item) {
			if (item) {
				args.push(item.prototype);
			}
		});

		(function (arr) {
			arr.forEach(function (item) {
				if (item.hasOwnProperty('remove')) {
					return;
				}

				Object.defineProperty(item, 'remove', {
					configurable: true,
					enumerable: true,
					writable: true,
					value: function remove() {
						this.parentNode.removeChild(this);
					}
				});
			});
		})(args);
	})();

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
})(window);