<?php
/**
 * WordPress Sentry Plugin
 *
 * Plugin Name: WordPress Sentry Plugin
 * Plugin URI:  https://github.com/asumranphan/wordpress-sentry-plugin
 * Description: A Wordpress Plugin send php errors to the Sentry errors reporting system.
 * Version:     0.1.0
 * Author:      Anurak Sumranphan <a.sumranphan@gmail.com>
 * Author URI:  https://www.atomfolio.me
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wordpress-sentry-plugin
 * Domain Path: /languages
 *
 * @category WordPress_Plugin
 * @package WordPress_Sentry_Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WordPress_Sentry_Plugin' ) ) {
	/**
	 * WordPress Sentry Plugin
	 */
	class WordPress_Sentry_Plugin {

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
			define( 'WSP_BASENAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'WSP_VERSION', $this->version );
		}

		/**
		 * Setup
		 */
		public function setup() {
			if ( get_option( WSP_BASENAME . '-version' ) === WSP_VERSION ) {
				return;
			}

			update_option( WSP_BASENAME . '-version', WSP_VERSION );
		}
	}

	$GLOBALS['wsp'] = new WordPress_Sentry_Plugin();
} // End if().
