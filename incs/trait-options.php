<?php
/**
 * WordPress plugin option class.
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
	 * Class Options.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	trait Options {
		use Variable;

		/**
		 * Get option.
		 *
		 * @param string $key Option key.
		 *
		 * @return string|array
		 */
		public static function get( $key = '' ) {
			$result          = '';
			$options         = get_option( VA_SOCIALBUZZ_NAME_OPTION, [] );
			$default_options = Variable::default_options();

			if ( '' !== $key && ! in_array( $key, [ 'all', 'default', 'raw' ] ) ) {
				$result = isset( $options[ $key ] ) ? $options[ $key ] : $default_options[ $key ];
			} elseif ( 'all' === $key ) {
				$result = wp_parse_args( $options, $default_options );
			} elseif ( 'default' === $key ) {
				$result = $default_options;
			} elseif ( 'raw' === $key ) {
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
			$options = self::get( 'all' );
			$key     = sanitize_key( $key );

			if ( '' === $value ) {
				return;
			}

			if ( is_array( $value ) && '' === $key ) {
				$options = $value;
			} elseif ( '' !== $value && '' !== $key ) {
				$options[ $key ] = $value;
			}

			return update_option( VA_SOCIALBUZZ_NAME_OPTION, $options );
		}

		/**
		 * Delete options.
		 *
		 * @param string $key Option key.
		 */
		public static function delete( $key = '' ) {
			if ( '' === $key ) {
				delete_option( VA_SOCIALBUZZ_NAME_OPTION );
			}

			$options = self::get( 'all' );

			if ( isset( $options[ $key ] ) ) {
				unset( $options[ $key ] );
				self::update( $options );
			}
		}
	}
}
