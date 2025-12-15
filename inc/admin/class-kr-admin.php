<?php
/**
 * KR Admin
 * 
 * Handles admin interface and dashboard
 * Following Astra Sites' modern admin UI patterns
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Admin' ) ) {

	/**
	 * KR_Admin class
	 */
	final class KR_Admin {

		/**
		 * Instance
		 */
		private static $instance = null;

		/**
		 * Get Instance
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
		}

		/**
		 * Initialize hooks
		 */
		private function init_hooks() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
		}

		/**
		 * Admin initialization
		 */
		public function admin_init() {
			// Run system check on admin init
			$system_check = KR_System_Check::instance();
			$system_check->run_checks();
		}

		/**
		 * Register admin menu
		 */
		public function register_menu() {
			// Main menu page
			add_menu_page(
				__( 'KR Toolkit', 'kr-toolkit' ),
				__( 'KR Toolkit', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit',
				array( $this, 'dashboard_page' ),
				'dashicons-download',
				59
			);

			// Dashboard submenu
			add_submenu_page(
				'kr-toolkit',
				__( 'Dashboard', 'kr-toolkit' ),
				__( 'Dashboard', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-dashboard',
				array( $this, 'dashboard_page' )
			);

			// Theme Options
			add_submenu_page(
				'kr-toolkit',
				__( 'Theme Options', 'kr-toolkit' ),
				__( 'Theme Options', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-theme-options',
				array( $this, 'theme_options_page' )
			);

			// Header Builder
			add_submenu_page(
				'kr-toolkit',
				__( 'Header Builder', 'kr-toolkit' ),
				__( 'Header Builder', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-header-builder',
				array( $this, 'header_builder_page' )
			);

			// Footer Builder
			add_submenu_page(
				'kr-toolkit',
				__( 'Footer Builder', 'kr-toolkit' ),
				__( 'Footer Builder', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-footer-builder',
				array( $this, 'footer_builder_page' )
			);

			// Demo Library
			add_submenu_page(
				'kr-toolkit',
				__( 'Demo Library', 'kr-toolkit' ),
				__( 'Demo Library', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-demos',
				array( $this, 'demos_page' )
			);

			// Child Theme
			add_submenu_page(
				'kr-toolkit',
				__( 'Child Theme', 'kr-toolkit' ),
				__( 'Child Theme', 'kr-toolkit' ),
				'install_themes',
				'kr-toolkit-child-theme',
				array( $this, 'child_theme_page' )
			);

			// Test Updates
			add_submenu_page(
				'kr-toolkit',
				__( 'Test Updates', 'kr-toolkit' ),
				__( 'Test Updates', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-test-updates',
				array( $this, 'test_updates_page' )
			);

			// System Info
			add_submenu_page(
				'kr-toolkit',
				__( 'System Info', 'kr-toolkit' ),
				__( 'System Info', 'kr-toolkit' ),
				'manage_options',
				'kr-toolkit-system-info',
				array( $this, 'system_info_page' )
			);
		}

		/**
		 * Enqueue admin scripts and styles
		 */
		public function enqueue_scripts() {
			// Admin CSS
			wp_enqueue_style(
				'kr-toolkit-admin',
				KR_TOOLKIT_URL . 'assets/css/admin.css',
				array(),
				KR_TOOLKIT_VERSION
			);

			// Admin JS
			wp_enqueue_script(
				'kr-toolkit-admin',
				KR_TOOLKIT_URL . 'assets/js/admin.js',
				array( 'jquery', 'wp-util' ),
				KR_TOOLKIT_VERSION,
				true
			);

			// Localize script
			wp_localize_script(
				'kr-toolkit-admin',
				'krToolkitAdmin',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'kr_toolkit_nonce' ),
					'strings' => array(
						'importing' => __( 'Importing...', 'kr-toolkit' ),
						'success' => __( 'Success!', 'kr-toolkit' ),
						'error' => __( 'Error occurred', 'kr-toolkit' ),
						'confirmReset' => __( 'Are you sure you want to reset existing content? This cannot be undone.', 'kr-toolkit' ),
						'confirmImport' => __( 'Are you sure you want to import this demo?', 'kr-toolkit' ),
						'systemCheckFailed' => __( 'System requirements check failed. Please fix the issues before importing.', 'kr-toolkit' ),
					)
				)
			);
		}

		/**
		 * Dashboard page
		 */
		public function dashboard_page() {
			$system_check = KR_System_Check::instance();
			$system_summary = $system_check->get_status_summary();
			$child_theme_manager = KR_Child_Theme::instance();
			$child_theme_status = $child_theme_manager->get_child_theme_status();

			include KR_TOOLKIT_DIR . 'admin/views/dashboard.php';
		}

		/**
		 * Demo library page
		 */
		public function demos_page() {
			$demos = $this->get_available_demos();
			$system_check = KR_System_Check::instance();
			$system_summary = $system_check->get_status_summary();

			include KR_TOOLKIT_DIR . 'admin/views/demos.php';
		}

		/**
		 * Child theme page
		 */
		public function child_theme_page() {
			$child_theme_manager = KR_Child_Theme::instance();
			$child_theme_status = $child_theme_manager->get_child_theme_status();
			$available_backups = $child_theme_manager->get_available_backups();

			include KR_TOOLKIT_DIR . 'admin/views/child-theme.php';
		}

		/**
		 * System info page
		 */
		public function system_info_page() {
			$system_check = KR_System_Check::instance();
			$system_report = $system_check->get_system_report();

			include KR_TOOLKIT_DIR . 'admin/views/system-info.php';
		}

		/**
		 * Theme Options page
		 */
		public function theme_options_page() {
			include KR_TOOLKIT_DIR . 'admin/views/theme-options.php';
		}

		/**
		 * Header Builder page
		 */
		public function header_builder_page() {
			include KR_TOOLKIT_DIR . 'admin/views/header-builder.php';
		}

		/**
		 * Footer Builder page
		 */
		public function footer_builder_page() {
			include KR_TOOLKIT_DIR . 'admin/views/footer-builder.php';
		}

		/**
		 * Test Updates page
		 */
		public function test_updates_page() {
			include KR_TOOLKIT_DIR . 'admin/views/test-updates.php';
		}

		/**
		 * Get available demos
		 */
		private function get_available_demos() {
			$demos_dir = KR_TOOLKIT_DIR . 'demos/';
			$demos = array();

			if ( ! is_dir( $demos_dir ) ) {
				return $demos;
			}

			$demo_folders = scandir( $demos_dir );
			
			foreach ( $demo_folders as $folder ) {
				if ( $folder === '.' || $folder === '..' ) {
					continue;
				}

				$demo_path = $demos_dir . $folder;
				$config_file = $demo_path . '/config.json';

				if ( is_dir( $demo_path ) && file_exists( $config_file ) ) {
					$config = json_decode( file_get_contents( $config_file ), true );
					
					if ( $config ) {
						$config['slug'] = $folder;
						$config['preview_url'] = isset( $config['preview_url'] ) ? $config['preview_url'] : '';
						$config['screenshot'] = $this->get_demo_screenshot( $folder );
						$demos[] = $config;
					}
				}
			}

			return $demos;
		}

		/**
		 * Get demo screenshot
		 */
		private function get_demo_screenshot( $demo_slug ) {
			$screenshot_extensions = array( 'jpg', 'jpeg', 'png', 'gif' );
			
			foreach ( $screenshot_extensions as $ext ) {
				$screenshot_file = KR_TOOLKIT_DIR . "demos/{$demo_slug}/screenshot.{$ext}";
				
				if ( file_exists( $screenshot_file ) ) {
					return KR_TOOLKIT_URL . "demos/{$demo_slug}/screenshot.{$ext}";
				}
			}

			// Return placeholder if no screenshot found
			return KR_TOOLKIT_URL . 'assets/images/demo-placeholder.png';
		}

		/**
		 * Add admin head styles
		 */
		public function admin_head() {
			$current_screen = get_current_screen();
			
			if ( strpos( $current_screen->id, 'kr-toolkit' ) !== false ) {
				?>
				<style>
				.kr-toolkit-admin {
					background: #f1f1f1;
				}
				.kr-toolkit-header {
					background: #fff;
					border-bottom: 1px solid #ddd;
					padding: 20px;
					margin: 0 -20px 20px -20px;
				}
				.kr-toolkit-logo {
					font-size: 24px;
					font-weight: 600;
					color: #23282d;
				}
				</style>
				<?php
			}
		}

		/**
		 * Get admin notice HTML
		 */
		public function get_admin_notice( $message, $type = 'info', $dismissible = true ) {
			$classes = array( 'notice', 'notice-' . $type );
			
			if ( $dismissible ) {
				$classes[] = 'is-dismissible';
			}

			return sprintf(
				'<div class="%s"><p>%s</p></div>',
				esc_attr( implode( ' ', $classes ) ),
				wp_kses_post( $message )
			);
		}

		/**
		 * Check if current page is KR Toolkit admin page
		 */
		public function is_kr_toolkit_page() {
			$current_screen = get_current_screen();
			return strpos( $current_screen->id, 'kr-toolkit' ) !== false;
		}
	}
}