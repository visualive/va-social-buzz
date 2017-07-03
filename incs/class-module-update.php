<?php
/**
 * WordPress plugin update class.
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

	/**
	 * Class Update.
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class Update {
		use Instance, Options;

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		private function __construct() {
			add_action( VA_SOCIALBUZZ_PREFIX . 'update', [ &$this, 'run' ] );
			add_action( 'init', [ Installer::get_called_class(), 'update' ] );
			add_action( 'admin_init', [ Installer::get_called_class(), 'update' ] );
		}

		/**
		 * Run update.
		 */
		public function run() {
			$options    = Options::get( 'raw' );
			$db_version = isset( $options['db_version'] ) ? $options['db_version'] : '0';

			self::version_0( $options, $db_version );
		}

		/**
		 * Run update version 0.
		 *
		 * @param array  $options    Options.
		 * @param string $db_version Option version.
		 */
		protected function version_0( $options = false, $db_version = '0' ) {
			if ( false !== $options && ! empty( $options ) && version_compare( '0', $db_version, '==' ) ) {
				$transient_key = 'vasocialbuzz_push7_register_url';
				$transient     = get_transient( $transient_key );
				$old_options   = $options;
				$new_options   = [
					'db_version'      => VA_SOCIALBUZZ_VERSION_DB,
					'notices'         => [],
					'fb_page'         => $old_options['fb_page'],
					'fb_appid'        => $old_options['fb_appid'],
					'twttr_name'      => $old_options['tw_account'],
					'text_like_0'     => $old_options['text']['like'][0],
					'text_like_1'     => $old_options['text']['like'][1],
					'text_share'      => $old_options['text']['share'],
					'text_tweet'      => $old_options['text']['tweet'],
					'text_follow'     => $old_options['text']['follow'],
					'like_area_bg'    => $old_options['like_button_area']['bg'],
					'like_area_color' => $old_options['like_button_area']['color'],
					'post_types'      => $old_options['post_type'],
				];

				if ( Functions::exists_push7() ) {
					$new_options['text_push7'] = $old_options['text']['push7'];
				}

				if ( Functions::exists_bcadd() ) {
					$new_options['like_area_opacity'] = $old_options['like_button_area']['bg_opacity'];
				}

				if ( false !== $transient ) {
					delete_transient( $transient_key );
				}

				Options::delete();
				Options::update( $new_options );
			}
		}
	}
}
