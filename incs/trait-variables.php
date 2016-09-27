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
	 * @package VAEXTRASETTINGS\Modules
	 */
	trait Variable {
		use Functions;

		/**
		 * Get setting labels.
		 *
		 * @return array
		 */
		public static function settings() {
			$settings = [
				'fb_page' => [
					'label'       => __( 'Facebook Page Web Address', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'fb_page',
					'sanitize'    => '_sanitize_zero_one',
					'_builtin'    => true,
				],
				'fb_appid' => [
					'label'       => __( 'Facebook App ID', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'fb_appid',
					'sanitize'    => '_sanitize_intval',
					'_builtin'    => true,
				],
				'twttr_name' => [
					'label'       => __( 'Twitter Account', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'twttr_name',
					'sanitize'    => '_sanitize_twttr_name',
					'_builtin'    => true,
				],
				'text_like_0' => [
					'label'       => __( 'Like Aria Text 1', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'text_like_0',
					'sanitize'    => 'sanitize_text_field',
					'_builtin'    => true,
				],
				'text_like_1' => [
					'label'       => __( 'Like Aria Text 2', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'text_like_1',
					'sanitize'    => 'sanitize_text_field',
					'_builtin'    => true,
				],
				'text_share' => [
					'label'       => __( 'Share Button Text', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'text_share',
					'sanitize'    => 'sanitize_text_field',
					'_builtin'    => true,
				],
				'text_tweet' => [
					'label'       => __( 'Tweet Button Text', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'text_tweet',
					'sanitize'    => 'sanitize_text_field',
					'_builtin'    => true,
				],
				'text_follow' => [
					'label'       => __( 'Twitter Follow Text', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'text_follow',
					'sanitize'    => 'sanitize_text_field',
					'_builtin'    => true,
				],
				'like_area_bg' => [
					'label'       => __( 'Twitter Follow Text', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'like_area_bg',
					'sanitize'    => 'sanitize_hex_color_no_hash',
					'_builtin'    => true,
				],
				'like_area_color' => [
					'label'       => __( 'Twitter Follow Text', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'like_area_color',
					'sanitize'    => 'sanitize_hex_color_no_hash',
					'_builtin'    => true,
				],
				'post_types' => [
					'label'       => __( 'Show in', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'post_types',
					'sanitize'    => 'sanitize_key',
					'_builtin'    => true,
				],
			];

			if ( Functions::exists_push7() ) {
				$settings['text_push7'] = [
					'label'       => __( 'Show in', 'va-social-buzz' ),
					'description' => '',
					'render'      => 'text_push7',
					'sanitize'    => 'sanitize_text_field',
					'_builtin'    => true,
				];
			}

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'admin_settings', ksort( $settings ) );
		}
	}
}
