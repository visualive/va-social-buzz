<?php
/**
 * WordPress plugin view class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @since      1.1.0
 * @author     KUCKLU <kuck1u@visualive.jp>
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
		 * Option items.
		 *
		 * @var array
		 */
		private $options = [];

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		private function __construct() {
			$this->options = Options::get( 'all' );

			add_action( VA_SOCIALBUZZ_PREFIX . 'enqueue_scripts', [ &$this, 'enqueue_scripts' ] );
			add_filter( VA_SOCIALBUZZ_PREFIX . 'the_content', [ &$this, 'the_content' ] );
		}

		/**
		 * Echo scripts.
		 *
		 * @since 0.0.1 (Alpha)
		 * @since 1.1.0 Refactoring.
		 */
		public function enqueue_scripts() {
			$thumbnail          = Functions::get_thumbnail();
			$localize['locale'] = esc_attr( Functions::get_locale() );
			$css                = self::_tmp_head_css();
			$file_prefix        = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
			$options            = $this->options;
			$background_color   = Functions::hex_to_rgb( sanitize_hex_color( $options['like_area_bg'] ), true );
			$opacity            = preg_replace( '/[^0-9\.]/', '', $options['like_area_opacity'] );
			$color              = sanitize_hex_color( $options['like_area_color'] );

			if ( 'none' !== $thumbnail ) {
				$thumbnail = sprintf( 'url(%s)', $thumbnail );
			}

			$css = str_replace( '{{thumbnail}}', $thumbnail, $css );
			$css = str_replace( '{{background_color}}', $background_color, $css );
			$css = str_replace( '{{opacity}}', $opacity, $css );
			$css = str_replace( '{{color}}', $color, $css );

			if ( ! empty( $options['fb_appid'] ) ) {
				$localize['appid'] = esc_attr( preg_replace( '/[^0-9]/', '', $options['fb_appid'] ) );
			}

			wp_enqueue_style( VA_SOCIALBUZZ_BASENAME, VA_SOCIALBUZZ_URL . 'assets/css/style' . $file_prefix . '.css', array(), VA_SOCIALBUZZ_VERSION );
			wp_add_inline_style( VA_SOCIALBUZZ_BASENAME, $css );
			wp_enqueue_script( VA_SOCIALBUZZ_BASENAME, VA_SOCIALBUZZ_URL . 'assets/js/script' . $file_prefix . '.js', array( 'jquery' ), VA_SOCIALBUZZ_VERSION, true );
			wp_localize_script( VA_SOCIALBUZZ_BASENAME, 'vaSocialBuzzSettings', $localize );
		}

		/**
		 * Create content.
		 *
		 * @since 0.0.1 (Alpha)
		 * @since 1.1.0 Refactoring.
		 *
		 * @param string $content Post content.
		 *
		 * @return string
		 */
		public function the_content( $content = '' ) {
			$options = $this->options;
			$show_in = $options['post_types'];

			if (
				! is_embed()
			    && ! has_shortcode( $content, 'socialbuzz' )
				&& in_the_loop()
				&& is_singular()
			    && in_array( get_post_type(), $show_in )
			) {
				// Recommend you don't use this short code registering your own post data.
				$content .= do_shortcode( '[socialbuzz]' );
			};

			return $content;
		}

		/**
		 * Template head css.
		 *
		 * @return string
		 */
		protected function _tmp_head_css() {
			$css = <<<EOI
.vasb_fb {
	background-image: {{thumbnail}};
}
.vasb_fb_like {
	background-color: rgba({{background_color}}, {{opacity}});
	color: {{color}};
}
@media only screen and (min-width : 711px) {
	.vasb_fb_thumbnail {
		background-image: {{thumbnail}};
	}
	.vasb_fb_like {
		background-color: rgba({{background_color}}, 1);
	}
}
EOI;
			$css = trim( preg_replace( array( '/(?:\r\n)|[\r\n]/', '/[\\x00-\\x09\\x0b-\\x1f]/', '/\n/' ), '', $css ) );

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_head_css', $css );
		}
	}
}
