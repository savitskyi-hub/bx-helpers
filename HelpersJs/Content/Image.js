/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Image');

(function() {
	'use strict';

	/**
	 *
	 *
	 * (для асинхронной загрузки двойные кавычки будут заменены на
	 *  одинарные, нужно это учесть при возвращении в нормальное состояния);
	 */
	BX.SavitskyiHub.BxHelpers.Helpers.Content.Image = {

		/**
		 *
		 * @param event
		 */
		asyncUploadImage : function(event) {
			////	if (event == undefined) {
			////
			////		// Menu catalog
			////		$("header nav > div, header nav > div .dropdown-menu").hover(function() {
			////			var uploadImages = $(this).find('div[data-upload-image="Y"]');
			////
			////			if (uploadImages.length) {
			////				uploadImages.each(function() {
			////					$(this).replaceWith(creataAsyncImage($(this)));
			////				});
			////			}
			////		});
			////
			////	} else {
			////
			////		switch (event) {
			////			case "basket_item-image-popup":
			////				var uploadImages = $(".basket_item.i_overlay").find('div[data-upload-image="Y"]');
			////
			////				if (uploadImages.length) {
			////					uploadImages.each(function() {
			////						$(this).replaceWith(creataAsyncImage($(this)));
			////					});
			////				}
			////				break;
			////
			////			case "offers-pallet-image":
			////				var palletActiveList = $(".card-product").find(".offers-pallet-image.active"),
			////					activeID = 0,
			////					uploadImages;
			////
			////				palletActiveList.each(function() {
			////					activeID = $(this).data('id');
			////					uploadImages = $('div[data-upload-image="Y"][data-class="card-product-offer-image id-' + activeID + '"]');
			////
			////					if (uploadImages.length) {
			////						uploadImages.replaceWith(creataAsyncImage(uploadImages));
			////					}
			////				});
			////				break;
			////		}
			////
			////	}
		},

		/**
		 *
		 * @param el
		 */
		createAsyncImage : function(el) {
			////	let attrs = {};
			////
			////	if (el.data('alt') != undefined) {
			////		attrs["alt"] = el.data('alt');
			////	}
			////
			////	if (el.data('srcset') != undefined) {
			////		attrs["srcset"] = el.data('srcset');
			////	}
			////
			////	if (el.data('sizes') != undefined) {
			////		attrs["sizes"] = el.data('sizes');
			////	}
			////
			////	if (el.data('class') != undefined) {
			////		attrs["class"] = el.data('class');
			////	}
			////
			////	return BX.create('IMG', {
			////		props : {src : el.data('src')},
			////		attrs : attrs
			////	});
		}
	};
});