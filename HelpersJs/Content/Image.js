//BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Image');
//
//(function() {
//	'use strict';
//
//	/**
//	 * Метод предназначен для реализации функционала фиксирования блоков при скроллинге, само фиксирование начинается от момента:
//	 * - когда верхняя черта экрана доходит к блоку который должен быть зафиксирован
//	 * - когда есть достаточно места для вертикального скроллинга
//	 *
//	 * @type {{obListFixedBlock: {}, init: BX.Local.Helpers.Content.FixedBlockOnScroll.init}}
//	 */
//	BX.SavitskyiHub.BxHelpers.Helpers.Content.Image = {}
//});
//
////
////function asyncUploadImage(event) {
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
////}
////
////function creataAsyncImage(el) {
////	var attrs = {};
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
////}