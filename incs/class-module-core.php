<?php
/**
 * WordPress plugin core class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @since      1.1.0
 * @author     KUCKLU <oss@visualive.jp>
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

	require_once dirname( __FILE__ ) . '/trait-instance.php';
	require_once dirname( __FILE__ ) . '/trait-functions.php';
	require_once dirname( __FILE__ ) . '/trait-variables.php';
	require_once dirname( __FILE__ ) . '/trait-options.php';
	require_once dirname( __FILE__ ) . '/class-module-installer.php';
	require_once dirname( __FILE__ ) . '/class-module-install.php';
	require_once dirname( __FILE__ ) . '/class-module-uninstall.php';
	require_once dirname( __FILE__ ) . '/class-module-update.php';
	require_once dirname( __FILE__ ) . '/class-module-admin.php';
	require_once dirname( __FILE__ ) . '/class-module-shortcode.php';
	require_once dirname( __FILE__ ) . '/class-module-view.php';

	/**
	 * Class Core.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class Core {
		use Instance, Functions;

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		protected function __construct() {
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
			$shortcode = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_shortcode', ShortCode::get_called_class() );
			$view      = apply_filters( VA_SOCIALBUZZ_PREFIX . 'module_view', View::get_called_class() );

			$install::get_instance();
			$uninstall::get_instance();
			$update::get_instance();
			$admin::get_instance();
			$shortcode::get_instance();
			$view::get_instance();

			add_image_size( VA_SOCIALBUZZ_PREFIX . 'thumbnail', '980', '9999', false );
			// Recommend you don't use this short code registering your own post data.
			add_shortcode( 'socialbuzz', array( &$this, 'add_shortcode' ) );

			if ( ! is_admin() ) {
				add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ), 15 );
				add_filter( 'the_content', array( &$this, 'the_content' ) );
			}
		}

		/**
		 * Echo scripts.
		 */
		public function wp_enqueue_scripts() {
			do_action( VA_SOCIALBUZZ_PREFIX . 'wp_enqueue_scripts' );
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

		/**
		 * Add short code.
		 * Recommend you don't use this short code registering your own post data.
		 *
		 * @param array $atts Short code parameter.
		 *
		 * @return null|string
		 */
		public function add_shortcode( $atts ) {
			return apply_filters( VA_SOCIALBUZZ_PREFIX . 'add_shortcode', $atts );
		}
	}
}
