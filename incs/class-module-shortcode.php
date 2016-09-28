<?php
/**
 * WordPress plugin short code class.
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
	 * Class ShortCode.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class ShortCode {
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

			add_filter( VA_SOCIALBUZZ_PREFIX . 'add_shortcode', [ &$this, 'add_shortcode' ] );
		}

		/**
		 * Add short code.
		 *
		 * @param array $atts Short code parameter.
		 *
		 * @return null|string
		 */
		public function add_shortcode( $atts ) {
			$result = null;
			$atts   = shortcode_atts( array(
				'box' => '',
			), $atts, 'socialbuzz' );

			switch ( $atts['box'] ) {
				case 'like':
					$result = self::_shortcode_likeblock();
					break;
				case 'share':
					$result = 'share';
					break;
				case 'follow':
					$result = 'follow';
					break;
				default:
					$result = 'Default';
					break;
			}

			if ( ! empty( $result ) ) {
				$result = str_replace( '{{content}}', $result, self::_tmp_wrapper() );
			}

			return $result;
		}

		/**
		 * Short code the like block.
		 *
		 * @return string
		 */
		protected function _shortcode_likeblock() {
			$output  = null;
			$tmp     = self::_tmp_likeblock();
			$options = $this->options;

			if ( ! empty( $options['fb_page'] ) ) {
				$text   = [];
				$output = $tmp;

				if ( ! empty( $options['text_like_0'] ) ) {
					$text[] = esc_html( $options['text_like_0'] );
				}

				if ( ! empty( $options['text_like_1'] ) ) {
					$text[] = esc_html( $options['text_like_1'] );
				}

				$output = str_replace( '{{text}}', implode( '<br>', $text ), $output );
				$output = str_replace( '{{fb_page}}', esc_attr( $options['fb_page'] ), $output );
			}

			return $output;
		}

		/**
		 * Wrapper Template.
		 *
		 * @return string
		 */
		protected function _tmp_wrapper() {
			$tmp = '<div id="va-social-buzz" class="va-social-buzz">';
			$tmp .= '{{content}}';
			$tmp .= '</div><!-- //.va-social-buzz -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_wrapper', $tmp );
		}

		/**
		 * Like block Template.
		 *
		 * @return string
		 */
		protected function _tmp_likeblock() {
			$tmp = '<div class="vasb_fb">';
			$tmp .= '<div class="vasb_fb_thumbnail"></div>';
			$tmp .= '<div class="vasb_fb_like">';
			$tmp .= '<p class="vasb_fb_like_text">{{text}}</p>';
			$tmp .= '<div class="fb-like" data-href="https://www.facebook.com/{{fb_page}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>';
			$tmp .= '</div><!-- //.vasb_fb_like -->';
			$tmp .= '</div><!-- //.vasb_fb -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_likeblock', $tmp );
		}
	}
}
