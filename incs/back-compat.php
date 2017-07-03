<?php
/**
 * WordPress plugin back compat functionality.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin only works in WordPress 4.3 or later and PHP 5.4 or later.
 */
if ( false === va_socialbuzz_check_version() ) {
	add_action( 'admin_notices', function () {
		$message = sprintf( __( '%1$s requires at least WordPress version %2$s and PHP version %3$s. You are running WordPress version %4$s and PHP version %5$s. Please upgrade and try again.', 'va-social-buzz' ), VA_SOCIALBUZZ_NAME, VA_SOCIALBUZZ_VERSION_WP, VA_SOCIALBUZZ_VERSION_PHP, $GLOBALS['wp_version'], PHP_VERSION );
		printf( '<div class="error"><p>%s</p></div>', esc_html( $message ) );
	} );
}
