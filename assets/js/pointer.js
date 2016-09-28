/*!
 * VA Social Buzz.
 *
 * @package   VisuAlive.
 * @author    KUCKLU.
 * @copyright Copyright (c) KUCKLU and VisuAlive.
 * @link      http://visualive.jp/
 * @license   GNU General Public License version 2.0 later.
 */


(function ($) {
	'use strict';
	$(function () {
		if( window.VASocialBuzz && VASocialBuzz.pointerEnable ) {
			$("#menu-settings .wp-has-submenu").pointer({
				content: VASocialBuzz.pointerContent,
				position: {"edge": "left", "align": "center"},
				close: function () {
					$.post('admin-ajax.php', {
						action: 'dismiss-wp-pointer',
						pointer: VASocialBuzz.pointerName
					})

				}
			}).pointer("open");
		}
	})

})(window.jQuery);
