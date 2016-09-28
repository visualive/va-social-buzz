<?php
/**
 * WordPress plugin core class.
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
	 * Class Core.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class Core {
		use Instance;

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		private function __construct() {
			self::init();
		}

		/**
		 * Singleton.
		 */
		protected function init() {
			$install   = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_install', Install::get_called_class() );
			$uninstall = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_uninstall', Uninstall::get_called_class() );
			$update    = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_update', Update::get_called_class() );
			$admin     = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_admin', Admin::get_called_class() );

			$install::get_instance();
			$uninstall::get_instance();
			$update::get_instance();
			$admin::get_instance();

			// Recommend you don't use this short code registering your own post data.
			add_shortcode( 'socialbuzz', array( &$this, 'add_shortcode' ) );
			add_filter( 'the_content', array( &$this, 'the_content' ) );
		}

		/**
		 * Add short code.
		 * Recommend you don't use this short code registering your own post data.
		 */
		public function add_shortcode() {
			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'add_shortcode', null );
		}

		/**
		 * Show in Social Buzz.
		 *
		 * @param string $content The content.
		 *
		 * @return string
		 */
		public function the_content( $content ) {
			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'the_content', $content );
		}
	}
}
