<?php
/**
 * WordPress plugin singleton class.
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

namespace VASOCIALBUZZ;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SINGLETON.
 *
 * @since 0.0.1 (Alpha)
 */
abstract class VASOCIALBUZZ_Singleton {
	/**
	 * Holds the singleton instance of this class
	 *
	 * @since 0.0.1 (Alpha)
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Instance.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @param  array $settings If the set value is required, pass a value in an array.
	 *
	 * @return self
	 */
	public static function instance( $settings = array() ) {
		$class_name = get_called_class();
		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name( $settings );
		}

		return self::$instances[ $class_name ];
	}

	/**
	 * This hook is called once any activated themes have been loaded.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @param array $settings If the set value is required, pass a value in an array.
	 */
	protected function __construct( $settings = array() ) {
	}

	/**
	 * SNS List.
	 *
	 * @since 1.0.14
	 * @return array
	 */
	protected function _sns_list() {
		$list = apply_filters( 'vasocialbuzz_admin_sns_list', array(
			'share' => array(
				'prefix'   => 'fb',
				'endpoint' => 'https://www.facebook.com/sharer/sharer.php?u=%permalink%',
				'text'     => __( 'Share', 'va-social-buzz' ),
			),
			'tweet' => array(
				'prefix'   => 'tw',
				'endpoint' => 'https://twitter.com/share?url=%permalink%&text=%post_title%',
				'text'     => __( 'Tweet', 'va-social-buzz' )
			),
		) );

		return $list;
	}

	/**
	 * Dummy settings.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return array
	 */
	protected function _dummy_option() {
		$sns_list = self::_sns_list();
		$text     = array(
			'like'   => array(
				__( 'If you liked this article,', 'va-social-buzz' ),
				__( 'please click this "like!".', 'va-social-buzz' ),
			),
			'follow' => __( 'Follow on Twetter !', 'va-social-buzz' ),
			'push7'  => __( 'Receive the latest posts with push notifications', 'va-social-buzz' ),
		);

		foreach ( $sns_list as $key => $sns ) {
			$text[ $key ] = $sns['text'];
		}

		return apply_filters( 'vasocialbuzz_admin_dummy_option', array(
			'fb_page'          => '',
			'fb_appid'         => '',
			'tw_account'       => '',
			'text'             => $text,
			'like_button_area' => array(
				'bg'         => '#2b2b2b',
				'color'      => '#ffffff',
				'bg_opacity' => '0.7',
			),
			'post_type'        => array( 'post' ),
		) );
	}

	/**
	 * Get settings.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return array
	 */
	protected function get_option() {
		$dummy_options               = self::_dummy_option();
		$options                     = wp_parse_args( get_option( 'va_social_buzz' ), $dummy_options );
		$like_button_area            = wp_parse_args( $options['like_button_area'], $dummy_options['like_button_area'] );
		$text                        = wp_parse_args( $options['text'], $dummy_options['text'] );
		$post_type                   = wp_parse_args( $options['post_type'], $dummy_options['post_type'] );
		$options['like_button_area'] = $like_button_area;
		$options['text']             = $text;
		$options['post_type']        = $post_type;

		return $options;
	}
}
