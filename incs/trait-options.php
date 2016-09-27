<?php
/**
 * WordPress plugin option class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @since      1.1.0
 * @author     KUCKLU <kuck1u@visualive.jp>
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
	 * Class Options.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	trait Options {
		/**
		 * Get option.
		 *
		 * @param string $key Option key.
		 *
		 * @return string|array
		 */
		public static function get( $key = '' ) {
			$options = get_option( VA_SOCIALBUZZ_NAME_OPTION, [] );

			if ( '' !== $key && 'all' !== $key && isset( $options[ $key ] ) ) {
				$result = $options[ $key ];
			} else {
				$result = $options;
			}

			return $result;
		}

		/**
		 * Update options.
		 *
		 * @param string|array $value Option value.
		 * @param string       $key   Option key.
		 */
		public static function update( $value = '', $key = '' ) {
			$options = get_option( VA_SOCIALBUZZ_NAME_OPTION, [] );

			if ( '' === $value ) {
				return;
			}

			if ( ! is_array( $value ) && '' !== $key ) {
				$options[ $key ] = $value;
			} elseif ( is_array( $value ) ) {
				$options = $value;
			}

			update_option( VA_SOCIALBUZZ_NAME_OPTION, $options );
		}
	}
}
