<?php
/**
 * Plugin Name: KR Toolkit
 * Plugin URI: https://krtheme.com
 * Description: Essential companion plugin for KR Theme. Features one-click demo import, child theme manager, license management, and system requirements checker. Unlock the full potential of KR Theme with this powerful toolkit.
 * Version: 4.2.1
 * Author: Aziz Kerkeni
 * Author URI: https://www.kerkeniaziz.ovh/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kr-toolkit
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * 
 * Copyright: 2015-2025 Aziz Kerkeni
 *
 * @package KR_Toolkit
 * @author Aziz Kerkeni
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin constants
define( 'KR_TOOLKIT_VERSION', '4.2.1' );
define( 'KR_TOOLKIT_FILE', __FILE__ );
define( 'KR_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );
define( 'KR_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );
define( 'KR_TOOLKIT_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main KR Toolkit Class
 */
final class KR_Toolkit {

	/**
	 * Plugin instance
	 *
	 * @var KR_Toolkit
	 */
	private static $instance = null;

	/**
	 * Get plugin instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required dependencies
	 */
	private function load_dependencies() {
		// Core classes
		require_once KR_TOOLKIT_PATH . 'includes/class-kr-toolkit.php';
		require_once KR_TOOLKIT_PATH . 'includes/class-demo-importer.php';
		require_once KR_TOOLKIT_PATH . 'includes/class-child-theme-manager.php';
		require_once KR_TOOLKIT_PATH . 'includes/class-license-manager.php';
		require_once KR_TOOLKIT_PATH . 'includes/class-system-requirements.php';
		require_once KR_TOOLKIT_PATH . 'includes/class-customizer-extensions.php';
		require_once KR_TOOLKIT_PATH . 'includes/helpers.php';

		// Admin classes
		if ( is_admin() ) {
			require_once KR_TOOLKIT_PATH . 'admin/class-admin.php';
		}
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize plugin
	 */
	public function init() {
		// Check if KR Theme is active
		if ( ! $this->is_kr_theme_active() ) {
			add_action( 'admin_notices', array( $this, 'theme_not_active_notice' ) );
			return;
		}

		// Initialize main plugin class
		KR_Toolkit_Main::instance();

		// Initialize admin
		if ( is_admin() ) {
			KR_Toolkit_Admin::instance();
		}

		do_action( 'kr_toolkit_loaded' );
	}

	/**
	 * Load plugin textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'kr-toolkit', false, dirname( KR_TOOLKIT_BASENAME ) . '/languages' );
	}

	/**
	 * Check if KR Theme is active
	 */
	private function is_kr_theme_active() {
		$theme = wp_get_theme();
		return ( 'KR Theme' === $theme->get( 'Name' ) || 'KR Theme' === $theme->get_template() );
	}

	/**
	 * Theme not active notice
	 */
	public function theme_not_active_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: %s: theme name */
					esc_html__( 'KR Toolkit requires %s to be installed and activated.', 'kr-toolkit' ),
					'<strong>KR Theme</strong>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Plugin activation
	 */
	public function activate() {
		// Set transient for welcome redirect
		set_transient( 'kr_toolkit_activation_redirect', true, 30 );

		// Create default options
		$defaults = array(
			'version'            => KR_TOOLKIT_VERSION,
			'activation_date'    => current_time( 'timestamp' ),
			'imported_demos'     => array(),
			'license_key'        => '',
			'license_status'     => 'inactive',
		);

		add_option( 'kr_toolkit_settings', $defaults );

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		// Clear scheduled hooks if any
		wp_clear_scheduled_hook( 'kr_toolkit_daily_tasks' );

		// Flush rewrite rules
		flush_rewrite_rules();
	}
}

/**
 * Initialize the plugin
 */
function kr_toolkit() {
	return KR_Toolkit::instance();
}

// Start the plugin
kr_toolkit();
