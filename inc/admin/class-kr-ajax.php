<?php
/**
 * KR AJAX Handlers
 * 
 * Handles all AJAX requests for demo import and system operations
 * Following Astra Sites' secure AJAX patterns
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Ajax' ) ) {

	/**
	 * KR_Ajax class
	 */
	final class KR_Ajax {

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
			// System check
			add_action( 'wp_ajax_kr_system_check', array( $this, 'system_check' ) );
			
			// Demo import
			add_action( 'wp_ajax_kr_import_demo', array( $this, 'import_demo' ) );
			add_action( 'wp_ajax_kr_import_step', array( $this, 'import_step' ) );
			add_action( 'wp_ajax_kr_reset_content', array( $this, 'reset_content' ) );
			
			// Child theme
			add_action( 'wp_ajax_kr_create_child_theme', array( $this, 'create_child_theme' ) );
			add_action( 'wp_ajax_kr_backup_customizations', array( $this, 'backup_customizations' ) );
			add_action( 'wp_ajax_kr_restore_backup', array( $this, 'restore_backup' ) );
			
			// Plugin management
			add_action( 'wp_ajax_kr_install_plugin', array( $this, 'install_plugin' ) );
			add_action( 'wp_ajax_kr_activate_plugin', array( $this, 'activate_plugin' ) );
		}

		/**
		 * Verify nonce and permissions
		 */
		private function verify_request() {
			if ( ! check_ajax_referer( 'kr_toolkit_nonce', 'nonce', false ) ) {
				wp_send_json_error( array( 'message' => __( 'Security verification failed.', 'kr-toolkit' ) ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'kr-toolkit' ) ) );
			}
		}

		/**
		 * System check AJAX handler
		 */
		public function system_check() {
			$this->verify_request();

			try {
				$system_check = KR_System_Check::instance();
				$results = $system_check->run_checks();

				wp_send_json_success( array(
					'results' => $results,
					'summary' => $system_check->get_status_summary(),
					'can_import' => $system_check->can_import(),
				) );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Import demo AJAX handler
		 */
		public function import_demo() {
			$this->verify_request();

			try {
				$demo_slug = sanitize_text_field( $_POST['demo_slug'] ?? '' );
				$reset_content = (bool) ( $_POST['reset_content'] ?? false );

				if ( empty( $demo_slug ) ) {
					throw new Exception( __( 'Demo slug is required.', 'kr-toolkit' ) );
				}

				// System requirements check
				$system_check = KR_System_Check::instance();
				if ( ! $system_check->can_import() ) {
					throw new Exception( __( 'System requirements not met.', 'kr-toolkit' ) );
				}

				// Initialize importer
				$importer = KR_Importer::instance();
				
				// Reset content if requested
				if ( $reset_content ) {
					$reset_result = $importer->reset_content();
					if ( is_wp_error( $reset_result ) ) {
						throw new Exception( $reset_result->get_error_message() );
					}
				}

				// Start import process
				$result = $importer->start_import( $demo_slug );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( array(
					'message' => __( 'Import started successfully.', 'kr-toolkit' ),
					'import_id' => $result['import_id'],
					'steps' => $result['steps'],
					'current_step' => 0,
				) );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Import step AJAX handler (for batch processing)
		 */
		public function import_step() {
			$this->verify_request();

			try {
				$import_id = sanitize_text_field( $_POST['import_id'] ?? '' );
				$step = (int) ( $_POST['step'] ?? 0 );

				if ( empty( $import_id ) ) {
					throw new Exception( __( 'Import ID is required.', 'kr-toolkit' ) );
				}

				$importer = KR_Importer::instance();
				$result = $importer->process_step( $import_id, $step );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( $result );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Reset content AJAX handler
		 */
		public function reset_content() {
			$this->verify_request();

			try {
				$importer = KR_Importer::instance();
				$result = $importer->reset_content();

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( array(
					'message' => __( 'Content reset successfully.', 'kr-toolkit' )
				) );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Create child theme AJAX handler
		 */
		public function create_child_theme() {
			// Check for theme installation permission
			if ( ! current_user_can( 'install_themes' ) ) {
				wp_send_json_error( array( 'message' => __( 'Insufficient permissions to create child themes.', 'kr-toolkit' ) ) );
			}

			$this->verify_request();

			try {
				$child_name = sanitize_text_field( $_POST['child_name'] ?? '' );
				$child_description = sanitize_textarea_field( $_POST['child_description'] ?? '' );

				$child_theme_manager = KR_Child_Theme::instance();
				$result = $child_theme_manager->create_child_theme( $child_name, $child_description );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( $result );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Backup customizations AJAX handler
		 */
		public function backup_customizations() {
			$this->verify_request();

			try {
				$child_theme_manager = KR_Child_Theme::instance();
				$result = $child_theme_manager->backup_customizations();

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( $result );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Restore backup AJAX handler
		 */
		public function restore_backup() {
			$this->verify_request();

			try {
				$backup_key = sanitize_text_field( $_POST['backup_key'] ?? '' );

				if ( empty( $backup_key ) ) {
					throw new Exception( __( 'Backup key is required.', 'kr-toolkit' ) );
				}

				$child_theme_manager = KR_Child_Theme::instance();
				$result = $child_theme_manager->restore_from_backup( $backup_key );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( $result );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Install plugin AJAX handler
		 */
		public function install_plugin() {
			$this->verify_request();

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( 'Insufficient permissions to install plugins.', 'kr-toolkit' ) ) );
			}

			try {
				$plugin_slug = sanitize_text_field( $_POST['plugin_slug'] ?? '' );

				if ( empty( $plugin_slug ) ) {
					throw new Exception( __( 'Plugin slug is required.', 'kr-toolkit' ) );
				}

				// Include WordPress plugin installation functions
				if ( ! function_exists( 'plugins_api' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				}
				if ( ! class_exists( 'WP_Upgrader' ) ) {
					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				}

				// Get plugin info
				$api = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug ) );
				
				if ( is_wp_error( $api ) ) {
					throw new Exception( sprintf( __( 'Plugin information not found: %s', 'kr-toolkit' ), $api->get_error_message() ) );
				}

				// Install plugin
				$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
				$result = $upgrader->install( $api->download_link );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				if ( $result === false ) {
					throw new Exception( __( 'Plugin installation failed.', 'kr-toolkit' ) );
				}

				wp_send_json_success( array(
					'message' => sprintf( __( 'Plugin "%s" installed successfully.', 'kr-toolkit' ), $api->name ),
					'plugin_file' => $upgrader->plugin_info()
				) );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Activate plugin AJAX handler
		 */
		public function activate_plugin() {
			$this->verify_request();

			if ( ! current_user_can( 'activate_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( 'Insufficient permissions to activate plugins.', 'kr-toolkit' ) ) );
			}

			try {
				$plugin_file = sanitize_text_field( $_POST['plugin_file'] ?? '' );

				if ( empty( $plugin_file ) ) {
					throw new Exception( __( 'Plugin file is required.', 'kr-toolkit' ) );
				}

				// Activate plugin
				$result = activate_plugin( $plugin_file );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( array(
					'message' => __( 'Plugin activated successfully.', 'kr-toolkit' )
				) );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Get import progress
		 */
		public function get_import_progress() {
			$this->verify_request();

			try {
				$import_id = sanitize_text_field( $_POST['import_id'] ?? '' );

				if ( empty( $import_id ) ) {
					throw new Exception( __( 'Import ID is required.', 'kr-toolkit' ) );
				}

				$importer = KR_Importer::instance();
				$progress = $importer->get_import_progress( $import_id );

				wp_send_json_success( $progress );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}

		/**
		 * Cancel import
		 */
		public function cancel_import() {
			$this->verify_request();

			try {
				$import_id = sanitize_text_field( $_POST['import_id'] ?? '' );

				if ( empty( $import_id ) ) {
					throw new Exception( __( 'Import ID is required.', 'kr-toolkit' ) );
				}

				$importer = KR_Importer::instance();
				$result = $importer->cancel_import( $import_id );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				wp_send_json_success( array(
					'message' => __( 'Import cancelled successfully.', 'kr-toolkit' )
				) );

			} catch ( Exception $e ) {
				wp_send_json_error( array( 'message' => $e->getMessage() ) );
			}
		}
	}
}