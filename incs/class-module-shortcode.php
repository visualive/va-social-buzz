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
			global $post;

			$output      = null;
			$tmp_wrapper = self::_tmp_wrapper();
			$atts        = shortcode_atts( array(
				'box' => '',
			), $atts, 'socialbuzz' );

			switch ( $atts['box'] ) {
				case 'like':
					$output = self::_shortcode_likeblock();
					break;
				case 'share':
					$output = self::_shortcode_shareblock( $post );
					break;
				case 'follow':
					$output = self::_shortcode_followblock();
					break;
				default:
					$output = self::_shortcode_likeblock();
					$output .= self::_shortcode_shareblock( $post );
					$output .= self::_shortcode_followblock();
					break;
			}

			if ( ! empty( $output ) ) {
				$output = str_replace( '{{content}}', $output, $tmp_wrapper );
			}

			return $output;
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
		 * Short code the share block.
		 *
		 * @param null|\WP_Query $post The post object.
		 *
		 * @return string
		 */
		protected function _shortcode_shareblock( $post = null ) {
			$output      = null;
			$tmp         = self::_tmp_shareblock();
			$tmp_wrapper = self::_tmp_wrapper_shareblock();
			$sns         = Variable::sns_list();

			if ( ! empty( $post ) && ! is_wp_error( $post ) ) {
				foreach ( $sns as $key => $value ) {
					$output[ $key ] = $tmp;
					$output[ $key ] = str_replace( '{{prefix}}', sanitize_html_class( $key ), $output[ $key ] );
					$output[ $key ] = str_replace( '{{endpoint}}', $value['endpoint'], $output[ $key ] );
					$output[ $key ] = str_replace( '{{anchor_text}}', $value['anchor_text'], $output[ $key ] );
					$output[ $key ] = str_replace( '{{permalink}}', rawurlencode( get_the_permalink( $post->ID ) ), $output[ $key ] );
					$output[ $key ] = str_replace( '{{post_title}}', rawurlencode( get_the_title( $post->ID ) ), $output[ $key ] );
				}

				$output = implode( '', $output );
			}

			if ( ! empty( $output ) ) {
				$output = str_replace( '{{content}}', $output, $tmp_wrapper );
			}

			return $output;
		}

		/**
		 * Short code the follow block.
		 *
		 * @return string
		 */
		protected function _shortcode_followblock() {
			$output  = null;
			$tmp     = self::_tmp_followblock();
			$options = $this->options;

			if ( ! empty( $options['twttr_name'] ) ) {
				$output = $tmp;
				$twttr  = esc_attr( $options['twttr_name'] );
				$output = str_replace( '{{twttr_name}}', $twttr, $output );

				if ( ! empty( $options['text_follow'] ) ) {
					$output = str_replace( '{{text}}', esc_html( $options['text_follow'] ) . ' ', $output );
				}
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

		/**
		 * Wrapper Template.
		 *
		 * @return string
		 */
		protected function _tmp_wrapper_shareblock() {
			$tmp = '<div class="vasb_share">';
			$tmp .= '{{content}}';
			$tmp .= '</div><!-- //.vasb_share -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_wrapper_shareblock', $tmp );
		}

		/**
		 * Share block Template.
		 *
		 * @return string
		 */
		protected function _tmp_shareblock() {
			$tmp = '<div class="vasb_share_button vasb_share_button-{{prefix}}">';
			$tmp .= '<a href="{{endpoint}}">';
			$tmp .= '<i class="vasb_icon"></i>';
			$tmp .= '<span class="vasb_share_button_text">{{anchor_text}}</span>';
			$tmp .= '</a>';
			$tmp .= '</div><!-- //.vasb_share_button-{{prefix}} -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_shareblock', $tmp );
		}

		/**
		 * Followb block Template.
		 *
		 * @return string
		 */
		protected function _tmp_followblock() {
			$tmp = '<div class="vasb_tw">';
			$tmp .= '{{text}}<a href="https://twitter.com/{{twttr_name}}" class="twitter-follow-button" data-show-count="true" data-size="large" data-show-screen-name="false">Follow {{twttr_name}}</a>';
			$tmp .= '</div><!-- //.vasb_tw -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_followblock', $tmp );
		}
	}
}
