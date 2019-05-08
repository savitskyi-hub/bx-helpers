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

	if (!!window.BX.SavitskyiHub.BxHelpers.Helpers.Content.Image) {
		return;
	}

	BX.namespace("SavitskyiHub.BxHelpers.Helpers.Content.Image");

	/**
	 * Объект для работы с изображениями
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Image = {

		/**
		 * Метод для асинхронной подгрузки изображения, что работает на базе функционала - SavitskyiHub\BxHelpers\Helpers\Content\Image
		 * - замена блоков на изображение происходить в ручном режиме;
		 * - поиск заменяемых блоков осуществляется от переданного родительского елемента, производя поиск всех его потомков для замены;
		 *
		 * @param nodeParent
		 */
		asyncUploadImage : function(node, classID) {
			if (node == undefined) {
				return false;
			}

			var attrs, src, alt, className, sizes, srcset, dataAttrs, arAttr, i, nodeDivImage,
				asyncUploadImages = BX.findChildren(node, {
					attrs : (undefined != classID? {"data-class" : classID} : {"data-upload-image" : "Y"})
				}, true);

			if (null != asyncUploadImages) {
				for (i = 0; i < asyncUploadImages.length; ++i) {
					nodeDivImage = asyncUploadImages[i];
					attrs = {};

					if (undefined != (src = nodeDivImage.dataset["src"])) {
						attrs["src"] = src;
					}

					if (undefined != (alt = nodeDivImage.dataset["alt"])) {
						attrs["alt"] = alt;
					}

					if (undefined != (className = nodeDivImage.dataset["class"])) {
						attrs["class"] = className;
					}

					if (undefined != (sizes = nodeDivImage.dataset["sizes"])) {
						attrs["sizes"] = sizes;
					}

					if (undefined != (srcset = nodeDivImage.dataset["srcset"])) {
						attrs["srcset"] = srcset;
					}

					if (undefined != (dataAttrs = nodeDivImage.dataset["attrs"])) {
						dataAttrs.split(" ").forEach(function(strAttr) {
							arAttr = strAttr.split("=");

							if (undefined != arAttr[0]) {
								attrs[arAttr[0]] = '';
							}

							if (undefined != arAttr[1] && 2 < arAttr[1].length) {
								attrs[arAttr[0]] = arAttr[1].substr(1, (arAttr[1].length - 2));
							}
						});
					}

					nodeDivImage.replaceWith(BX.create('IMG', {attrs : attrs}));
				}
			}
		}
	};
})(window);