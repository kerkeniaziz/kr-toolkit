<?php
/**
 * Main KR Toolkit Class
 * 
 * Handles plugin initialization and loading following Astra Sites' proven patterns
 * 
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Toolkit' ) ) {

	/**
	 * Main KR_Toolkit Class - Singleton Pattern
	 */
	final class KR_Toolkit {

		/**
		 * Instance
		 *
		 * @var KR_Toolkit
		 */
		private static $instance = null;

		/**
		 * Get Instance - Singleton Pattern
		 *
		 * @return KR_Toolkit
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
			$this->init_hooks();
			$this->includes();
			$this->system_requirements_check();
		}

		/**
		 * Initialize Hooks
		 */
		private function init_hooks() {
			// Plugin initialization
			add_action( 'init', array( $this, 'init_plugin' ) );
			
			// Load textdomain
			add_action( 'init', array( $this, 'load_textdomain' ) );
			
			// Admin initialization
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			}
			
			// AJAX handlers
			add_action( 'wp_ajax_kr_import_demo', array( $this, 'ajax_import_demo' ) );
			add_action( 'wp_ajax_kr_get_system_info', array( $this, 'ajax_get_system_info' ) );
			add_action( 'wp_ajax_kr_create_child_theme', array( $this, 'ajax_create_child_theme' ) );
			
			// Welcome redirect
			add_action( 'admin_init', array( $this, 'welcome_redirect' ) );
		}

		/**
		 * Include Files - Load in correct order with error handling
		 */
		private function includes() {
			// Core classes
			$this->include_file( 'classes/class-kr-system-check.php' );
			$this->include_file( 'classes/class-kr-importer.php' );
			$this->include_file( 'classes/class-kr-batch-processor.php' );
			$this->include_file( 'classes/class-kr-error-handler.php' );
			$this->include_file( 'classes/class-kr-child-theme.php' );
			
			// Library classes
			$this->include_file( 'lib/class-kr-helper.php' );
			$this->include_file( 'lib/class-kr-api.php' );
			
			// Importers
			$this->include_file( 'importers/class-kr-wp-importer.php' );
			$this->include_file( 'importers/class-kr-widget-importer.php' );
			$this->include_file( 'importers/class-kr-customizer-importer.php' );
			$this->include_file( 'importers/class-kr-elementor-importer.php' );
			
			// Admin classes (only in admin)
			if ( is_admin() ) {
				$this->include_file( 'admin/class-kr-admin.php' );
				$this->include_file( 'admin/class-kr-ajax.php' );
			}
		}

		/**
		 * Safe file include with error handling
		 *
		 * @param string $file_path Relative path from inc/ directory
		 * @return bool
		 */
		private function include_file( $file_path ) {
			$full_path = KR_TOOLKIT_DIR . 'inc/' . $file_path;
			
			if ( ! file_exists( $full_path ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( "KR Toolkit: Missing file - {$file_path}" );
				}
				return false;
			}

			try {
				require_once $full_path;
				return true;
			} catch ( Exception $e ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( "KR Toolkit: Error loading {$file_path}: " . $e->getMessage() );
				}
				return false;
			}
		}

		/**
		 * System Requirements Check
		 */
		private function system_requirements_check() {
			if ( class_exists( 'KR_System_Check' ) ) {
				$system_check = KR_System_Check::instance();
				$system_check->run_checks();
			}
		}

		/**
		 * Initialize Plugin
		 */
		public function init_plugin() {
			// Check if KR Theme is active
			if ( ! $this->is_kr_theme_active() ) {
				add_action( 'admin_notices', array( $this, 'theme_not_active_notice' ) );
				return;
			}

			// Initialize components
			$this->init_components();

			// Hook for other plugins/themes
			do_action( 'kr_toolkit_loaded' );
		}

		/**
		 * Initialize Components
		 */
		private function init_components() {
			// Initialize importer
			if ( class_exists( 'KR_Importer' ) ) {
				KR_Importer::instance();
			}

			// Initialize batch processor
			if ( class_exists( 'KR_Batch_Processor' ) ) {
				KR_Batch_Processor::instance();
			}

			// Initialize child theme manager
			if ( class_exists( 'KR_Child_Theme' ) ) {
				KR_Child_Theme::instance();
			}

			// Initialize admin interface
			if ( is_admin() && class_exists( 'KR_Admin' ) ) {
				KR_Admin::instance();
			}
		}

		/**
		 * Load Plugin Text Domain
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 
				'kr-toolkit', 
				false, 
				dirname( KR_TOOLKIT_BASENAME ) . '/languages' 
			);
		}

		/**
		 * Admin Initialization
		 */
		public function admin_init() {
			// Check for theme compatibility
			$this->theme_compatibility_check();
		}

		/**
		 * Add Admin Menu
		 */
		public function admin_menu() {
			if ( ! class_exists( 'KR_Admin' ) ) {
				return;
			}

			KR_Admin::instance()->register_menu();
		}

		/**
		 * Enqueue Admin Scripts
		 */
		public function admin_scripts( $hook_suffix ) {
			// Only load on KR Toolkit pages
			if ( strpos( $hook_suffix, 'kr-toolkit' ) === false ) {
				return;
			}

			// Main admin styles
			wp_enqueue_style(
				'kr-toolkit-admin',
				KR_TOOLKIT_URL . 'admin/css/admin.css',
				array(),
				KR_TOOLKIT_VERSION
			);

			// Main admin script
			wp_enqueue_script(
				'kr-toolkit-admin',
				KR_TOOLKIT_URL . 'admin/js/admin.js',
				array( 'jquery' ),
				KR_TOOLKIT_VERSION,
				true
			);

			// Import script for demo import pages
			if ( strpos( $hook_suffix, 'demo' ) !== false ) {
				wp_enqueue_script(
					'kr-toolkit-import',
					KR_TOOLKIT_URL . 'assets/js/import.js',
					array( 'jquery', 'kr-toolkit-admin' ),
					KR_TOOLKIT_VERSION,
					true
				);
			}

			// Localize scripts
			wp_localize_script( 'kr-toolkit-admin', 'krToolkit', array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'kr_toolkit_nonce' ),
				'pluginUrl'  => KR_TOOLKIT_URL,
				'strings'    => array(
					'importing'       => __( 'Importing demo...', 'kr-toolkit' ),
					'success'         => __( 'Demo imported successfully!', 'kr-toolkit' ),
					'error'           => __( 'Import failed. Please try again.', 'kr-toolkit' ),
					'confirm_import'  => __( 'Are you sure you want to import this demo? This will replace your current content.', 'kr-toolkit' ),
					'confirm_reset'   => __( 'Are you sure you want to reset your site? This action cannot be undone.', 'kr-toolkit' ),
				),
			) );
		}

		/**
		 * AJAX: Import Demo
		 */
		public function ajax_import_demo() {
			// Verify nonce and permissions
			if ( ! wp_verify_nonce( $_POST['nonce'], 'kr_toolkit_nonce' ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Invalid request' );
			}

			$demo_slug = sanitize_text_field( $_POST['demo_slug'] );
			
			if ( ! $demo_slug ) {
				wp_send_json_error( 'Demo slug is required' );
			}

			try {
				if ( class_exists( 'KR_Importer' ) ) {
					$importer = KR_Importer::instance();
					$result = $importer->import_demo( $demo_slug );
					
					if ( is_wp_error( $result ) ) {
						wp_send_json_error( $result->get_error_message() );
					}
					
					wp_send_json_success( $result );
				} else {
					wp_send_json_error( 'Importer class not found' );
				}
			} catch ( Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}
		}

		/**
		 * AJAX: Get System Info
		 */
		public function ajax_get_system_info() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'kr_toolkit_nonce' ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Invalid request' );
			}

			if ( class_exists( 'KR_System_Check' ) ) {
				$system_info = KR_System_Check::instance()->get_system_report();
				wp_send_json_success( $system_info );
			} else {
				wp_send_json_error( 'System check class not found' );
			}
		}

		/**
		 * AJAX: Create Child Theme
		 */
		public function ajax_create_child_theme() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'kr_toolkit_nonce' ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Invalid request' );
			}

			if ( class_exists( 'KR_Child_Theme' ) ) {
				$child_theme = KR_Child_Theme::instance();
				$result = $child_theme->create_child_theme();
				
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( $result->get_error_message() );
				}
				
				wp_send_json_success( $result );
			} else {
				wp_send_json_error( 'Child theme class not found' );
			}
		}

		/**
		 * Welcome Redirect
		 */
		public function welcome_redirect() {
			if ( get_transient( 'kr_toolkit_activation_redirect' ) ) {
				delete_transient( 'kr_toolkit_activation_redirect' );
				
				if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
					wp_safe_redirect( admin_url( 'admin.php?page=kr-toolkit' ) );
					exit;
				}
			}
		}

		/**
		 * Check if KR Theme is active
		 */
		private function is_kr_theme_active() {
			$theme = wp_get_theme();
			return ( 'KR Theme' === $theme->get( 'Name' ) || 'kr-theme' === $theme->get_template() );
		}

		/**
		 * Theme compatibility check
		 */
		private function theme_compatibility_check() {
			if ( ! $this->is_kr_theme_active() ) {
				return;
			}

			// Check theme version compatibility
			$theme = wp_get_theme();
			$theme_version = $theme->get( 'Version' );
			$required_theme_version = '1.3.0';

			if ( version_compare( $theme_version, $required_theme_version, '<' ) ) {
				add_action( 'admin_notices', array( $this, 'theme_version_notice' ) );
			}
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
		 * Theme version notice
		 */
		public function theme_version_notice() {
			?>
			<div class="notice notice-warning">
				<p>
					<?php
					esc_html_e( 'KR Toolkit works best with the latest version of KR Theme. Please update your theme for optimal compatibility.', 'kr-toolkit' );
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Get plugin version
		 */
		public function get_version() {
			return KR_TOOLKIT_VERSION;
		}

		/**
		 * Check if component is active
		 */
		public function is_component_active( $component ) {
			$active_components = array(
				'elementor'     => class_exists( 'Elementor\Plugin' ),
				'woocommerce'   => class_exists( 'WooCommerce' ),
				'kr_theme'      => $this->is_kr_theme_active(),
			);

			return isset( $active_components[ $component ] ) ? $active_components[ $component ] : false;
		}

		/**
		 * Prevent cloning
		 */
		private function __clone() {}

		/**
		 * Prevent unserialization
		 */
		private function __wakeup() {}
	}
}