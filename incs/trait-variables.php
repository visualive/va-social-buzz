<?php
/**
 * WordPress plugin variable class.
 *
 * @package    WordPress
 * @subpackage VA Extra Settings
 * @since      1.1.0
 * @author     KUCKLU <kuck1u@visualive.jp>
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
		 *
		 * @return array
		 */
		public static function settings() {
			$settings['fb_page']     = [
				'label'         => __( 'Facebook Page Web Address', 'va-social-buzz' ),
				'description'   => __( 'Facebook Page Web Address can only contain A-Z, a-z, 0-9, and periods (.).', 'va-social-buzz' ),
				'default_value' => '',
				'render'        => 'render_fb_page',
				'sanitize'      => '_sanitize_fb_page',
				'_builtin'      => true,
			];
			$settings['fb_appid']    = [
				'label'         => __( 'Facebook App ID', 'va-social-buzz' ),
				'description'   => __( 'Facebook App ID can only contain 0-9.', 'va-social-buzz' ),
				'default_value' => '',
				'render'        => 'render_fb_appid',
				'sanitize'      => '_sanitize_intval',
				'_builtin'      => true,
			];
			$settings['twttr_name']  = [
				'label'         => __( 'Twitter Account', 'va-social-buzz' ),
				'description'   => __( 'Twitter Account can only contain A-Z, a-z, 0-9, and underscore (_).', 'va-social-buzz' ),
				'default_value' => '',
				'render'        => 'render_twttr_name',
				'sanitize'      => '_sanitize_twttr_name',
				'_builtin'      => true,
			];
			$settings['text_like_0'] = [
				'label'         => __( 'Like Aria Text 1', 'va-social-buzz' ),
				'description'   => __( 'Appear on top of the "like" button. Sentence of the first line.', 'va-social-buzz' ),
				'default_value' => __( 'If you liked this article,', 'va-social-buzz' ),
				'render'        => 'render_text_like_0',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_like_1'] = [
				'label'         => __( 'Like Aria Text 2', 'va-social-buzz' ),
				'description'   => __( 'Appear on top of the "like" button. Sentence of the second line.', 'va-social-buzz' ),
				'default_value' => __( 'please click this "like!".', 'va-social-buzz' ),
				'render'        => 'render_text_like_1',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_share']  = [
				'label'         => __( 'Share Button Text', 'va-social-buzz' ),
				'description'   => __( 'Share button to Facebook.', 'va-social-buzz' ),
				'default_value' => __( 'Share', 'va-social-buzz' ),
				'render'        => 'render_text_share',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_tweet']  = [
				'label'         => __( 'Tweet Button Text', 'va-social-buzz' ),
				'description'   => __( 'Tweet button to Twitter.', 'va-social-buzz' ),
				'default_value' => __( 'Tweet', 'va-social-buzz' ),
				'render'        => 'render_text_tweet',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];
			$settings['text_follow'] = [
				'label'         => __( 'Twitter Follow Text', 'va-social-buzz' ),
				'description'   => __( 'Follow Push7 button of the text.', 'va-social-buzz' ),
				'default_value' => __( 'Follow on Twetter !', 'va-social-buzz' ),
				'render'        => 'render_text_follow',
				'sanitize'      => 'sanitize_text_field',
				'_builtin'      => true,
			];

			if ( Functions::exists_push7() ) {
				$settings['text_push7'] = [
					'label'         => __( 'Push7 Button Text', 'va-social-buzz' ),
					'description'   => __( 'Follow button left of the text.', 'va-social-buzz' ),
					'default_value' => __( 'Receive the latest posts with push notifications', 'va-social-buzz' ),
					'render'        => 'render_text_push7',
					'sanitize'      => 'sanitize_text_field',
					'_builtin'      => true,
				];
			}

			$settings['like_area_bg']      = [
				'label'         => __( 'Like Aria Background Color', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => '#2b2b2b',
				'render'        => 'render_like_area_bg',
				'sanitize'      => 'sanitize_hex_color',
				'_builtin'      => true,
			];
			$settings['like_area_opacity'] = [
				'label'         => __( 'Like Aria Background Opacity', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => '0.7',
				'render'        => 'render_like_area_opacity',
				'sanitize'      => '_sanitize_number_float',
				'_builtin'      => true,
			];
			$settings['like_area_color']   = [
				'label'         => __( 'Like Aria Text Color', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => '#ffffff',
				'render'        => 'render_like_area_color',
				'sanitize'      => 'sanitize_hex_color',
				'_builtin'      => true,
			];
			$settings['post_types']        = [
				'label'         => __( 'Show in', 'va-social-buzz' ),
				'description'   => '',
				'default_value' => [],
				'render'        => 'render_post_types',
				'sanitize'      => '_sanitize_key_for_array_value',
				'_builtin'      => true,
			];

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'admin_settings', $settings );
		}

		/**
		 * Default setting values.
		 *
		 * @return array
		 */
		public static function default_options() {
			$settings = self::settings();
			$options  = [];

			foreach ( $settings as $key => $setting ) {
				$options[ $key ] = $setting['default_value'];
			}

			return $options;
		}
	}
}
