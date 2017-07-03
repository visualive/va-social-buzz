<?php
/**
 * WordPress plugin defines.
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

$va_socialbuzz_path = str_replace( 'incs/defines.php', 'functions.php', __FILE__ );
$va_socialbuzz_data = get_file_data( $va_socialbuzz_path, array(
	'Name'             => 'Plugin Name',
	'PluginURI'        => 'Plugin URI',
	'Version'          => 'Version',
	'WordPressVersion' => 'WordPress Version',
	'PHPVersion'       => 'PHP Version',
	'DBVersion'        => 'DB Version',
	'Description'      => 'Description',
	'Author'           => 'Author',
	'AuthorURI'        => 'Author URI',
	'TextDomain'       => 'Text Domain',
	'DomainPath'       => 'Domain Path',
	'Prefix'           => 'Prefix',
	'Network'          => 'Network',
) );

define( 'VA_SOCIALBUZZ_URL', plugin_dir_url( $va_socialbuzz_path ) );
define( 'VA_SOCIALBUZZ_PATH', untrailingslashit( plugin_dir_path( $va_socialbuzz_path ) ) );
define( 'VA_SOCIALBUZZ_BASENAME', dirname( plugin_basename( $va_socialbuzz_path ) ) );
define( 'VA_SOCIALBUZZ_NAME', $va_socialbuzz_data['Name'] );
define( 'VA_SOCIALBUZZ_PREFIX', $va_socialbuzz_data['Prefix'] );
define( 'VA_SOCIALBUZZ_VERSION', $va_socialbuzz_data['Version'] );
define( 'VA_SOCIALBUZZ_VERSION_WP', $va_socialbuzz_data['WordPressVersion'] );
define( 'VA_SOCIALBUZZ_VERSION_PHP', $va_socialbuzz_data['PHPVersion'] );
define( 'VA_SOCIALBUZZ_VERSION_DB', $va_socialbuzz_data['DBVersion'] );

$va_socialbuzz_option_name = rtrim( VA_SOCIALBUZZ_PREFIX, '_' );

define( 'VA_SOCIALBUZZ_NAME_OPTION', $va_socialbuzz_option_name );

unset( $va_socialbuzz_path );
unset( $va_socialbuzz_data );
unset( $va_socialbuzz_option_name );
