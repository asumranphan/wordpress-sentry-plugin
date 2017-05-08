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

require_once( 'libs/Raven/Autoloader.php' );
Raven_Autoloader::register();

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

			if ( is_admin() ) {
				add_action( 'admin_init', [ $this, 'setup' ] );
				add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
				add_action( 'admin_notices', [ $this, 'add_admin_notices' ] );
			}

			add_action( 'init', [ $this, 'run' ] );
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
			update_option( WSP_BASENAME . '-client-keys-dsn', null );
		}

		/**
		 * Add admin menu
		 */
		public function add_admin_menu() {
			add_submenu_page( 'options-general.php', 'Wordpress Sentry', 'Sentry' , 'manage_options', 'wordpress-sentry', [ $this, 'add_admin_display' ] );
		}

		/**
		 * Add admin display
		 */
		public function add_admin_display() {
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Sentry Settings', 'wordpresss-sentry-plugin' ) ?></h1>
				<form name="sentry-settings-form" method="post">
					<?php wp_nonce_field( 'client_keys_action', 'client_keys_nonce' ); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><label><?php esc_html_e( 'Client Keys (DSN)', 'wordpress-sentry-plugin' ); ?></label></th>
								<td>
									<input name="client_keys" class="regular-text" type="text" placeholder="<?php esc_html_e( 'Please enter your keys here!', 'wordpress-sentry-plugin' ); ?>" value="<?php echo esc_html( get_option( WSP_BASENAME . '-client-keys-dsn' ) ); ?>">
									<p class="description" id="client_keys_description"><?php esc_html_e( 'Example:  https://<key>:<secret>@sentry.io/<project>', 'wordpress-sentry-plugin' ); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<p class="submit"><button class="button button-primary" type="submit"><?php esc_html_e( 'Save Settings', 'wordpress-sentry-plugin' ) ?></button></p>
				</form>
			</div>
			<?php
		}

		/**
		 * Add admin notices
		 */
		public function add_admin_notices() {
			if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) { // Input var okay.
				if ( $this->save() ) {
					$this->add_admin_notices__success();
				} else {
					$this->add_admin_notices__error();
				}
			}
		}

		/**
		 * Admin notices success
		 */
		public function add_admin_notices__success() {
			$class = 'notice notice-success';
			$message = __( 'Settings saved.', 'wordpress-sentry-plugin' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

		/**
		 * Admin notices error
		 */
		public function add_admin_notices__error() {
			$class = 'notice notice-error';
			$message = __( 'The client keys (dsn) entered did not appear to be a valid keys. Please enter a valid client keys (dsn).', 'wordpress-sentry-plugin' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

		/**
		 * Save settings
		 */
		public function save() {
			if ( isset( $_POST['client_keys'], $_POST['client_keys_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['client_keys_nonce'] ), 'client_keys_action' ) ) { // Input var okay.
				$client_keys = sanitize_text_field( wp_unslash( $_POST['client_keys'] ) ); // Input var okay.

				if ( empty( $client_keys ) || preg_match( '/^https:\/\/\S{32}:\S{32}@sentry.io\/\d+$/', $client_keys ) ) {
					update_option( WSP_BASENAME . '-client-keys-dsn', $client_keys );
					return true;
				}
			}

			return false;
		}

		/**
		 * Run
		 */
		public function run() {
			$client_keys_dsn = get_option( WSP_BASENAME . '-client-keys-dsn' );

			if ( empty( $client_keys_dsn ) ) {
				return;
			}

			$client = new Raven_Client( $client_keys_dsn );

			// Settings.
			$environment = defined( 'WP_ENV' ) ? WP_ENV : null;
			$client->setEnvironment( $environment );

			$app_path = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : ABSPATH;
			$client->setAppPath( $app_path );

			// Automatic errors reporting.
			$error_handler = new Raven_ErrorHandler( $client );
			$error_handler->registerExceptionHandler();
			$error_handler->registerErrorHandler();
			$error_handler->registerShutdownFunction();
		}
	}

	$GLOBALS['wsp'] = new WordPress_Sentry_Plugin();
} // End if().
