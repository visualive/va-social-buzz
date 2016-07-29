<?php
/**
 * WordPress plugin content class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
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

namespace VASOCIALBUZZ\Modules;

use VASOCIALBUZZ\VASOCIALBUZZ_Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Content.
 *
 * @since 0.0.1 (Alpha)
 */
class VASOCIALBUZZ_Content extends VASOCIALBUZZ_Singleton {
	/**
	 * This hook is called once any activated themes have been loaded.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @param array $settings If the set value is required, pass a value in an array.
	 */
	public function __construct( $settings = array() ) {
		$priority = 10;

		add_image_size( 'vasocialbuzz-thumbnail', '980', '9999', false );

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_script' ) );
			add_filter( 'the_content', array( &$this, 'the_content' ), (int) $priority );
		}
	}

	/**
	 * Create content.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function the_content( $content ) {
		$content = array(
			$content,
			self::_template_content(),
		);

		return implode( PHP_EOL, $content );
	}

	/**
	 * Get thumbnail image url
	 *
	 * @param null|\WP_Post $post
	 *
	 * @return string
	 */
	public function get_thumbnail( $post = null ) {
		$thumb = '';
		if ( has_post_thumbnail( $post ) && ! post_password_required( $post ) ) {
			$thumb = get_the_post_thumbnail_url( $post, 'vasocialbuzz-thumbnail' );
		} elseif ( has_site_icon() ) {
			$thumb = get_site_icon_url();
		} elseif ( has_header_image() ) {
			$thumb = get_header_image();
		}

		return apply_filters( 'vasocialbuzz_get_thumbnail' , $thumb );
	}

	/**
	 * Echo scripts.
	 *
	 * @since 0.0.1 (Alpha)
	 */
	public function wp_enqueue_script() {
		$options      = self::get_option();
		$dummy_option = self::_dummy_option();
		$file_prefix  = ( defined( 'WP_DEBUG' ) && true == WP_DEBUG ) ? '' : '.min';

		if ( empty( $options['post_type'] ) ) {
			$options['post_type'] = apply_filters( 'vasocialbuzz_showin_post_type', $dummy_option['post_type'] );
		}

		//		if ( ! is_singular() || ! in_array( get_post_type(), (array) $options['post_type'] ) ) {
		//			return null;
		//		}

		$options['like_button_area'] = array_merge( $dummy_option['like_button_area'], $options['like_button_area'] );
		$bg                          = esc_attr( implode( ',', self::_hex_to_rgb( $options['like_button_area']['bg'] ) ) );
		$opacity                     = esc_attr( $options['like_button_area']['bg_opacity'] );
		$color                       = esc_attr( $options['like_button_area']['color'] );
		$localize['locale']          = esc_attr( self::_get_locale() );

		if ( has_post_thumbnail() && ! post_password_required() ) {
			$thumb = sprintf( 'url(%s)', esc_url( get_the_post_thumbnail_url( null, 'vasocialbuzz-thumbnail' ) ) );
		} elseif ( has_site_icon() ) {
			$thumb = sprintf( 'url(%s)', esc_url( get_site_icon_url() ) );
		} elseif ( has_header_image() ) {
			$thumb = sprintf( 'url(%s)', esc_url( get_header_image() ) );
		} else {
			$thumb = 'none';
		}

		$css = <<<EOI
.vasb_fb {
	background-image: {$thumb};
}
.vasb_fb_like {
	background-color: rgba({$bg}, {$opacity});
	color: {$color};
}
@media only screen and (min-width : 415px) {
	.vasb_fb_thumbnail {
		background-image: {$thumb};
	}
	.vasb_fb_like {
		background-color: rgba({$bg}, 1);
	}
}
EOI;
		$css = trim( preg_replace(
			array( '/(?:\r\n)|[\r\n]/', '/[\\x00-\\x09\\x0b-\\x1f]/', '/\n/' ),
			'',
			$css
		) );

		if ( isset( $options['fb_appid'] ) && ! empty( $options['fb_appid'] ) ) {
			$localize['appid'] = esc_attr( $options['fb_appid'] );
		}

		wp_enqueue_style( 'va-social-buzz', VASOCIALBUZZ_URL . 'assets/css/style' . $file_prefix . '.css', array(), VASOCIALBUZZ_VERSION );
		wp_add_inline_style( 'va-social-buzz', $css );
		wp_enqueue_script( 'va-social-buzz', VASOCIALBUZZ_URL . 'assets/js/script' . $file_prefix . '.js', array( 'jquery' ), VASOCIALBUZZ_VERSION, true );
		wp_localize_script( 'va-social-buzz', 'vaSocialBuzzSettings', $localize );
	}

	/**
	 * Content template.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return string
	 */
	protected function _template_content() {
		$options      = self::get_option();
		$dummy_option = self::_dummy_option();

		if ( empty( $options['post_type'] ) ) {
			$options['post_type'] = apply_filters( 'vasocialbuzz_showin_post_type', $dummy_option['post_type'] );
		}

		if ( ! is_singular() || ! in_array( get_post_type(), (array) $options['post_type'] ) ) {
			return null;
		}

		$template = self::_template_body();

		return apply_filters( 'vasocialbuzz_template_content', $template, $options );
	}

	/**
	 * Content template.
	 *
	 * @since 1.0.14
	 * @return string
	 */
	protected function _template_body() {
		$options     = self::get_option();
		$_template   = array();
		$_template[] = '<div id="va-social-buzz" class="va-social-buzz">';
		$_template[] = apply_filters( 'vasocialbuzz_template_content_before', '', $options );
		$_template[] = self::_template_fb_page();
		$_template[] = self::_template_share();
		$_template[] = self::_template_push7();
		$_template[] = self::_template_follow();
		$_template[] = apply_filters( 'vasocialbuzz_template_content_after', '', $options );
		$_template[] = '<!-- //.va-social-buzz --></div>';
		$template    = apply_filters( 'vasocialbuzz_template_body', $_template, $options );

		return implode( PHP_EOL, $template );
	}

	/**
	 * Facebook like.
	 *
	 * @return string
	 */
	protected function _template_fb_page() {
		$options  = self::get_option();
		$template = array();

		if ( ! empty( $options['fb_page'] ) ) {
			$template[] = '<div class="vasb_fb">';
			$template[] = '<div class="vasb_fb_thumbnail"></div>';
			$template[] = '<div class="vasb_fb_like">';
			$template[] = sprintf(
				'<p>%s<br>%s</p>',
				esc_html( $options['text']['like'][0] ),
				esc_html( $options['text']['like'][1] )
			);
			$template[] = sprintf(
				'<div class="fb-like" data-href="https://www.facebook.com/%s" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>',
				esc_attr( $options['fb_page'] )
			);
			$template[] = '</div>';
			$template[] = '<!-- //.vasb_fb --></div>';
		}

		return implode( PHP_EOL, $template );
	}

	/**
	 * SNS share.
	 *
	 * @return array
	 */
	protected function _template_share() {
		$options  = self::get_option();
		$sns_list = self::_sns_list();
		$template = array();

		$template['before'] = '<div class="vasb_share">';

		foreach ( $sns_list as $key => $value ) {
			$template[ $key ] = sprintf( '<div class="vasb_share_button vasb_share_button-%s">', esc_html( $value['prefix'] ) );
			$template[ $key ] .= sprintf( '<a href="%s">', esc_html( $value['endpoint'] ) );
			$template[ $key ] .= '<i class="vasb_icon"></i>';
			$template[ $key ] .= sprintf( '<span>%s</span>', esc_html( $options['text'][ $key ] ) );
			$template[ $key ] .= '</a>';
			$template[ $key ] .= '</div>';
		}

		$template['after'] = '<!-- //.vasb_share --></div>';

		$template = implode( PHP_EOL, $template );
		$template = str_replace( '%permalink%', rawurlencode( get_the_permalink() ), $template );
		$template = str_replace( '%post_title%', rawurlencode( get_the_title() ), $template );

		return $template;
	}

	/**
	 * Twitter follow.
	 *
	 * @return string
	 */
	protected function _template_follow() {
		$options  = self::get_option();
		$template = array();

		if ( ! empty( $options['tw_account'] ) ) {
			$template[] = '<div class="vasb_tw">';
			$template[] = sprintf(
				'%1$s <a href="https://twitter.com/%2$s" class="twitter-follow-button" data-show-count="true" data-size="large" data-show-screen-name="false">Follow @%2$s</a>',
				esc_html( $options['text']['follow'] ),
				esc_html( $options['tw_account'] )
			);
			$template[] = '<!-- //.vasb_tw --></div>';
		}

		return implode( PHP_EOL, $template );
	}

	/**
	 * Push notification.
	 *
	 * @return array
	 */
	protected function _template_push7() {
		$options            = self::get_option();
		$template           = array();
		$push7_register_url = self::_get_push7_register_url();

		if ( is_null( $push7_register_url ) ) {
			return null;
		}

		$template['before'] = '<div class="vasb_push">';
		$template['body']   = '<div class="vasb_push_button">';
		$template['body'] .= sprintf( '<a href="%s">', esc_url( $push7_register_url ) );
		$template['body'] .= '<i class="vasb_icon"></i>';
		$template['body'] .= sprintf( '<span>%s</span>', esc_html( $options['text']['push7'] ) );
		$template['body'] .= '</a>';
		$template['body'] .= '</div>';
		$template['after'] = '<!-- //.vasb_push --></div>';

		return implode( PHP_EOL, $template );
	}

	/**
	 * Get Push7 register url.
	 *
	 * @return null|string
	 */
	protected function _get_push7_register_url() {
		$push7_appno        = get_option( 'push7_appno', null );
		$push7_register_url = get_transient( 'vasocialbuzz_push7_register_url' );

		if ( is_null( $push7_appno ) || empty( $push7_appno ) ) {
			$push7_register_url = null;
		} else {
			$push7_appno = preg_replace( '/[^a-zA-Z0-9]/', '', $push7_appno );
		}

		if ( false === $push7_register_url ) {
			$push7_api      = 'https://api.push7.jp/api/v1/' . $push7_appno . '/head';
			$push7_response = wp_remote_get( $push7_api );
			$response_code  = wp_remote_retrieve_response_code( $push7_response );
			$body           = wp_remote_retrieve_body( $push7_response );

			if ( 200 === $response_code && ! empty( $body ) ) {
				$body = json_decode( $body );
			} else {
				return null;
			}

			$domain = isset( $body->domain ) ? filter_var( $body->domain, FILTER_SANITIZE_URL ) : null;
			$alias  = isset( $body->alias ) ? filter_var( $body->alias, FILTER_SANITIZE_URL ) : null;

			if ( ! isset( $domain ) ) {
				return null;
			}

			if ( isset( $domain ) ) {
				$push7_register_url = esc_url_raw( 'https://' . $domain );
			}
			if ( isset( $alias ) ) {
				$push7_register_url = esc_url_raw( 'https://' . $alias );
			}

			set_transient( 'vasocialbuzz_push7_register_url', $push7_register_url, WEEK_IN_SECONDS );
		}

		return $push7_register_url;
	}

	/**
	 * Output the locale, doing some conversions to make sure the proper Facebook locale is outputted.
	 * Yoast SEO Plugin Thanks ! https://yoast.com/wordpress/plugins/seo/
	 *
	 * @see  http://www.facebook.com/translations/FacebookLocales.xml for the list of supported locales
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 * @return string $locale
	 */
	protected function _get_locale() {
		$locale     = apply_filters( 'vasocialbuzz_locale', get_locale() );
		$locales    = apply_filters(
			'va_social_buzz_locales', array(
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
		$fb_locales = apply_filters(
			'va_social_buzz_fb_locales', array(
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
		} elseif ( strlen( $locale ) == 2 ) {
			$locale = strtolower( $locale ) . '_' . strtoupper( $locale );
		} else {
			$locale = strtolower( substr( $locale, 0, 2 ) ) . '_' . strtoupper( substr( $locale, 0, 2 ) );
		}

		// Check to see if the locale is a valid FB one, if not, use en_US as a fallback.
		if ( ! in_array( $locale, $fb_locales ) ) {
			$locale = 'en_US';
		}

		return $locale;
	}

	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @link   http://php.net/manual/ja/function.hexdec.php
	 * @since  0.0.1 (Alpha)
	 *
	 * @param  string  $hexStr         (hexadecimal color value)
	 * @param  boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param  string  $seperator      (to separate RGB values. Applicable only if second parameter is true.)
	 *
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	function _hex_to_rgb( $hexStr, $returnAsString = false, $seperator = ',' ) {
		$dummy_options = self::_dummy_option();
		$hexStr        = preg_replace( '/[^0-9A-Fa-f]/', '', $hexStr );
		$rgbArray      = array();

		if ( strlen( $hexStr ) == 6 ) {
			$colorVal          = hexdec( $hexStr );
			$rgbArray['red']   = 0xFF & ( $colorVal >> 0x10 );
			$rgbArray['green'] = 0xFF & ( $colorVal >> 0x8 );
			$rgbArray['blue']  = 0xFF & $colorVal;
		} elseif ( strlen( $hexStr ) == 3 ) {
			$rgbArray['red']   = hexdec( str_repeat( substr( $hexStr, 0, 1 ), 2 ) );
			$rgbArray['green'] = hexdec( str_repeat( substr( $hexStr, 1, 1 ), 2 ) );
			$rgbArray['blue']  = hexdec( str_repeat( substr( $hexStr, 2, 1 ), 2 ) );
		} else {
			return self::_hex_to_rgb( $dummy_options['like_button_area']['bg'] );
		}

		return $returnAsString ? implode( $seperator, $rgbArray ) : $rgbArray;
	}
}
