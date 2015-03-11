<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Plugin_Name
 * @author    Vincent Schroeder
 * @license   GPL-2.0+
 * @link      http://webteilchen.de
 * @copyright 2013 Webteilchen
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @TODO: Define uninstall functionality here