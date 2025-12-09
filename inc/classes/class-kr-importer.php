<?php
/**
 * KR Importer
 * 
 * Handles demo import functionality with batch processing
 * Based on Astra Sites' proven import architecture
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Importer' ) ) {

	/**
	 * KR_Importer class
	 */
	final class KR_Importer {

		/**
		 * Instance
		 */
		private static $instance = null;

		/**
		 * Current import progress
		 */
		private $import_progress = array();

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
			// AJAX handlers for batch processing
			add_action( 'wp_ajax_kr_import_batch', array( $this, 'ajax_import_batch' ) );
			add_action( 'wp_ajax_kr_get_import_status', array( $this, 'ajax_get_import_status' ) );
			
			// Increase time limit for imports
			add_action( 'wp_ajax_kr_import_demo', array( $this, 'set_time_limit' ), 5 );
		}

		/**
		 * Set time limit for import
		 */
		public function set_time_limit() {
			@ini_set( 'max_execution_time', 300 ); // 5 minutes
			@ini_set( 'memory_limit', '256M' );
		}

		/**
		 * Import demo - Main entry point
		 */
		public function import_demo( $demo_slug ) {
			try {
				// System requirements check
				$system_check = $this->run_system_check();
				if ( is_wp_error( $system_check ) ) {
					throw new Exception( $system_check->get_error_message() );
				}

				// Get demo configuration
				$demo_config = $this->get_demo_config( $demo_slug );
				if ( ! $demo_config ) {
					throw new Exception( __( 'Demo configuration not found.', 'kr-toolkit' ) );
				}

				// Initialize import progress
				$this->init_import_progress( $demo_slug );

				// Start batch import process
				return $this->start_batch_import( $demo_slug, $demo_config );

			} catch ( Exception $e ) {
				$this->log_error( 'Import failed: ' . $e->getMessage() );
				return new WP_Error( 'import_failed', $e->getMessage() );
			}
		}

		/**
		 * Run system requirements check
		 */
		private function run_system_check() {
			if ( ! class_exists( 'KR_System_Check' ) ) {
				return new WP_Error( 'system_check_failed', 'System check class not available' );
			}

			$system_check = KR_System_Check::instance();
			$checks = $system_check->get_import_requirements_check();

			$errors = array();
			foreach ( $checks as $check ) {
				if ( ! $check['status'] && $check['required'] ) {
					$errors[] = $check['message'];
				}
			}

			if ( ! empty( $errors ) ) {
				return new WP_Error( 'system_requirements', implode( '<br>', $errors ) );
			}

			return true;
		}

		/**
		 * Get demo configuration
		 */
		private function get_demo_config( $demo_slug ) {
			$config_file = KR_TOOLKIT_DIR . "demos/{$demo_slug}/config.json";
			
			if ( ! file_exists( $config_file ) ) {
				return false;
			}

			$config = file_get_contents( $config_file );
			return json_decode( $config, true );
		}

		/**
		 * Initialize import progress tracking
		 */
		private function init_import_progress( $demo_slug ) {
			$this->import_progress = array(
				'demo_slug' => $demo_slug,
				'status' => 'started',
				'current_step' => 0,
				'total_steps' => 7,
				'steps' => array(
					'prepare' => array( 'status' => 'pending', 'message' => 'Preparing import...' ),
					'content' => array( 'status' => 'pending', 'message' => 'Importing content...' ),
					'widgets' => array( 'status' => 'pending', 'message' => 'Importing widgets...' ),
					'customizer' => array( 'status' => 'pending', 'message' => 'Importing customizer settings...' ),
					'elementor' => array( 'status' => 'pending', 'message' => 'Importing Elementor templates...' ),
					'menus' => array( 'status' => 'pending', 'message' => 'Setting up menus...' ),
					'finalize' => array( 'status' => 'pending', 'message' => 'Finalizing import...' ),
				),
				'started_at' => time(),
			);

			update_option( 'kr_import_progress', $this->import_progress );
		}

		/**
		 * Start batch import process
		 */
		private function start_batch_import( $demo_slug, $demo_config ) {
			// Update progress
			$this->update_import_step( 'prepare', 'processing' );

			// Create backup point (optional)
			$this->create_backup_point();

			// Reset content if requested
			if ( isset( $_POST['reset_content'] ) && $_POST['reset_content'] ) {
				$this->reset_content();
			}

			// Return batch processing data
			return array(
				'batch_processing' => true,
				'demo_slug' => $demo_slug,
				'total_steps' => $this->import_progress['total_steps'],
				'next_step' => 'content',
				'message' => __( 'Import started. Processing in batches...', 'kr-toolkit' ),
			);
		}

		/**
		 * AJAX: Import batch processing
		 */
		public function ajax_import_batch() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'kr_toolkit_nonce' ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Invalid request' );
			}

			$demo_slug = sanitize_text_field( $_POST['demo_slug'] );
			$step = sanitize_text_field( $_POST['step'] );

			try {
				$result = $this->process_import_step( $demo_slug, $step );
				wp_send_json_success( $result );
			} catch ( Exception $e ) {
				$this->log_error( "Batch import error ({$step}): " . $e->getMessage() );
				wp_send_json_error( $e->getMessage() );
			}
		}

		/**
		 * Process individual import step
		 */
		private function process_import_step( $demo_slug, $step ) {
			$this->update_import_step( $step, 'processing' );

			switch ( $step ) {
				case 'content':
					return $this->import_content( $demo_slug );
				
				case 'widgets':
					return $this->import_widgets( $demo_slug );
				
				case 'customizer':
					return $this->import_customizer( $demo_slug );
				
				case 'elementor':
					return $this->import_elementor( $demo_slug );
				
				case 'menus':
					return $this->setup_menus( $demo_slug );
				
				case 'finalize':
					return $this->finalize_import( $demo_slug );
				
				default:
					throw new Exception( "Unknown import step: {$step}" );
			}
		}

		/**
		 * Import WordPress content
		 */
		private function import_content( $demo_slug ) {
			$content_file = KR_TOOLKIT_DIR . "demos/{$demo_slug}/content.xml";
			
			if ( ! file_exists( $content_file ) ) {
				$this->update_import_step( 'content', 'skipped' );
				return array( 'next_step' => 'widgets', 'message' => 'Content file not found, skipping...' );
			}

			// Use WordPress Importer
			if ( ! class_exists( 'KR_WP_Importer' ) ) {
				throw new Exception( 'WordPress importer class not found' );
			}

			$importer = new KR_WP_Importer();
			$result = $importer->import( $content_file );

			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			$this->update_import_step( 'content', 'completed' );
			return array( 'next_step' => 'widgets', 'message' => 'Content imported successfully' );
		}

		/**
		 * Import widgets
		 */
		private function import_widgets( $demo_slug ) {
			$widgets_file = KR_TOOLKIT_DIR . "demos/{$demo_slug}/widgets.wie";
			
			if ( ! file_exists( $widgets_file ) ) {
				$this->update_import_step( 'widgets', 'skipped' );
				return array( 'next_step' => 'customizer', 'message' => 'Widgets file not found, skipping...' );
			}

			if ( ! class_exists( 'KR_Widget_Importer' ) ) {
				throw new Exception( 'Widget importer class not found' );
			}

			$importer = new KR_Widget_Importer();
			$result = $importer->import( $widgets_file );

			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			$this->update_import_step( 'widgets', 'completed' );
			return array( 'next_step' => 'customizer', 'message' => 'Widgets imported successfully' );
		}

		/**
		 * Import customizer settings
		 */
		private function import_customizer( $demo_slug ) {
			$customizer_file = KR_TOOLKIT_DIR . "demos/{$demo_slug}/customizer.dat";
			
			if ( ! file_exists( $customizer_file ) ) {
				$this->update_import_step( 'customizer', 'skipped' );
				return array( 'next_step' => 'elementor', 'message' => 'Customizer file not found, skipping...' );
			}

			if ( ! class_exists( 'KR_Customizer_Importer' ) ) {
				throw new Exception( 'Customizer importer class not found' );
			}

			$importer = new KR_Customizer_Importer();
			$result = $importer->import( $customizer_file );

			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			$this->update_import_step( 'customizer', 'completed' );
			return array( 'next_step' => 'elementor', 'message' => 'Customizer settings imported successfully' );
		}

		/**
		 * Import Elementor templates
		 */
		private function import_elementor( $demo_slug ) {
			if ( ! class_exists( 'Elementor\Plugin' ) ) {
				$this->update_import_step( 'elementor', 'skipped' );
				return array( 'next_step' => 'menus', 'message' => 'Elementor not active, skipping...' );
			}

			$elementor_file = KR_TOOLKIT_DIR . "demos/{$demo_slug}/elementor-kit.json";
			
			if ( ! file_exists( $elementor_file ) ) {
				$this->update_import_step( 'elementor', 'skipped' );
				return array( 'next_step' => 'menus', 'message' => 'Elementor file not found, skipping...' );
			}

			if ( ! class_exists( 'KR_Elementor_Importer' ) ) {
				throw new Exception( 'Elementor importer class not found' );
			}

			$importer = new KR_Elementor_Importer();
			$result = $importer->import( $elementor_file );

			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}

			$this->update_import_step( 'elementor', 'completed' );
			return array( 'next_step' => 'menus', 'message' => 'Elementor templates imported successfully' );
		}

		/**
		 * Setup navigation menus
		 */
		private function setup_menus( $demo_slug ) {
			$demo_config = $this->get_demo_config( $demo_slug );
			
			if ( ! isset( $demo_config['menus'] ) ) {
				$this->update_import_step( 'menus', 'skipped' );
				return array( 'next_step' => 'finalize', 'message' => 'No menu configuration found, skipping...' );
			}

			$menus = $demo_config['menus'];
			$menu_locations = get_theme_mod( 'nav_menu_locations' );

			foreach ( $menus as $location => $menu_name ) {
				$menu = wp_get_nav_menu_object( $menu_name );
				if ( $menu ) {
					$menu_locations[ $location ] = $menu->term_id;
				}
			}

			set_theme_mod( 'nav_menu_locations', $menu_locations );

			$this->update_import_step( 'menus', 'completed' );
			return array( 'next_step' => 'finalize', 'message' => 'Menus configured successfully' );
		}

		/**
		 * Finalize import
		 */
		private function finalize_import( $demo_slug ) {
			$demo_config = $this->get_demo_config( $demo_slug );

			// Set front page
			if ( isset( $demo_config['front_page'] ) ) {
				$front_page = get_page_by_title( $demo_config['front_page'] );
				if ( $front_page ) {
					update_option( 'show_on_front', 'page' );
					update_option( 'page_on_front', $front_page->ID );
				}
			}

			// Set blog page
			if ( isset( $demo_config['blog_page'] ) ) {
				$blog_page = get_page_by_title( $demo_config['blog_page'] );
				if ( $blog_page ) {
					update_option( 'page_for_posts', $blog_page->ID );
				}
			}

			// Clear caches
			$this->clear_caches();

			// Update import progress
			$this->import_progress['status'] = 'completed';
			$this->import_progress['completed_at'] = time();
			update_option( 'kr_import_progress', $this->import_progress );

			$this->update_import_step( 'finalize', 'completed' );

			// Log successful import
			$this->log_success( "Demo '{$demo_slug}' imported successfully" );

			return array( 
				'completed' => true, 
				'message' => __( 'Demo imported successfully!', 'kr-toolkit' ),
				'redirect_url' => home_url(),
			);
		}

		/**
		 * Update import step progress
		 */
		private function update_import_step( $step, $status ) {
			$progress = get_option( 'kr_import_progress', array() );
			
			if ( isset( $progress['steps'][ $step ] ) ) {
				$progress['steps'][ $step ]['status'] = $status;
				
				if ( $status === 'completed' ) {
					$progress['current_step']++;
				}
				
				update_option( 'kr_import_progress', $progress );
				$this->import_progress = $progress;
			}
		}

		/**
		 * AJAX: Get import status
		 */
		public function ajax_get_import_status() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'kr_toolkit_nonce' ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Invalid request' );
			}

			$progress = get_option( 'kr_import_progress', array() );
			wp_send_json_success( $progress );
		}

		/**
		 * Create backup point
		 */
		private function create_backup_point() {
			// Create a simple backup record
			$backup_data = array(
				'timestamp' => time(),
				'demo_import' => true,
			);
			update_option( 'kr_pre_import_backup', $backup_data );
		}

		/**
		 * Reset existing content
		 */
		private function reset_content() {
			// Reset posts, pages, and media (be careful!)
			$posts = get_posts( array( 'numberposts' => -1, 'post_status' => 'any' ) );
			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}

			// Reset widgets
			$sidebars_widgets = wp_get_sidebars_widgets();
			foreach ( $sidebars_widgets as $sidebar => $widgets ) {
				if ( $sidebar !== 'wp_inactive_widgets' ) {
					$sidebars_widgets[ $sidebar ] = array();
				}
			}
			wp_set_sidebars_widgets( $sidebars_widgets );

			// Reset customizer (keep basic settings)
			$theme_mods = get_theme_mods();
			$keep_mods = array( 'custom_css_post_id' );
			foreach ( $theme_mods as $mod => $value ) {
				if ( ! in_array( $mod, $keep_mods ) ) {
					remove_theme_mod( $mod );
				}
			}
		}

		/**
		 * Clear caches
		 */
		private function clear_caches() {
			// WordPress object cache
			wp_cache_flush();

			// Rewrite rules
			flush_rewrite_rules();

			// Common caching plugins
			if ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
			}

			if ( function_exists( 'w3tc_flush_all' ) ) {
				w3tc_flush_all();
			}

			if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
			}
		}

		/**
		 * Log error
		 */
		private function log_error( $message ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'KR Toolkit Import Error: ' . $message );
			}
		}

		/**
		 * Log success
		 */
		private function log_success( $message ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'KR Toolkit Import Success: ' . $message );
			}
		}
	}
}