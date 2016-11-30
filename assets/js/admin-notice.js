/*!
 * VA Social Buzz.
 *
 * @package   VisuAlive.
 * @version   1.0.0
 * @author    KUCKLU.
 * @copyright Copyright (c) KUCKLU and VisuAlive.
 * @link      http://visualive.jp/
 * @license   GNU General Public License version 2.0 later.
 */

(function ($, window, document, undefined) {
    'use strict';

    var VASBNotices = {
        update: function (dismissible) {
            var defer = $.Deferred();

            $.ajax({
                type    : 'POST',
                url     : ajaxurl,
                data    : {
                    'action'     : vaSocialBuzzSettings.action,
                    'nonce'      : vaSocialBuzzSettings.nonce,
                    'dismissible': dismissible
                },
                dataType: 'json',
                success : defer.resolve,
                error   : defer.reject
            });

            return defer.promise();
        },
        sanitizeKey: function (key) {
            return key.replace(/[^\w\-]/g, '');
        }
    };

    $(document).on('ready', function () {
        $('#vasb-notices').find('.notice-dismiss').on('click', function(e){
            var $dismissible;

            $dismissible = $(this).parent('#vasb-notices').attr('data-dismissible');
            $dismissible = VASBNotices.sanitizeKey($dismissible);


            if ('' !== $dismissible || 'undefined' !== typeof $dismissible) {
                VASBNotices.update($dismissible).done(function(r){
                    console.log(r.data);
                });
            }
        });
    });
})(window.jQuery, window, document, undefined);