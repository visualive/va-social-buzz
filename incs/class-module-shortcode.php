<?php
/**
 * WordPress plugin short code class.
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
		protected function __construct() {
			$this->options = Options::get( 'all' );

			add_filter( VA_SOCIALBUZZ_PREFIX . 'add_shortcode', [ &$this, 'add_shortcode' ] );
			add_filter( VA_SOCIALBUZZ_PREFIX . 'create_the_content', [ &$this, 'the_content' ], -10 );
			add_filter( 'widget_text', [ &$this, 'widget_text' ], -10 );
			add_filter( 'wp_insert_post_data', [ &$this, 'wp_insert_post_data' ], -10 );
		}

		/**
		 * Add short code.
		 * Recommend you don't use this short code registering your own post data.
		 *
		 * @param array $atts Short code parameter.
		 *
		 * @return null|string
		 */
		public function add_shortcode( $atts = [] ) {
			$output               = null;
			$tmp_wrapper          = self::_tmp_wrapper();
			$show_in_like         = apply_filters( VA_SOCIALBUZZ_PREFIX . 'show_in_like', true );
			$show_in_share        = apply_filters( VA_SOCIALBUZZ_PREFIX . 'show_in_share', true );
			$show_in_notification = apply_filters( VA_SOCIALBUZZ_PREFIX . 'show_in_notification', true );
			$show_in_follow       = apply_filters( VA_SOCIALBUZZ_PREFIX . 'show_in_follow', true );
			$doing                = apply_filters( VA_SOCIALBUZZ_PREFIX . 'show_in_doing', doing_filter( VA_SOCIALBUZZ_PREFIX . 'doing_show_in' ) );
			$atts                 = shortcode_atts( array(
				'box'           => '',
				'mode'          => '',
				'likebox_text1' => '',
				'likebox_text2' => '',
			), $atts, 'socialbuzz' );

			if ( true !== $doing ) {
				return $output;
			}

			switch ( $atts['box'] ) {
				case 'like':
					$output = self::_shortcode_likeblock( $atts['likebox_text1'], $atts['likebox_text2'], $atts['mode'] );
					break;
				case 'share':
					$output = self::_shortcode_shareblock();
					break;
				case 'notification':
					$output = self::_shortcode_notificationblock();
					break;
				case 'follow':
					$output = self::_shortcode_followblock();
					break;
				case 'select':
					if ( true === $show_in_like ) {
						$output[] = self::_shortcode_likeblock( $atts['likebox_text1'], $atts['likebox_text2'], $atts['mode'] );
					}
					if ( true === $show_in_share ) {
						$output[] = self::_shortcode_shareblock();
					}
					if ( true === $show_in_notification ) {
						$output[] = self::_shortcode_notificationblock();
					}
					if ( true === $show_in_follow ) {
						$output[] = self::_shortcode_followblock();
					}
					break;
				default:
					$output[] = self::_shortcode_likeblock( $atts['likebox_text1'], $atts['likebox_text2'], $atts['mode'] );
					$output[] = self::_shortcode_shareblock();
					$output[] = self::_shortcode_notificationblock();
					$output[] = self::_shortcode_followblock();
					break;
			}

			if ( is_array( $output ) ) {
				$output = implode( '', $output );
			}

			if ( ! empty( $output ) && 'widget' !== $atts['mode'] ) {
				$output = str_replace( '{{content}}', $output, $tmp_wrapper );
			}

			return $output;
		}

		/**
		 * Rnsert post data.
		 *
		 * @param array $data Post data.
		 *
		 * @return array
		 */
		public function wp_insert_post_data( $data = [] ) {
			$raw                  = $data['post_content'];
			$data['post_content'] = self::_strip_short_code( $data['post_content'] );

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'xxx_insert_post_data', $data, $raw );
		}

		/**
		 * The content.
		 *
		 * @param string $content Post content.
		 *
		 * @return string
		 */
		public function the_content( $content = '' ) {
			$raw     = $content;
			$content = self::_strip_short_code( $content );

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'xxx_the_content', $content, $raw );
		}

		/**
		 * The content.
		 *
		 * @param string $text Post content.
		 *
		 * @return string
		 */
		public function widget_text( $text = '' ) {
			$raw  = $text;
			$text = self::_strip_short_code( $text );

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'xxx_widget_text', $text, $raw );
		}

		/**
		 * Short code the like block.
		 *
		 * @param string $text1
		 * @param string $text2
		 * @param string $mode
		 *
		 * @return string
		 */
		protected function _shortcode_likeblock( $text1 = '', $text2 = '', $mode = '' ) {
			$output  = null;
			$tmp     = self::_tmp_likeblock();
			$options = $this->options;

			if ( ! empty( $options['fb_page'] ) ) {
				$text   = [];
				$output = $tmp;

				if ( ! empty( $text1 ) && 'widget' === $mode ) {
					$text[] = esc_html( $text1 );
				} elseif ( ! empty( $options['text_like_0'] ) && 'widget' !== $mode ) {
					$text[] = esc_html( $options['text_like_0'] );
				}

				if ( ! empty( $text2 ) && 'widget' === $mode ) {
					$text[] = esc_html( $text2 );
				} elseif ( ! empty( $options['text_like_1'] ) && 'widget' !== $mode ) {
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
		 * @return string
		 */
		protected function _shortcode_shareblock() {
			$output        = null;
			$tmp           = self::_tmp_shareblock();
			$tmp_wrapper   = self::_tmp_wrapper_shareblock();
			$sns           = Variable::sns_list();
			$current_url   = Functions::get_current_url();
			$current_title = wp_get_document_title();

			if ( ! empty( $current_url ) && ! empty( $current_title ) ) {
				foreach ( $sns as $key => $value ) {
					$output[ $key ] = $tmp;
					$output[ $key ] = str_replace( '{{prefix}}', sanitize_html_class( $key ), $output[ $key ] );
					$output[ $key ] = str_replace( '{{endpoint}}', $value['endpoint'], $output[ $key ] );
					$output[ $key ] = str_replace( '{{anchor_text}}', $value['anchor_text'], $output[ $key ] );
					$output[ $key ] = str_replace( '{{permalink}}', rawurlencode( esc_url( $current_url ) ), $output[ $key ] );
					$output[ $key ] = str_replace( '{{post_title}}', rawurlencode( html_entity_decode( $current_title, ENT_NOQUOTES | ENT_HTML5, get_bloginfo( 'charset' ) ) ), $output[ $key ] );
				}

				$output = implode( '', $output );
			}

			if ( ! empty( $output ) ) {
				$output = str_replace( '{{content}}', $output, $tmp_wrapper );
			}

			return $output;
		}

		/**
		 * Short code the notification block.
		 *
		 * @return string
		 */
		protected function _shortcode_notificationblock() {
			$output      = null;
			$tmp         = self::_tmp_notificationblock();
			$tmp_wrapper = self::_tmp_wrapper_notificationblock();
			$sns         = Variable::notification_list();

			foreach ( $sns as $key => $value ) {
				$output[ $key ] = $tmp;
				$output[ $key ] = str_replace( '{{prefix}}', sanitize_html_class( $key ), $output[ $key ] );
				$output[ $key ] = str_replace( '{{endpoint}}', $value['endpoint'], $output[ $key ] );
				$output[ $key ] = str_replace( '{{anchor_text}}', $value['anchor_text'], $output[ $key ] );
			}

			if ( is_array( $output ) && ! empty( $output ) ) {
				$output = str_replace( '{{content}}', implode( '', $output ), $tmp_wrapper );
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
				$output = str_replace( '{{twttr_name}}', esc_attr( $options['twttr_name'] ), $output );
				$output = str_replace( '{{text}}', esc_html( $options['text_follow'] ) . ' ', $output );
			}

			return $output;
		}

		/**
		 * Wrapper Template.
		 *
		 * @return string
		 */
		protected function _tmp_wrapper() {
			$tmp = '<div class="va-social-buzz">';
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
		 * Wrapper Template.
		 *
		 * @return string
		 */
		protected function _tmp_wrapper_notificationblock() {
			$tmp = '<div class="vasb_notification">';
			$tmp .= '{{content}}';
			$tmp .= '</div><!-- //.vasb_notification -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_wrapper_notificationblock', $tmp );
		}

		/**
		 * Notification block Template.
		 *
		 * @return string
		 */
		protected function _tmp_notificationblock() {
			$tmp = '<div class="vasb_notification_button vasb_notification_button-{{prefix}}">';
			$tmp .= '<a href="{{endpoint}}">';
			$tmp .= '<i class="vasb_icon"></i>';
			$tmp .= '<span class="vasb_share_button_text">{{anchor_text}}</span>';
			$tmp .= '</a>';
			$tmp .= '</div><!-- //.vasb_notification_button-{{prefix}} -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_notificationblock', $tmp );
		}

		/**
		 * Followb block Template.
		 *
		 * @return string
		 */
		protected function _tmp_followblock() {
			$tmp = '<div class="vasb_follow">';
			$tmp .= '{{text}}<a href="https://twitter.com/{{twttr_name}}" class="twitter-follow-button" data-show-count="true" data-size="large" data-show-screen-name="false">Follow {{twttr_name}}</a>';
			$tmp .= '</div><!-- //.vasb_tw -->';

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'tmp_followblock', $tmp );
		}

		/**
		 * Strip short code.
		 *
		 * @param string $text Content text.
		 *
		 * @return string
		 */
		protected function _strip_short_code( $text = '' ) {
			if ( has_shortcode( $text, 'socialbuzz' ) ) {
				$text = preg_replace( '/' . get_shortcode_regex( [ 'socialbuzz' ] ) . '/', '', $text );
			}

			return $text;
		}
	}
}
