//BX.namespace('SavitskyiHub.BxHelpers.Helpers.Content.Text');
//
//(function() {
//	'use strict';
//
//	BX.SavitskyiHub.BxHelpers.Helpers.Content.Text = {
////		params : {
////			useFlexContainerEditMode : true
////		},
//
//		/**
//		 *
//		 */
////		init : function() {
////
////			if (this.params.useFlexContainerEditMode) {
////				this.flexContainerEditMode();
////			}
////
////		},
//
//		/**
//		 * -
//		 * @param el -
//		 * @returns {string}
//		 */
//		getFullTextInNode : function(el) {
//			'use strict';
//
//			let n, a = '', walk = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);
//
//			while (n = walk.nextNode()) {
//				a += n.textContent;
//			}
//
//			return a;
//		},
//
//		/**
//		 *
//		 * @returns {boolean}
//		 */
//		copyTextToClipboard : function(text) {
//			'use strict';
//
//			let textArea = document.createElement("textarea"), successful, msg;
//
//			textArea.style.position = 'fixed';
//			textArea.style.top = 0;
//			textArea.style.left = 0;
//			textArea.style.width = '2em';
//			textArea.style.height = '2em';
//			textArea.style.padding = 0;
//			textArea.style.border = 'none';
//			textArea.style.outline = 'none';
//			textArea.style.boxShadow = 'none';
//			textArea.style.background = 'transparent';
//			textArea.value = text;
//
//			document.body.appendChild(textArea);
//
//			textArea.focus();
//			textArea.select();
//
//			try {
//				successful = document.execCommand('copy');
//				msg = successful ? 'successful' : 'unsuccessful';
//			} catch (err) {
//				console.log('Oops, unable to copy');
//			}
//
//			document.body.removeChild(textArea);
//		}
//	};
//})();