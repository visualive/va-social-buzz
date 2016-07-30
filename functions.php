<?php
/**
 * Plugin Name: VA Social Buzz
 * Plugin URI: https://github.com/visualive/va-social-buzz
 * Description: It displays buttons at the end of every article for readers to "Like" your recommended Facebook page, to share the article on Facebook, to tweet about it on Twitter, and to follow you on Twitter.
 * Author: KUCKLU
 * Version: 1.0.20
 * Author URI: https://www.visualive.jp/
 * Text Domain: va-social-buzz
 * Domain Path: /langs
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @author     KUCKLU <kuck1u@visualive.jp>
 *             Copyright (C) 2015 KUCKLU & VisuAlive.
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

$vasocialbuzz_data = get_file_data( __FILE__, array(
	'Name' => 'Plugin Name',
	'PluginURI' => 'Plugin URI',
	'Version' => 'Version',
	'Description' => 'Description',
	'Author' => 'Author',
	'AuthorURI' => 'Author URI',
	'TextDomain' => 'Text Domain',
	'DomainPath' => 'Domain Path',
	'Network' => 'Network',
) );;

define( 'VASOCIALBUZZ_URL', plugin_dir_url( __FILE__ ) );
define( 'VASOCIALBUZZ_PATH', plugin_dir_path( __FILE__ ) );
define( 'VASOCIALBUZZ_PREFIX', 'vasocialbuzz' );
define( 'VASOCIALBUZZ_VERSION', $vasocialbuzz_data['Version'] );

unset( $vasocialbuzz_data );

require_once dirname( __FILE__ ) . '/incs/singleton.php';
require_once dirname( __FILE__ ) . '/incs/admin.php';
require_once dirname( __FILE__ ) . '/incs/content.php';
require_once dirname( __FILE__ ) . '/incs/installer.php';

/**
 * Run plugin.
 *
 * @since 0.0.1 (Alpha)
 */
add_action( 'plugins_loaded', function () {
	$vasocialbuzz_admin_class   = apply_filters( 'vasocialbuzz_admin_class', \VASOCIALBUZZ\Modules\VASOCIALBUZZ_Admin::get_vasocialbuzz_admin() );
	$vasocialbuzz_content_class = apply_filters( 'vasocialbuzz_content_class', \VASOCIALBUZZ\Modules\VASOCIALBUZZ_Content::get_vasocialbuzz_content() );

	new $vasocialbuzz_admin_class;
	new $vasocialbuzz_content_class;

	load_plugin_textdomain( 'va-social-buzz', false, dirname( plugin_basename( __FILE__ ) ) . '/langs' );
} );

/**
 * Uninstall.
 *
 * @since 0.0.1 (Alpha)
 */
register_activation_hook( __FILE__, function() {
	register_uninstall_hook( __FILE__, array( \VASOCIALBUZZ\Modules\VASOCIALBUZZ_Installer::get_vasocialbuzz_installer(), 'uninstall' ) );
} );

if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
	register_deactivation_hook( __FILE__, array( \VASOCIALBUZZ\Modules\VASOCIALBUZZ_Installer::get_vasocialbuzz_installer(), 'uninstall' ) );
}
