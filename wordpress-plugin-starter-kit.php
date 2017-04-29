<?php
/**
 * WordPress Plugin Starter Kit
 *
 * Plugin Name: WordPress Plugin Starter Kit
 * Plugin URI:  https://github.com/asumranphan/wordpress-plugin-starter-kit
 * Description: A WordPress Plugin Starter Kit for creating the WordPress Plugin.
 * Version:     0.1.0
 * Author:      Anurak Sumranphan <a.sumranphan@gmail.com>
 * Author URI:  https://www.atomfolio.me
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wordpress-plugin-starter-kit
 * Domain Path: /languages
 *
 * @category WordPress_Plugin
 * @package WordPress_Plugin_Starter_Kit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WordPress_Plugin_Starter_Kit' ) ) {
	/**
	 * WordPress Plugin Starter Kit
	 */
	class WordPress_Plugin_Starter_Kit {

		/**
		 * Version
		 *
		 * @var string
		 */
		protected $version = '0.1.0';

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->define_constants();

			add_action( 'init', [ $this, 'setup' ] );
		}

		/**
		 * Define Plugin Constants
		 */
		public function define_constants() {
			define( 'WPSK_BASENAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'WPSK_VERSION', $this->version );
		}

		/**
		 * Setup
		 */
		public function setup() {
			if ( get_option( WPSK_BASENAME . '-version' ) === WPSK_VERSION ) {
				return;
			}

			update_option( WPSK_BASENAME . '-version', WPSK_VERSION );
		}
	}

	$GLOBALS['wpsk'] = new WordPress_Plugin_Starter_Kit();
} // End if().
