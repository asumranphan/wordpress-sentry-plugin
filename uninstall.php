<?php
/**
 * WordPress Sentry Plugin
 *
 * Uninstalling delete user roles, pages, tables, and options.
 *
 * @category WordPress_Plugin
 * @package WordPress_Sentry_Plugin/Uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

define( 'WSP_BASENAME', plugin_basename( dirname( __FILE__ ) ) );

// Put your code here.
delete_option( WSP_BASENAME . '-version' );
delete_option( WSP_BASENAME . '-client-keys-dsn' );

// Clear any cached data.
wp_cache_flush();
