<?php
/**
 * WP Excel CMS
 *
 * Import von Excel-Tabellen
 *
 * @package   Excel_Import
 * @author    Vincent Schroeder <info@webteilchen.de>
 * @license   GPL-2.0+
 * @link      http://webteilchen.de
 * @copyright 2013 Webteilchen
 *
 * @wordpress-plugin
 * Plugin Name:       WP Excel CMS
 * Plugin URI:        http://webteilchen.de
 * Description:       Imports and managages content of Excel (*.xls *.xlsx) Files into Wordpress and make the data available in the theme or shortcodes.
 * Version:           1.0.2
 * Author:            Vincent Schroeder
 * Author URI:        http://webteilchen.de
 * Text Domain:       wp-excel-cms
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/wp-excel-cms.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
register_activation_hook( __FILE__, array( 'WP_Excel_Cms', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Excel_Cms', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'WP_Excel_Cms', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/wp-excel-cms-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Excel_Cms_Admin', 'get_instance' ) );

}
