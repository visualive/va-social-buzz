<?php
/**
 * WordPress plugin variable class.
 *
 * @package    WordPress
 * @subpackage VA Extra Settings
 * @since      1.1.0
 * @author     KUCKLU <oss@visualive.jp>
 *             Copyright (C) 2016 KUCKLU and VisuAlive.
 *             This program is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU General Public License as published by
 *             the Free Software Foundation; either version 2 of the License, or
 *             (at your option) any later version.
 *             This program is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU General Public License for more details.
 *             You should have received a copy of the GNU General Public License along
 *             with this program; if not, write to the Free Software Foundation, Inc.,
 *             51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *             It is also available through the world-wide-web at this URL:
 *             http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace VASOCIALBUZZ\Modules {
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Class Variable
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	trait Variable {
		use Functions;

		/**
		 * Get setting labels.
		 * recommend you don't use this when registering your own settings.
		 *
		 * @return array
		 */
		public static function settings() {
			$settings['fb_page']     = [
				'label'         => __( 'Facebook Page username', 'va-social-buzz' ),
				'description'   => [
					__( 'Usernames can only contain alphanumeric characters (A-Z, 0-9) or a period (".").', 'va-social-buzz' ),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( 'https://www.facebook.com/help/203523569682738' ),
						__( 'How do I change the username for my Page?', 'va-social-buzz' )
					),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( 'https://www.facebook.com/help/105399436216001' ),
						__( 'What are the guidelines around creating a custom username for my Page or profile?', 'va-social-buzz' )
					),
				],
				'default_value' => '',
				'render'        => 'render_fb_page',
				'sanitize'      => '_sanitize_fb_page',
				'_builtin'      => true,
			];
			$settings['fb_appid']    = [
				'label'         => __( 'Facebook App ID', 'va-social-buzz' ),
				'description'   => [
					__( 'App ID can only contain alphanumeric (0-9).', 'va-social-buzz' ),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( 'https://developers.facebook.com/docs/apps/register' ),
						__( 'Creating an App ID', 'va-social-buzz' )
					),
				],
				'default_value' => '',
				'render'        => 'render_fb_appid',
				'sanitize'      => '_sanitize_intval',
				'_builtin'      => true,
			];
			$settings['twttr_name']  = [
				'label'         => __( 'Twitter username', 'va-social-buzz' ),
				'description'   => __( 'Usernames can only contain alphanumeric characters (A-Z, 0-9) or a underscore ("_").', 'va-social-buzz' ),
				'default_value' => '',
				'render'        => 'render_twttr_name',
				'sanitize'      => '_sanitize_twttr_name',
				'_builtin'      => true,
			];
			$settings['text_like_0'] = [
				'label'         => __( 'Text of the "Like!" button area 1', 'va-social-buzz' ),
				'description'   => __( 'Appear on top of the "Like!" button. Sentence of the first line.', 'va-social-buzz' ),
				'default_value' => __( 'If you liked this article', 'va-social-buzz' ),
				'render'        => 'render_text_like_0',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_like_1'] = [
				'label'         => __( 'Text of the "Like!" button area 2', 'va-social-buzz' ),
				'description'   => __( 'Appear on top of the "Like!" button. Sentence of the second line.', 'va-social-buzz' ),
				'default_value' => __( 'please click on the "Like!".', 'va-social-buzz' ),
				'render'        => 'render_text_like_1',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_share']  = [
				'label'         => __( 'Share button', 'va-social-buzz' ),
				'description'   => __( 'Text of the share button to Facebook.', 'va-social-buzz' ),
				'default_value' => __( 'Share', 'va-social-buzz' ),
				'render'        => 'render_text_share',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_tweet']  = [
				'label'         => __( 'Tweet button', 'va-social-buzz' ),
				'description'   => __( 'Text of the tweet button to Twitter.', 'va-social-buzz' ),
				'default_value' => __( 'Tweet', 'va-social-buzz' ),
				'render'        => 'render_text_tweet',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_follow'] = [
				'label'         => __( 'Follow button', 'va-social-buzz' ),
				'description'   => __( 'Follow button horizontal text.', 'va-social-buzz' ),
				'default_value' => __( 'Follow on Twetter !', 'va-social-buzz' ),
				'render'        => 'render_text_follow',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];

			if ( Functions::exists_push7() ) {
				$settings['text_push7'] = [
					'label'         => __( 'Push7 button', 'va-social-buzz' ),
					'description'   => __( 'Text of the subscription button to Push7.', 'va-social-buzz' ),
					'default_value' => __( 'Receive the latest posts with push notifications', 'va-social-buzz' ),
					'render'        => 'render_text_push7',
					'sanitize'      => 'sanitize_text_field',
					'_builtin'      => true,
				];
			}

			$settings['like_area_bg']      = [
				'label'         => __( 'Background color of the "like!" button area', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => '#2b2b2b',
				'render'        => 'render_like_area_bg',
				'sanitize'      => 'sanitize_hex_color',
				'_builtin'      => true,
			];
			$settings['like_area_opacity'] = [
				'label'         => __( 'Background opacity of the "like!" button area', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => '0.7',
				'render'        => 'render_like_area_opacity',
				'sanitize'      => '_sanitize_number_float',
				'_builtin'      => true,
			];
			$settings['like_area_color']   = [
				'label'         => __( 'Text color of the "like!" button area', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => '#ffffff',
				'render'        => 'render_like_area_color',
				'sanitize'      => 'sanitize_hex_color',
				'_builtin'      => true,
			];
			$settings['post_types']        = [
				'label'         => __( 'Show in', 'va-social-buzz' ),
				'description'   => __( 'Please choose post type one or more.', 'va-social-buzz' ),
				'default_value' => [ 'post' ],
				'render'        => 'render_post_types',
				'sanitize'      => '_sanitize_key_for_array_value',
				'_builtin'      => true,
			];

			/**
			 * Setting items to be displayed in the settings page.
			 *
			 * @param array $settings Recommend you don't use this "_builtin" parameter registering your own settings.
			 */

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'admin_settings', $settings );
		}

		/**
		 * Default setting values.
		 *
		 * @return array
		 */
		public static function default_options() {
			$settings              = self::settings();
			$options['db_version'] = VA_SOCIALBUZZ_VERSION_DB;
			$options['notices']    = [];

			foreach ( $settings as $key => $setting ) {
				$options[ $key ] = $setting['default_value'];
			}

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'admin_default_options', $options );
		}

		/**
		 * SNS list.
		 *
		 * @return array
		 */
		public static function sns_list() {
			$options         = get_option( VA_SOCIALBUZZ_NAME_OPTION, [] );
			$default_options = self::default_options();
			$text_share      = isset( $options['text_share'] ) ? $options['text_share'] : $default_options['text_share'];
			$text_tweet      = isset( $options['text_tweet'] ) ? $options['text_tweet'] : $default_options['text_tweet'];
			$links           = [
				'fb'    => [
					'endpoint'    => 'https://www.facebook.com/sharer/sharer.php?u={{permalink}}',
					'anchor_text' => $text_share,
				],
				'twttr' => [
					'endpoint'    => 'https://twitter.com/share?url={{permalink}}&text={{post_title}}',
					'anchor_text' => $text_tweet,
				],
			];

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'sns_list', $links );
		}

		/**
		 * Notification list.
		 *
		 * @since 1.1.6
		 *
		 * @return array
		 */
		public static function notification_list() {
			$options         = get_option( VA_SOCIALBUZZ_NAME_OPTION, [] );
			$default_options = self::default_options();
			$links           = [];

			if ( Functions::exists_push7() ) {
				$endpoint_push7 = Functions::get_push7_register_url();
				$text_push7     = isset( $options['text_push7'] ) ? $options['text_push7'] : $default_options['text_push7'];
				$links['push7'] = [
					'endpoint'    => $endpoint_push7,
					'anchor_text' => $text_push7,
				];
			}

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'notification_list', $links );
		}
	}
}
