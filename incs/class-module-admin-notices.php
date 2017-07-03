<?php
/**
 * WordPress plugin admin notice class.
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @since      1.1.8
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
	 * Class Admin notices
	 *
	 * @package VASOCIALBUZZ\Modules
	 */
	class AdminNotices {
		use Instance, Options;

		/**
		 * Setting items.
		 *
		 * @var array
		 */
		private $settings = [];

		/**
		 * Ajax action.
		 *
		 * @var string
		 */
		private $ajax_action = 'vasb_dismiss_admin_notice';

		/**
		 * This hook is called once any activated plugins have been loaded.
		 */
		protected function __construct() {
			$this->settings = Variable::settings();

			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
			add_action( "wp_ajax_{$this->ajax_action}", array( &$this, 'dismiss_admin_notice' ) );
		}

		/**
		 * Admin enqueue scripts.
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_script( 'va-social-buzz-admin-notice', VA_SOCIALBUZZ_URL . 'assets/js/admin-notice.js', array( 'jquery' ), false, true );
			wp_localize_script( 'va-social-buzz-admin-notice', 'vaSocialBuzzSettings', array(
				'action' => $this->ajax_action,
				'nonce' => wp_create_nonce( 'va-social-buzz-admin-notice' ),
			) );
		}

		/**
		 * Admin notices.
		 *
		 * @since  1.1.8
		 */
		public function admin_notices() {
			$notices = Options::get( 'notices' );
			$key     = 'vasb-2017-06-07';

			if ( ! current_user_can( 'manage_options' ) || defined( 'VA_SOCIALBUZZ_PLUS_SW_VERSION' ) || ! self::_is_admin_notice_active( $key ) || in_array( $key, $notices ) ) {
				return;
			}

			$message = __( 'Premium add-on that makes VA Social Buzz more flexible, more convenient!', 'va-social-buzz' );
			$href    = __( 'https://gumroad.com/l/va-social-buzz-plus-sw/', 'va-social-buzz' );
			$anchor  = sprintf( '<a href="%s">%s</a>', esc_url( $href ), __( 'Click here to details.', 'va-social-buzz' ) );
			$dismiss = __( 'Dismiss this notice.', 'va-social-buzz' );
			$notices = <<<EOM
<div id="vasb-notices" class="notice notice-info is-dismissible" data-dismissible="{$key}">
	<p><strong>{$message}</strong></p>
	<p><strong>{$anchor}</strong></p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">{$dismiss}</span>
	</button>
</div>
EOM;

			echo $notices;
		}

		/**
		 * Ajax.
		 */
		public function dismiss_admin_notice() {
			check_ajax_referer( 'va-social-buzz-admin-notice', 'nonce' );
			$dismissible = filter_input( INPUT_POST, 'dismissible', FILTER_SANITIZE_STRING );
			$dismissible = sanitize_key( $dismissible );

			if ( empty( $dismissible ) ) {
				wp_send_json_error( __( 'Dismissible has not been set.', 'va-social-buzz' ) );
			} else {
				$options = Options::get( 'all' );

				if ( empty( $options['notices'] ) || ! in_array( $dismissible, $options['notices'], true ) ) {
					$options['notices'][] = $dismissible;
					$message              = ( true === Options::update( $options ) ) ? 'Settings saved!' : 'Save errored!';
					$result               = [ 'message' => $message ];

					wp_send_json_success( wp_json_encode( $result ) );
				}
			}

			exit;
		}

		/**
		 * Is admin notice active?
		 *
		 * @param string $name data-dismissible content of notice.
		 *
		 * @return bool
		 */
		protected function _is_admin_notice_active( $name ) {
			$result  = true;
			$name    = sanitize_key( $name );
			$notices = Options::get( 'notices' );

			if ( is_array( $notices ) && ! empty( $notices ) && in_array( $name, $notices ) ) {
				$result = false;
			}

			return $result;
		}
	}
}
