<?php
/**
 * WordPress plugin view class.
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

	/**
	 * Class View.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class View {
		use Instance, Options;

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		protected function __construct() {
			add_action( VA_SOCIALBUZZ_PREFIX . 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ] );
			add_filter( VA_SOCIALBUZZ_PREFIX . 'the_content', [ &$this, 'the_content' ] );
			add_filter( VA_SOCIALBUZZ_PREFIX . 'doing_show_in', [ &$this, 'doing_show_in' ], 10, 2 );
		}

		/**
		 * Echo scripts.
		 *
		 * @since 0.0.1 (Alpha)
		 * @since 1.1.0 Refactoring.
		 */
		public function enqueue_scripts() {
			$localize['locale'] = esc_attr( Functions::get_locale() );
			$css                = self::_inline_style();
			$style_file         = self::_style_file();
			$script_file        = self::_script_file();
			$include_style      = apply_filters( VA_SOCIALBUZZ_PREFIX . 'include_style', true );
			$include_script     = apply_filters( VA_SOCIALBUZZ_PREFIX . 'include_script', true );
			$object_name        = 'vaSocialBuzzSettings';
			$options            = Options::get( 'all' );

			if ( ! empty( $options['fb_appid'] ) ) {
				$localize['appid'] = esc_attr( preg_replace( '/[^0-9]/', '', $options['fb_appid'] ) );
			}

			/**
			 * Add extra CSS styles to a registered stylesheet.
			 *
			 * @param string $data   String containing the CSS styles to be added.
			 * @param string $handle Name of the stylesheet to add the extra styles to.
			 */
			$css = apply_filters( VA_SOCIALBUZZ_PREFIX . 'inline_style', trim( $css ), VA_SOCIALBUZZ_BASENAME );

			/**
			 * Localize script.
			 *
			 * @param array $l10n         The data itself. The data can be either a single or multi-dimensional array.
			 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
			 *                            Example: '/[a-zA-Z0-9_]+/'.
			 * @param string $handle      Script handle the data will be attached to.
			 */
			$localize = apply_filters( VA_SOCIALBUZZ_PREFIX . 'localize_script', $localize, $object_name, VA_SOCIALBUZZ_BASENAME );

			if ( true === $include_style ) {
				wp_enqueue_style( VA_SOCIALBUZZ_BASENAME, esc_url( $style_file ), array(), VA_SOCIALBUZZ_VERSION );
				wp_add_inline_style( VA_SOCIALBUZZ_BASENAME, $css );
			}

			if ( true === $include_script ) {
				wp_enqueue_script( VA_SOCIALBUZZ_BASENAME, esc_url( $script_file ), array( 'jquery' ), VA_SOCIALBUZZ_VERSION, true );
				wp_localize_script( VA_SOCIALBUZZ_BASENAME, $object_name, $localize );
			}

			/**
			 * Enqueue scripts.
			 *
			 * @param string $css         String containing the CSS styles to be added.
			 * @param array $l10n         The data itself. The data can be either a single or multi-dimensional array.
			 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
			 *                            Example: '/[a-zA-Z0-9_]+/'.
			 */
			do_action( VA_SOCIALBUZZ_PREFIX . 'enqueue_scripts', $css, $localize, $object_name );
		}

		/**
		 * Doing show in'.
		 *
		 * @since 0.0.1 (Alpha)
		 * @since 1.1.0 Refactoring.
		 *
		 * @param string $content Post content.
		 *
		 * @return string
		 */
		public function the_content( $content = '' ) {
			global $post;

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'doing_show_in', $content, $post );
		}

		/**
		 * Create content.
		 *
		 * @param string $content Post content.
		 * @param string $post    \WP_Query.
		 *
		 * @return string
		 */
		public function doing_show_in( $content = '', $post = null ) {
			$options    = Options::get( 'all' );
			$show_in    = $options['post_types'];
			$raw        = apply_filters( VA_SOCIALBUZZ_PREFIX . 'raw_the_content', $content );
			$content    = apply_filters( VA_SOCIALBUZZ_PREFIX . 'create_the_content', $content, $raw );
			$conditions = ( ! ( is_embed() || is_feed() || is_front_page() || is_home() ) && is_singular() && in_the_loop() && isset( $show_in ) && in_array( get_post_type(), $show_in ) );
			$conditions = apply_filters( VA_SOCIALBUZZ_PREFIX . 'show_in_conditions', $conditions, $post->ID );

			if ( $conditions ) {
				// Recommend you don't use this short code registering your own post data.
				$content .= do_shortcode( '[socialbuzz box="select"]' );
			};

			return $content;
		}

		/**
		 * Create javascript file url.
		 *
		 * @return string
		 */
		protected function _script_file() {
			$file_prefix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
			$filename    = apply_filters( VA_SOCIALBUZZ_PREFIX . 'script_filename', 'vasocialbuzz.js' );
			$filename    = trim( $filename, '/' );

			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $filename ) ) {
				$file = trailingslashit( get_stylesheet_directory_uri() )  . $filename;
			} else {
				$file = VA_SOCIALBUZZ_URL . 'assets/js/script' . $file_prefix . '.js';
			}

			return esc_url( $file );
		}

		/**
		 * Create stylesheet file url.
		 *
		 * @return string
		 */
		protected function _style_file() {
			$file_prefix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
			$filename    = apply_filters( VA_SOCIALBUZZ_PREFIX . 'style_filename', 'vasocialbuzz.css' );
			$filename    = trim( $filename, '/' );

			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $filename ) ) {
				$file = trailingslashit( get_stylesheet_directory_uri() )  . $filename;
			} else {
				$file = VA_SOCIALBUZZ_URL . 'assets/css/style' . $file_prefix . '.css';
			}

			return esc_url( $file );
		}

		/**
		 * Add inline style.
		 *
		 * @return string
		 */
		protected function _inline_style() {
			$thumbnail        = Functions::get_thumbnail();
			$css              = self::_tmp_head_css();
			$options          = Options::get( 'all' );
			$background_color = Functions::hex_to_rgb( sanitize_hex_color( $options['like_area_bg'] ), true );
			$opacity          = preg_replace( '/[^0-9\.]/', '', $options['like_area_opacity'] );
			$color            = sanitize_hex_color( $options['like_area_color'] );

			if ( 'none' !== $thumbnail ) {
				$thumbnail = sprintf( 'url(%s)', $thumbnail );
			}

			$css = str_replace( '{{thumbnail}}', $thumbnail, $css );
			$css = str_replace( '{{background_color}}', $background_color, $css );
			$css = str_replace( '{{opacity}}', $opacity, $css );
			$css = str_replace( '{{color}}', $color, $css );

			return $css;
		}

		/**
		 * Template head css.
		 *
		 * @return string
		 */
		protected function _tmp_head_css() {
			$css_before            = apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css_before', '' );
			$css_after             = apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css_after', '' );
			$css_center            = apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css_center', '' );
			$css_mediaquery_before = apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css_mediaquery_before', '' );
			$css_mediaquery_after  = apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css_mediaquery_after', '' );
			$css                   = <<<EOI
{$css_before}
.va-social-buzz .vasb_fb .vasb_fb_thumbnail {
	background-image: {{thumbnail}};
}
#secondary #widget-area .va-social-buzz .vasb_fb .vasb_fb_like,
#secondary .widget-area .va-social-buzz .vasb_fb .vasb_fb_like,
#secondary.widget-area .va-social-buzz .vasb_fb .vasb_fb_like,
.secondary .widget-area .va-social-buzz .vasb_fb .vasb_fb_like,
.sidebar-container .va-social-buzz .vasb_fb .vasb_fb_like,
.va-social-buzz .vasb_fb .vasb_fb_like {
	background-color: rgba({{background_color}}, {{opacity}});
	color: {{color}};
}
{$css_center}
@media only screen and (min-width: 711px) {
	{$css_mediaquery_before}
	.va-social-buzz .vasb_fb .vasb_fb_like {
		background-color: rgba({{background_color}}, 1);
	}
	{$css_mediaquery_after}
}
{$css_after}
EOI;
			$css = apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css', $css );
			$css = trim( preg_replace( array( '/(?:\r\n)|[\r\n]/', '/[\\x00-\\x09\\x0b-\\x1f]/', '/\n/', '/\s{2,}/' ), '', $css ) );
			$css = preg_replace( '/:\s/', ':', $css );
			$css = preg_replace( '/,\s/', ',', $css );
			$css = preg_replace( '/\s{/', '{', $css );

			return $css;
		}
	}
}
