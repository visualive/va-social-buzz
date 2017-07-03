<?php
/**
 * WordPress plugin functions.
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

add_filter( 'load_textdomain_mofile', function ( $mofile, $domain ) {
	if ( 'en' !== get_locale() && VA_SOCIALBUZZ_BASENAME === $domain ) {
		$mofile = sprintf( '%s/langs/%s-%s.mo', VA_SOCIALBUZZ_PATH, VA_SOCIALBUZZ_BASENAME, get_locale() );
	}

	return $mofile;
}, 10, 2 );
load_plugin_textdomain( 'va-social-buzz', false, VA_SOCIALBUZZ_BASENAME . '/langs' );

/**
 * Version check.
 *
 * @return bool
 */
function va_socialbuzz_check_version() {
	$result = false;

	if ( version_compare( $GLOBALS['wp_version'], VA_SOCIALBUZZ_VERSION_WP, '>=' ) && version_compare( PHP_VERSION, VA_SOCIALBUZZ_VERSION_PHP, '>=' ) ) {
		$result = true;
	}

	return $result;
}
