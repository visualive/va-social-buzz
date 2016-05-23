/*!
 * VA Social Buzz.
 *
 * @package   VisuAlive.
 * @version   1.0.11
 * @author    KUCKLU.
 * @copyright Copyright (c) KUCKLU and VisuAlive.
 * @link      http://visualive.jp/
 * @license   GNU General Public License version 2.0 later.
 */

(function ($, window, document, undefined) {
    'use strict';

    var vaSocialBuzz = {
        /**
         * Define cache var.
         *
         * @since 1.0.0
         */
        cache: {},

        /**
         * Main Function.
         *
         * @since 1.0.0
         */
        init: function () {
            this.cacheElements();
            this.bindEvents();
        },

        /**
         * Cache Elements.
         *
         * @since 1.0.0
         */
        cacheElements: function () {
            this.cache = {
                $window  : $(window),
                window   : window,
                $document: $(document),
                document : document,
                wordpress: vaSocialBuzzSettings
            };
        },

        /**
         * Bind Events.
         *
         * @since 1.0.0
         */
        bindEvents: function () {
            // Store object in new var
            var self = this;

            // Ajax Cache
            $.ajaxSetup({
                cache: true,
                async: true
            });

            // Run on document ready
            self.cache.$document.on('ready', function () {
                self.gaEventTracking();
                self.createElements();
                self.getJavaScriptSDK();
            });
        },

        /**
         * Run plugin.
         *
         * @since 1.0.0
         */
        gaEventTracking: function () {
            var self = this,
                $vasb = $('#va-social-buzz'),
                $facebook = $vasb.find('.vasb_share_button-fb').children('a'),
                $twitter = $vasb.find('.vasb_share_button-tw').children('a');

            if (typeof self.cache.window.GoogleAnalyticsObject != 'undefined' && self.cache.window.GoogleAnalyticsObject == 'ga') {
                self._gaEventTracking(self);
            } else {
                $facebook.on('click', function (e) {
                    e.preventDefault();
                    if (typeof (FB) != 'undefined' && typeof self.cache.wordpress.appid != 'undefined') {
                        FB.ui({
                            method      : 'share',
                            href        : self.cache.window.location.href,
                            redirect_uri: self.cache.window.location.href
                        });
                    } else {
                        self._shareNewWindow(this);
                    }
                });
                $twitter.on('click', function (e) {
                    e.preventDefault();
                    self._shareNewWindow(this);
                });
            }
        },

        /**
         * Create Fb elements.
         *
         * @since 1.0.0
         */
        createElements: function () {
            var self = this,
                fbRoot = self.cache.document.getElementById('fb-root'),
                body;

            if (null == fbRoot) {
                body = self.cache.document.body;
                fbRoot = self.cache.document.createElement('div');

                fbRoot.setAttribute('id', 'fb-root');
                body.insertBefore(fbRoot, body.firstChild);
            }
        },

        /**
         * Load js sdk.
         *
         * @since 1.0.0
         */
        getJavaScriptSDK: function () {
            var self = this,
                fb_init;

            $.getScript('//connect.facebook.net/' + self._deletionOtherAlphanumeric(self.cache.wordpress.locale) + '/sdk.js', function () {
                fb_init = {
                    version: 'v2.5',
                    status : true,
                    cookie : true,
                    xfbml  : true
                };

                if (typeof self.cache.wordpress.appid != 'undefined') {
                    fb_init.appId = self._deletionOtherNumeric(self.cache.wordpress.appid);
                    window.fbAsyncInit = function() {
                        FB.init(fb_init);
                    };
                } else {
                    window.fbAsyncInit = function() {
                        FB.init(fb_init);
                    };
                }
            });

            $.getScript('//platform.twitter.com/widgets.js', function () {
                twttr.widgets.load();
            });
        },

        /**
         * Escape.
         *
         * @since 1.0.0
         * @param str
         * @returns {*}
         * @private
         */
        _escapeHTML: function (str) {
            return str.replace(/<("[^"]*"|'[^']*'|[^'">])*>/g, '').replace(/"/g, '&quot;').replace(/\'/g, '&apos;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        },

        /**
         * Deletion other than alphanumeric & hyphen.
         *
         * @since 1.0.0
         * @param str
         * @returns {*}
         * @private
         */
        _deletionOtherAlphanumeric: function (str) {
            return str.replace(/[^\w\-]/g, '');
        },

        /**
         * Deletion other than alphanumeric & hyphen.
         *
         * @since 1.0.0
         * @param str
         * @returns {*}
         * @private
         */
        _deletionOtherNumeric: function (str) {
            return str.replace(/[^0-9]/g, '');
        },

        /**
         * Google Analytics Events Send.
         *
         * @since 1.0.0
         * @param t
         * @private
         */
        _gaEventTracking: function (t) {
            var self = t,
                $vasb = $('#va-social-buzz'),
                $facebook = $vasb.find('.vasb_share_button-fb').children('a'),
                $twitter = $vasb.find('.vasb_share_button-tw').children('a');

            window.onload = function () {
                if (typeof (FB) != 'undefined') {
                    FB.Event.subscribe('edge.create', function (url) {
                        ga('send', 'event', 'VA Social Buzz', 'Fb Like', url);
                        ga('send', 'social', 'facebook', 'like', url);
                    });
                    FB.Event.subscribe('edge.remove', function (url) {
                        ga('send', 'event', 'VA Social Buzz', 'Fb Unlike', url);
                        ga('send', 'social', 'facebook', 'unlike', url);
                    });
                }

                if (typeof (twttr) != 'undefined') {
                    twttr.ready(function (twttr) {
                        twttr.events.bind('follow', function () {
                            ga('send', 'event', 'VA Social Buzz', 'Twi Follow', self.cache.window.location.href);
                            ga('send', 'social', 'Twitter', 'Follow', self.cache.window.location.href);
                        });
                    });
                }
            };

            $facebook.on('click', function (e) {
                e.preventDefault();

                if (typeof (FB) != 'undefined' && typeof self.cache.wordpress.appid != 'undefined') {
                    FB.ui({
                        method      : 'share',
                        href        : self.cache.window.location.href,
                        redirect_uri: self.cache.window.location.href
                    }, function (response) {
                        if (response && !response.error_message) {
                            ga('send', 'event', 'VA Social Buzz', 'Fb Share', self.cache.window.location.href);
                            ga('send', 'social', 'Facebook', 'Share', self.cache.window.location.href);
                        }
                    });
                } else {
                    ga('send', 'event', 'VA Social Buzz', 'Fb Share', self.cache.window.location.href);
                    ga('send', 'social', 'Facebook', 'Share', self.cache.window.location.href);
                    self._shareNewWindow(this);
                }
            });

            $twitter.on('click', function (e) {
                e.preventDefault();
                ga('send', 'event', 'VA Social Buzz', 'Twi Tweet', self.cache.window.location.href);
                ga('send', 'social', 'Twitter', 'Tweet', self.cache.window.location.href);
                self._shareNewWindow(this);
            });
        },

        /**
         * Open new window.
         *
         * @since 1.0.3
         *
         * @param t this
         * @private
         */
        _shareNewWindow: function (t) {
            var self = this,
                href = self._escapeHTML($(t).attr('href'));

            window.open(href, '', 'width=550,height=500');
        }
    };

    // Get things going
    vaSocialBuzz.init();
})(window.jQuery, window, document, undefined);
