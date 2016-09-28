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
		use Instance;

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		private function __construct() {
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
			$result = null;
			$atts   = shortcode_atts( array(
				'box' => '',
			), $atts, 'socialbuzz' );

			switch ( $atts['box'] ) {
				case 'like':
					$result = 'like';
					break;
				case 'share':
					$result = 'share';
					break;
				case 'follow':
					$result = 'follow';
					break;
				default:
					$result = 'Default';
					break;
			}

			return $result;
		}
	}
}
