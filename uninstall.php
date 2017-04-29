<?php
/**
 * WordPress Plugin Starter Kit Uninstall
 *
 * Uninstalling delete user roles, pages, tables, and options.
 *
 * @category WordPress_Plugin
 * @package WordPress_Plugin_Starter_Kit/Uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define( 'WPSK_BASENAME', plugin_basename( dirname( __FILE__ ) ) );

// Put your code here.
delete_option( WPSK_BASENAME . '-version' );

// Clear any cached data.
wp_cache_flush();
