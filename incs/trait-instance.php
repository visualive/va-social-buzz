<?php
/**
 * WordPress plugin instance class.
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
	trait Instance {
		/**
		 * Holds the instance of this class
		 *
		 * @var array
		 */
		private static $instance = [];

		/**
		 * Instance.
		 *
		 * @param  array $settings If the set value is required, pass a value in an array.
		 *
		 * @return self
		 */
		public static function get_instance( $settings = [] ) {
			$class = get_called_class();

			if ( ! isset( self::$instance[ $class ] ) ) {
				self::$instance[ $class ] = new $class( $settings );
			}

			return self::$instance[ $class ];
		}

		/**
		 * Get my class.
		 *
		 * @return string
		 */
		public static function get_called_class() {
			return get_called_class();
		}
	}
}
