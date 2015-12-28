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
	 *
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Plugin prefix.
	 *
	 * @since 0.0.1 (Alpha)
	 *
	 * @var string
	 */
	protected static $prefix = 'vasocialbuzz';

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
	 * Dummy settings.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return array
	 */
	protected function dummy_option() {
		return array(
			'fb_page'    => 'wordpress',
			'fb_appid'   => '',
			'tw_account' => 'wordpress',
			'text'       => array(
				'like'   => array(
					'If you liked this article,',
					'please click this \'like!\'.',
				),
				'share'  => 'Share',
				'tweet'  => 'Tweet',
				'follow' => 'Follow on Twetter !',
			),
		);
	}

	/**
	 * Get settings.
	 *
	 * @since 0.0.1 (Alpha)
	 * @return array
	 */
	protected function get_option() {
		return wp_parse_args( get_option( 'va_social_buzz' ), self::dummy_option() );
	}
}
