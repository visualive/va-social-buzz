<?php
/**
 * WordPress plugin functions class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
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
	 * Class Instance.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	trait Functions {
		/**
		 * Exists push7.
		 *
		 * @return bool
		 */
		public static function exists_push7() {
			return ! is_null( get_option( 'push7_appno', null ) );
		}

		/**
		 * Exists bcadd function.
		 *
		 * @return bool
		 */
		public static function exists_bcadd() {
			return function_exists( 'bcadd' );
		}

		/**
		 * Get thumbnail image url
		 *
		 * @param null|\WP_Post $_post Post data object.
		 *
		 * @return string
		 */
		public static function get_thumbnail( $_post = null ) {
			global $post;

			$thumb          = apply_filters( VA_SOCIALBUZZ_PREFIX . 'default_thumbnail', 'none' );
			$show_on_front  = get_option( 'show_on_front' );
			$page_on_front  = get_option( 'page_on_front' );
			$page_for_posts = get_option( 'page_for_posts' );

			if ( empty( $_post ) && 'page' === $show_on_front && is_front_page() ) {
				$_post = get_post( $page_on_front );
			} elseif ( empty( $_post ) && 'page' === $show_on_front && is_home() ) {
				$_post = get_post( $page_for_posts );
			}

			if ( empty( $_post ) && is_singular() ) {
				$_post = $post;
			}

			if ( ! empty( $_post ) && has_post_thumbnail( $_post ) && ! post_password_required( $_post ) ) {
				$thumb = get_the_post_thumbnail_url( $_post, VA_SOCIALBUZZ_PREFIX . 'thumbnail' );
			} elseif ( 'none' === $thumb && has_header_image() ) {
				$thumb = get_header_image();
			} elseif ( 'none' === $thumb && has_site_icon() ) {
				$thumb = get_site_icon_url();
			}

			return $thumb;
		}

		/**
		 * Get Push7 register url.
		 *
		 * @return null|string
		 */
		public static function get_push7_register_url() {
			$push7              = false;
			$transient_key      = VA_SOCIALBUZZ_PREFIX . 'push7_register_url';
			$push7_appno        = get_option( 'push7_appno', null );
			$push7_register_url = get_transient( $transient_key );

			if ( ! empty( $push7_appno ) ) {
				$push7_appno = preg_replace( '/[^a-zA-Z0-9]/', '', $push7_appno );
				$push7       = true;
			}

			if ( false === $push7_register_url && true === $push7 ) :
				$endpoint      = sprintf( 'https://api.push7.jp/api/v1/%s/head', $push7_appno );
				$response      = wp_remote_get( $endpoint );
				$response_code = wp_remote_retrieve_response_code( $response );
				$body          = wp_remote_retrieve_body( $response );

				if ( 200 === $response_code && ! empty( $body ) ) {
					$body = json_decode( $body );
				}

				if ( ! empty( $body ) ) {
					$domain = isset( $body->domain ) ? filter_var( $body->domain, FILTER_SANITIZE_URL ) : false;
					$alias  = isset( $body->alias ) ? filter_var( $body->alias, FILTER_SANITIZE_URL ) : false;

					if ( false !== $alias ) {
						$push7_register_url = esc_url_raw( 'https://' . $alias );
					}

					if ( false === $alias && false !== $domain ) {
						$push7_register_url = esc_url_raw( 'https://' . $domain );
					}

					if ( false !== $push7_register_url ) {
						set_transient( $transient_key, $push7_register_url, WEEK_IN_SECONDS );
					}
				}
			endif;

			return $push7_register_url;
		}

		/**
		 * Get current url.
		 *
		 * @return string
		 */
		public static function get_current_url() {
			global $wp;
			$wpbitly = get_option( 'wpbitly-options', array() );

			if ( ! $wp->did_permalink ) {
				$current_url = add_query_arg( $wp->query_string, '', home_url( '/' ) );
			} elseif ( isset( $wpbitly ) && ! empty( $wpbitly ) && is_singular() && is_array( $wpbitly ) && in_array( get_post_type(), $wpbitly['post_types'] ) ) {
				$current_url = wp_get_shortlink( 0, 'query' );
			} else {
				$current_url = home_url( add_query_arg( array(), $wp->request ) );
			}

			return $current_url;
		}

		/**
		 * Output the locale, doing some conversions to make sure the proper Facebook locale is outputted.
		 * Yoast SEO Plugin Thanks ! https://yoast.com/wordpress/plugins/seo/
		 *
		 * @see  http://www.facebook.com/translations/FacebookLocales.xml for the list of supported locales
		 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
		 * @return string $locale
		 */
		public static function get_locale() {
			$locale     = get_locale();
			$locales    = apply_filters( VA_SOCIALBUZZ_PREFIX . 'locales', array(
					'ca' => 'ca_ES',
					'en' => 'en_US',
					'el' => 'el_GR',
					'et' => 'et_EE',
					'ja' => 'ja_JP',
					'sq' => 'sq_AL',
					'uk' => 'uk_UA',
					'vi' => 'vi_VN',
					'zh' => 'zh_CN',
				)
			);
			$fb_locales = apply_filters( VA_SOCIALBUZZ_PREFIX . 'fb_locales', array(
					'af_ZA', // Afrikaans.
					'ar_AR', // Arabic.
					'az_AZ', // Azerbaijani.
					'be_BY', // Belarusian.
					'bg_BG', // Bulgarian.
					'bn_IN', // Bengali.
					'bs_BA', // Bosnian.
					'ca_ES', // Catalan.
					'cs_CZ', // Czech.
					'cx_PH', // Cebuano.
					'cy_GB', // Welsh.
					'da_DK', // Danish.
					'de_DE', // German.
					'el_GR', // Greek.
					'en_GB', // English (UK).
					'en_PI', // English (Pirate).
					'en_UD', // English (Upside Down).
					'en_US', // English (US).
					'eo_EO', // Esperanto.
					'es_ES', // Spanish (Spain).
					'es_LA', // Spanish.
					'et_EE', // Estonian.
					'eu_ES', // Basque.
					'fa_IR', // Persian.
					'fb_LT', // Leet Speak.
					'fi_FI', // Finnish.
					'fo_FO', // Faroese.
					'fr_CA', // French (Canada).
					'fr_FR', // French (France).
					'fy_NL', // Frisian.
					'ga_IE', // Irish.
					'gl_ES', // Galician.
					'gn_PY', // Guarani.
					'gu_IN', // Gujarati.
					'he_IL', // Hebrew.
					'hi_IN', // Hindi.
					'hr_HR', // Croatian.
					'hu_HU', // Hungarian.
					'hy_AM', // Armenian.
					'id_ID', // Indonesian.
					'is_IS', // Icelandic.
					'it_IT', // Italian.
					'ja_JP', // Japanese.
					'ja_KS', // Japanese (Kansai).
					'jv_ID', // Javanese.
					'ka_GE', // Georgian.
					'kk_KZ', // Kazakh.
					'km_KH', // Khmer.
					'kn_IN', // Kannada.
					'ko_KR', // Korean.
					'ku_TR', // Kurdish.
					'la_VA', // Latin.
					'lt_LT', // Lithuanian.
					'lv_LV', // Latvian.
					'mk_MK', // Macedonian.
					'ml_IN', // Malayalam.
					'mn_MN', // Mongolian.
					'mr_IN', // Marathi.
					'ms_MY', // Malay.
					'nb_NO', // Norwegian (bokmal).
					'ne_NP', // Nepali.
					'nl_NL', // Dutch.
					'nn_NO', // Norwegian (nynorsk).
					'pa_IN', // Punjabi.
					'pl_PL', // Polish.
					'ps_AF', // Pashto.
					'pt_BR', // Portuguese (Brazil).
					'pt_PT', // Portuguese (Portugal).
					'ro_RO', // Romanian.
					'ru_RU', // Russian.
					'si_LK', // Sinhala.
					'sk_SK', // Slovak.
					'sl_SI', // Slovenian.
					'sq_AL', // Albanian.
					'sr_RS', // Serbian.
					'sv_SE', // Swedish.
					'sw_KE', // Swahili.
					'ta_IN', // Tamil.
					'te_IN', // Telugu.
					'tg_TJ', // Tajik.
					'th_TH', // Thai.
					'tl_PH', // Filipino.
					'tr_TR', // Turkish.
					'uk_UA', // Ukrainian.
					'ur_PK', // Urdu.
					'uz_UZ', // Uzbek.
					'vi_VN', // Vietnamese.
					'zh_CN', // Simplified Chinese (China).
					'zh_HK', // Traditional Chinese (Hong Kong).
					'zh_TW', // Traditional Chinese (Taiwan).
				)
			);

			// Convert locales like "en" to "en_US", in case that works for the given locale (sometimes it does).
			if ( isset( $locales[ $locale ] ) ) {
				$locale = $locales[ $locale ];
			} elseif ( 2 === intval( strlen( $locale ) ) ) {
				$locale = strtolower( $locale ) . '_' . strtoupper( $locale );
			} else {
				$locale = strtolower( substr( $locale, 0, 2 ) ) . '_' . strtoupper( substr( $locale, 0, 2 ) );
			}

			// Check to see if the locale is a valid FB one, if not, use en_US as a fallback.
			if ( ! in_array( $locale, $fb_locales ) ) {
				$locale = 'en_US';
			}

			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'locale', $locale );
		}

		/**
		 * Convert a hexa decimal color code to its RGB equivalent
		 *
		 * @link   http://php.net/manual/ja/function.hexdec.php
		 * @since  0.0.1 (Alpha)
		 *
		 * @param  string  $hexStr         Hexadecimal color value.
		 * @param  boolean $returnAsString If set true, returns the value separated by the separator character. Otherwise returns associative array.
		 * @param  string  $seperator      To separate RGB values. Applicable only if second parameter is true.
		 *
		 * @return array|string|bool Depending on second parameter. Returns False if invalid hex color value.
		 */
		public static function hex_to_rgb( $hexStr, $returnAsString = false, $seperator = ',' ) {
			$result   = false;
			$rgbArray = array();
			$hexStr   = preg_replace( '/[^0-9A-Fa-f]/', '', $hexStr );

			if ( 6 === intval( strlen( $hexStr ) ) ) {
				$colorVal          = hexdec( $hexStr );
				$rgbArray['red']   = 0xFF & ( $colorVal >> 0x10 );
				$rgbArray['green'] = 0xFF & ( $colorVal >> 0x8 );
				$rgbArray['blue']  = 0xFF & $colorVal;
			} elseif ( 3 === intval( strlen( $hexStr ) ) ) {
				$rgbArray['red']   = hexdec( str_repeat( substr( $hexStr, 0, 1 ), 2 ) );
				$rgbArray['green'] = hexdec( str_repeat( substr( $hexStr, 1, 1 ), 2 ) );
				$rgbArray['blue']  = hexdec( str_repeat( substr( $hexStr, 2, 1 ), 2 ) );
			}

			if ( ! empty( $rgbArray ) ) {
				$result = $returnAsString ? implode( $seperator, $rgbArray ) : $rgbArray;
			}

			return $result;
		}
	}
}
