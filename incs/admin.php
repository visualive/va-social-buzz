<?php
/**
 * WordPress plugin admin class.
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
 * Class ADMIN.
 *
 * @since 0.0.1 (Alpha)
 */
class VASOCIALBUZZ_Admin extends VASOCIALBUZZ_Singleton {
	/**
	 * This hook is called once any activated themes have been loaded.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @param array $settings If the set value is required, pass a value in an array.
	 */
	public function __construct( $settings = array() ) {
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	}

	/**
	 * Add settings.
	 */
	public function admin_init() {
		register_setting( 'reading', 'va_social_buzz', array( &$this, '_sanitize_option' ) );

		add_settings_section( 'vasocialbuzz_section', __( 'VA Social Buzz', 'va-social-buzz' ), null, 'reading' );
		add_settings_field(
			'vasocialbuzz_fb_page',
			'<label for="vasocialbuzz_fb_page">' . __( 'Facebook Page Web Address', 'va-social-buzz' ) . '</label>',
			array( &$this, 'render_fb_page' ),
			'reading',
			'vasocialbuzz_section'
		);
		add_settings_field(
			'vasocialbuzz_fb_appid',
			'<label for="vasocialbuzz_fb_appid">' . __( 'Facebook App ID', 'va-social-buzz' ) . '</label>',
			array( &$this, 'render_fb_appid' ),
			'reading',
			'vasocialbuzz_section'
		);
		add_settings_field(
			'vasocialbuzz_tw_account',
			'<label for="vasocialbuzz_tw_account">' . __( 'Twitter Account', 'va-social-buzz' ) . '</label>',
			array( &$this, 'render_tw_account' ),
			'reading',
			'vasocialbuzz_section'
		);
		add_settings_field(
			'vasocialbuzz_text',
			__( 'Text', 'va-social-buzz' ),
			array( &$this, 'render_text' ),
			'reading',
			'vasocialbuzz_section'
		);
	}

	/**
	 * Facebook page url.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return string
	 */
	public function render_fb_page() {
		$options  = self::get_option();
		$output[] = '<label for="vasocialbuzz_fb_page">https://facebook.com/</label>';
		$output[] = sprintf(
			'<input id="vasocialbuzz_fb_page" class="regular-text code" name="va_social_buzz[fb_page]" value="%s">',
			esc_attr( $options['fb_page'] )
		);
		$output[] = '<p class="description">' . __( 'Facebook Page Web Address can only contain A-Z, a-z, 0-9, and periods (.)', 'va-social-buzz' ) . '</p>';

		echo implode( PHP_EOL, $output );
	}

	/**
	 * Facebook app id.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return string
	 */
	public function render_fb_appid() {
		$options  = self::get_option();
		$output[] = sprintf(
			'<input id="vasocialbuzz_fb_appid" class="regular-text" name="va_social_buzz[fb_appid]" value="%s">',
			esc_attr( $options['fb_appid'] )
		);
		$output[] = '<p class="description">' . __( 'Facebook App ID can only contain 0-9.', 'va-social-buzz' ) . '</p>';

		echo implode( PHP_EOL, $output );
	}

	/**
	 * Twitter account.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return string
	 */
	public function render_tw_account() {
		$options  = self::get_option();
		$output[] = sprintf(
			'<input id="vasocialbuzz_tw_account" class="regular-text code" name="va_social_buzz[tw_account]" value="%s">',
			esc_attr( $options['tw_account'] )
		);
		$output[] = '<p class="description">' . __( 'Twitter Account can only contain A-Z, a-z, 0-9, and underscore (_)', 'va-social-buzz' ) . '</p>';

		echo implode( PHP_EOL, $output );
	}

	public function render_text() {
		$options  = self::get_option();

		$output[] = '<p><label for="vasocialbuzz_text_like_0">';
		$output[] = sprintf(
			'<input id="vasocialbuzz_text_like_0" class="regular-text" name="va_social_buzz[text][like][0]" value="%s">',
			esc_attr( $options['text']['like'][0] )
		);
		$output[] = '</label></p>';
		$output[] = '<p><label for="vasocialbuzz_text_like_1">';
		$output[] = sprintf(
			'<input id="vasocialbuzz_text_like_1" class="regular-text" name="va_social_buzz[text][like][1]" value="%s">',
			esc_attr( $options['text']['like'][1] )
		);
		$output[] = '</label></p>';
		$output[] = '<p class="description">' . __( 'Appear on top of the like button.', 'va-social-buzz' ) . '</p>';

		$output[] = '<p><label for="vasocialbuzz_text_share">';
		$output[] = sprintf(
			'<input id="vasocialbuzz_text_share" class="regular-text" name="va_social_buzz[text][share]" value="%s">',
			esc_attr( $options['text']['share'] )
		);
		$output[] = '</label></p>';
		$output[] = '<p class="description">' . __( 'Share button to Facebook.', 'va-social-buzz' ) . '</p>';

		$output[] = '<p><label for="vasocialbuzz_text_tweet">';
		$output[] = sprintf(
			'<input id="vasocialbuzz_text_tweet" class="regular-text" name="va_social_buzz[text][tweet]" value="%s">',
			esc_attr( $options['text']['tweet'] )
		);
		$output[] = '</label></p>';
		$output[] = '<p class="description">' . __( 'Tweet button to Twitter.', 'va-social-buzz' ) . '</p>';

		$output[] = '<p><label for="vasocialbuzz_text_follow">';
		$output[] = sprintf(
			'<input id="vasocialbuzz_text_follow" class="regular-text" name="va_social_buzz[text][follow]" value="%s">',
			esc_attr( $options['text']['follow'] )
		);
		$output[] = '</label></p>';
		$output[] = '<p class="description">' . __( 'Follow button left of the text.', 'va-social-buzz' ) . '</p>';

		echo implode( PHP_EOL, $output );
	}

	/**
	 * Sanitize.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @param $options array Settings.
	 *
	 * @return array
	 */
	public function _sanitize_option( $options ) {
		$options                    = wp_parse_args( $options, self::dummy_option() );
		$options['fb_page']         = preg_replace( '/[^a-zA-Z0-9.]/', '', $options['fb_page'] );
		$options['fb_appid']        = preg_replace( '/[\D]/', '', $options['fb_appid'] );
		$options['tw_account']      = preg_replace( '/[\W]/', '', $options['tw_account'] );
		$options['text']['like'][0] = sanitize_text_field( $options['text']['like'][0] );
		$options['text']['like'][1] = sanitize_text_field( $options['text']['like'][1] );
		$options['text']['share']   = sanitize_text_field( $options['text']['share'] );
		$options['text']['tweet']   = sanitize_text_field( $options['text']['tweet'] );
		$options['text']['follow']  = sanitize_text_field( $options['text']['follow'] );

		return $options;
	}
}
