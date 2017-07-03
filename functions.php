<?php
/**
 * Plugin Name: VA Social Buzz
 * Plugin URI: https://github.com/visualive/va-social-buzz
 * Description: It displays buttons at the end of every article for readers to "Like!" your recommended Facebook page, to share the article on SNS.
 * Author: KUCKLU
 * Version: 1.1.14
 * WordPress Version: 4.4
 * PHP Version: 5.4
 * DB Version: 1.0.0
 * Author URI: https://www.visualive.jp
 * Domain Path: /langs
 * Text Domain: va-social-buzz
 * Prefix: va_social_buzz_
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package    WordPress
 * @subpackage VA Social Buzz
 * @since      0.0.1 (Alpha)
 * @author     KUCKLU <oss@visualive.jp>
 *             Copyright (C) 2016 KUCKLU & VisuAlive.
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

require_once dirname( __FILE__ ) . '/incs/defines.php';
require_once dirname( __FILE__ ) . '/incs/functions.php';
require_once dirname( __FILE__ ) . '/incs/back-compat.php';

if ( true === va_socialbuzz_check_version() ) :
	require_once dirname( __FILE__ ) . '/incs/class-module-core.php';

	/**
	 * Run plugin.
	 */
	add_action( 'plugins_loaded', array( '\VASOCIALBUZZ\Modules\Core', 'get_instance' ) );

	/**
	 * Install.
	 */
	register_activation_hook( __FILE__, array( \VASOCIALBUZZ\Modules\Installer::get_called_class(), 'install' ) );

	/**
	 * Uninstall.
	 */
	register_activation_hook( __FILE__, function () {
		register_uninstall_hook( __FILE__, array( \VASOCIALBUZZ\Modules\Installer::get_called_class(), 'uninstall' ) );
	} );

	/**
	 * Uninstall [Debug mode].
	 */
	if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
		register_deactivation_hook( __FILE__, array( \VASOCIALBUZZ\Modules\Installer::get_called_class(), 'uninstall' ) );
	}
endif;
