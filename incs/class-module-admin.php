<?php
/**
 * WordPress plugin admin class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @since      1.1.0
 * @author     KUCKLU <oss@visualive.jp>
 *             Copyright (C) 2015 KUCKLU and VisuAlive.
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

	if ( version_compare( $GLOBALS['wp_version'], '4.6', '<' ) && ! function_exists( 'sanitize_hex_color' ) ) {
		require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
	}

	/**
	 * Class Admin
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class Admin {
		use Instance, Options;

		/**
		 * Setting items.
		 *
		 * @var array
		 */
		private $settings = [];

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		protected function __construct() {
			$this->settings = Variable::settings();

			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_init', array( &$this, 'admin_notices' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'add_pointer_script' ) );
		}

		/**
		 * Admin enqueue scripts.
		 *
		 * @param string $hook The current admin page.
		 */
		public function admin_enqueue_scripts( $hook ) {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'va-social-buzz-pointer', VA_SOCIALBUZZ_URL . 'assets/js/pointer.js', array(
				'wp-pointer',
			), false, true );

			if ( 'options-reading.php' === $hook ) {
				wp_enqueue_style( 'va-social-buzz-admin', VA_SOCIALBUZZ_URL . 'assets/css/admin.css' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'va-social-buzz-admin', VA_SOCIALBUZZ_URL . 'assets/js/admin.js', array(
					'jquery',
					'wp-color-picker',
				), false, true );
			}
		}

		/**
		 * Add css/js for pointer.
		 *
		 * @since  1.0.19
		 * @author Toro_Unit (contributor)
		 */
		public function add_pointer_script() {
			$dismissed       = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
			$pointer_enable  = ( false === array_search( VA_SOCIALBUZZ_NAME_OPTION, $dismissed ) );
			$pointer_content = '<h3>' . __( 'VA Social Buzz', 'va-social-buzz' ) . '</h3>';
			$pointer_content .= '<p>' . __( 'You can setting VA Social Buzz in <a href="options-reading.php">Reading</a>.', 'va-social-buzz' ) . '</p>';

			wp_localize_script( 'va-social-buzz-pointer', 'VASocialBuzz', array(
				'pointerContent' => $pointer_content,
				'pointerName'    => VA_SOCIALBUZZ_NAME_OPTION,
				'pointerEnable'  => $pointer_enable,
			) );
		}

		/**
		 * Admin notices.
		 *
		 * @since  1.1.8
		 */
		public function admin_notices() {
			require_once dirname( __FILE__ ) . '/class-module-admin-notices.php';

			$notices  = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_admin_notices', AdminNotices::get_called_class() );

			$notices::get_instance();
		}

		/**
		 * Add settings.
		 */
		public function admin_init() {
			$settings = $this->settings;

			register_setting( 'reading', VA_SOCIALBUZZ_NAME_OPTION, array( &$this, 'sanitize_option' ) );
			add_settings_section( VA_SOCIALBUZZ_PREFIX . 'section', __( 'VA Social Buzz', 'va-social-buzz' ), null, 'reading' );

			foreach ( $settings as $key => $setting ) {
				$render = $setting['render'];

				if ( isset( $setting['_builtin'] ) && true === $setting['_builtin'] ) {
					$render = [ &$this, $render ];
				}

				add_settings_field(
					VA_SOCIALBUZZ_PREFIX . $key,
					'<label for="' . esc_attr( VA_SOCIALBUZZ_PREFIX . $key ) . '">' . esc_html( $setting['label'] ) . '</label>',
					$render,
					'reading',
					VA_SOCIALBUZZ_PREFIX . 'section'
				);
			}
		}

		/**
		 * Render form parts.
		 */
		public function render_fb_appid() {
			self::_render_text_field( 'fb_appid' );
		}

		/**
		 * Render form parts.
		 */
		public function render_twttr_name() {
			self::_render_text_field( 'twttr_name' );
		}

		/**
		 * Render form parts.
		 */
		public function render_fb_page() {
			self::_render_text_field( 'fb_page' );
		}

		/**
		 * Render form parts.
		 */
		public function render_text_like_0() {
			self::_render_text_field( 'text_like_0' );
		}

		/**
		 * Render form parts.
		 */
		public function render_text_like_1() {
			self::_render_text_field( 'text_like_1' );
		}

		/**
		 * Render form parts.
		 */
		public function render_text_share() {
			self::_render_text_field( 'text_share' );
		}

		/**
		 * Render form parts.
		 */
		public function render_text_tweet() {
			self::_render_text_field( 'text_tweet' );
		}

		/**
		 * Render form parts.
		 */
		public function render_text_follow() {
			self::_render_text_field( 'text_follow' );
		}

		/**
		 * Render form parts.
		 */
		public function render_like_area_bg() {
			self::_render_text_field( 'like_area_bg' );
		}

		/**
		 * Render form parts.
		 */
		public function render_like_area_color() {
			self::_render_text_field( 'like_area_color' );
		}

		/**
		 * Render form parts.
		 */
		public function render_text_push7() {
			self::_render_text_field( 'text_push7' );
		}

		/**
		 * Render form parts.
		 */
		public function render_like_area_opacity() {
			$output     = [];
			$value      = 0;
			$loop       = 11;
			$key        = 'like_area_opacity';
			$option     = Options::get( $key );
			$settings   = $this->settings[ $key ];
			$dom_select = '<select name="%s[%s]">%s</select>';

			do {
				$output[] = sprintf( '<option value="%s"%s>%s</option>', esc_attr( $value ), selected( $value, $option, false ), esc_html( $value ) );
				$value    = $value + 0.1;
				$loop     = $loop - 1;
			} while ( $loop );

			$output[] = self::_render_description( $settings );

			echo sprintf( $dom_select, esc_attr( VA_SOCIALBUZZ_NAME_OPTION ), esc_attr( $key ), implode( PHP_EOL, $output ) );
		}

		/**
		 * Render form parts.
		 */
		public function render_post_types() {
			$output     = [];
			$key        = 'post_types';
			$show_ins   = Options::get( $key );
			$settings   = $this->settings[ $key ];
			$post_types = array_values( get_post_types( array(
				'public' => true,
			) ) );

			foreach ( $post_types as $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$checked          = in_array( $post_type, (array) $show_ins ) ? ' checked' : '';
				$output[]         = sprintf(
					'<li><label><input class="%s" type="checkbox" name="%s[%s][]" value="%s"%s> %s</label></li>',
					esc_attr( VA_SOCIALBUZZ_PREFIX . 'post_types' ),
					esc_attr( VA_SOCIALBUZZ_NAME_OPTION ),
					esc_attr( $key ),
					esc_attr( $post_type ),
					esc_attr( $checked ),
					esc_html( $post_type_object->labels->name )
				);
			}

			$output[] = self::_render_description( $settings );

			echo sprintf( '<ul>%s</ul>', implode( PHP_EOL, $output ) );
		}

		/**
		 * Render text field.
		 *
		 * @param string $key Option key.
		 */
		protected function _render_text_field( $key = '' ) {
			$value    = Options::get( $key );
			$settings = $this->settings[ $key ];
			$output[] = sprintf(
				'<input id="%s" class="regular-text" type="text" name="%s[%s]" value="%s">',
				esc_attr( VA_SOCIALBUZZ_PREFIX . $key ),
				esc_attr( VA_SOCIALBUZZ_NAME_OPTION ),
				esc_attr( $key ),
				esc_attr( $value )
			);
			$output[] = self::_render_description( $settings );

			echo implode( PHP_EOL, $output );
		}

		/**
		 * Render description.
		 *
		 * @param array $settings Option key.
		 *
		 * @return string
		 */
		protected function _render_description( $settings = [] ) {
			$output      = [];
			$tmp         = '<p class="description">%s</p>';
			$description = isset( $settings['description'] ) ? $settings['description'] : '';

			if ( ! empty( $description ) && is_array( $description ) ) {
				foreach ( $description as $value ) {
					$output[] = sprintf( $tmp, wp_kses_data( $value ) );
				}
			} elseif ( ! empty( $description ) ) {
				$output[] = sprintf( $tmp, wp_kses_data( $description ) );
			}

			return implode( PHP_EOL, $output );
		}

		/**
		 * Sanitize.
		 *
		 * @param array $options Option.
		 *
		 * @return array
		 */
		public function sanitize_option( $options ) {
			$sanitize   = array();
			$settings   = $this->settings;
			$options    = wp_parse_args( $options, Options::get( 'default' ) );
			$db_version = ( isset( $options['db_version'] ) ) ? $options['db_version'] : VA_SOCIALBUZZ_VERSION_DB;
			$notices    = ( isset( $options['notices'] ) && ! empty( $options['notices'] ) ) ? $options['notices'] : [];

			unset( $options['db_version'] );
			unset( $options['notices'] );

			foreach ( $options as $key => $option ) {
				if ( isset( $settings[ $key ] ) ) {
					$sanitize = $settings[ $key ]['sanitize'];
				}

				$conditions = ( isset( $settings[ $key ]['_builtin'] ) && true === $settings[ $key ]['_builtin'] && 1 === preg_match( '/\A_(.*?)+\z/', $settings[ $key ]['sanitize'] ) );

				if ( $conditions ) {
					$sanitize = [ &$this, $sanitize ];
				}

				$options[ $key ] = call_user_func( $sanitize, $option );
			}

			$options['db_version'] = $db_version;
			$options['notices']    = $notices;

			return $options;
		}

		/**
		 * Sanitize checkbox.
		 *
		 * @param string $value 0 or 1.
		 *
		 * @return string
		 */
		protected function _sanitize_checkbox( $value = '0' ) {
			$value = '1' === $value ? $value : '0';

			return $value;
		}

		/**
		 * Sanitize integer.
		 *
		 * @param string $value Integer.
		 *
		 * @return string
		 */
		protected function _sanitize_intval( $value = '' ) {
			$value = preg_replace( '/[^0-9]/', '', $value );

			return $value;
		}

		/**
		 * Sanitize number float.
		 *
		 * @param string $value Number.
		 *
		 * @return string
		 */
		protected function _sanitize_number_float( $value = '' ) {
			return filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		}

		/**
		 * Sanitize Facebook oage.
		 *
		 * @param string $value Twitter name.
		 *
		 * @return string
		 */
		protected function _sanitize_fb_page( $value = '' ) {
			$value = preg_replace( '/[^\w\-.]/', '', $value );

			return $value;
		}

		/**
		 * Sanitize Twitter name.
		 *
		 * @param string $value Twitter name.
		 *
		 * @return string
		 */
		protected function _sanitize_twttr_name( $value = '' ) {
			$value = preg_replace( '/[^\w]/', '', $value );

			return $value;
		}

		/**
		 * Sanitize post type name.
		 *
		 * @param array $value Post types.
		 *
		 * @return array
		 */
		protected function _sanitize_key_for_array_value( $value = [] ) {
			if ( ! empty( $value ) ) {
				$value = array_map( 'sanitize_key', $value );
			}

			return $value;
		}
	}
}
