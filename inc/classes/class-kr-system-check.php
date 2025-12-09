<?php
/**
 * KR System Check
 * 
 * Handles system requirements validation for optimal performance
 * Based on Astra Sites' comprehensive checking system
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_System_Check' ) ) {

	/**
	 * KR_System_Check class
	 */
	final class KR_System_Check {

		/**
		 * Instance
		 */
		private static $instance = null;

		/**
		 * System check results
		 */
		private $system_status = array();

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
			// Admin notices for critical issues
			add_action( 'admin_notices', array( $this, 'display_system_notices' ) );
		}

		/**
		 * Run comprehensive system checks
		 */
		public function run_checks() {
			$this->check_php_version();
			$this->check_wordpress_version();
			$this->check_memory_limit();
			$this->check_max_execution_time();
			$this->check_file_permissions();
			$this->check_required_functions();
			$this->check_php_extensions();
			$this->check_database_connectivity();
			$this->check_theme_compatibility();
			$this->check_plugin_conflicts();
			
			// Store results
			update_option( 'kr_system_status', $this->system_status );
		}

		/**
		 * Check PHP Version
		 */
		private function check_php_version() {
			$required_version = '7.4';
			$current_version = PHP_VERSION;
			$status = version_compare( $current_version, $required_version, '>=' );
			
			$this->system_status['php_version'] = array(
				'label' => __( 'PHP Version', 'kr-toolkit' ),
				'current' => $current_version,
				'required' => $required_version . '+',
				'status' => $status,
				'required' => true,
				'message' => $status 
					? __( 'Your PHP version is compatible.', 'kr-toolkit' )
					: sprintf( __( 'Your PHP version (%s) is outdated. Please upgrade to PHP %s or higher.', 'kr-toolkit' ), $current_version, $required_version ),
				'type' => $status ? 'success' : 'error',
			);
		}

		/**
		 * Check WordPress Version
		 */
		private function check_wordpress_version() {
			global $wp_version;
			$required_version = '6.0';
			$status = version_compare( $wp_version, $required_version, '>=' );
			
			$this->system_status['wp_version'] = array(
				'label' => __( 'WordPress Version', 'kr-toolkit' ),
				'current' => $wp_version,
				'required' => $required_version . '+',
				'status' => $status,
				'required' => true,
				'message' => $status 
					? __( 'Your WordPress version is compatible.', 'kr-toolkit' )
					: sprintf( __( 'Your WordPress version (%s) is outdated. Please upgrade to WordPress %s or higher.', 'kr-toolkit' ), $wp_version, $required_version ),
				'type' => $status ? 'success' : 'error',
			);
		}

		/**
		 * Check Memory Limit
		 */
		private function check_memory_limit() {
			$memory_limit = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
			$required_memory = 128 * 1024 * 1024; // 128MB
			$recommended_memory = 256 * 1024 * 1024; // 256MB
			
			$status = $memory_limit >= $required_memory;
			$is_optimal = $memory_limit >= $recommended_memory;
			
			$this->system_status['memory_limit'] = array(
				'label' => __( 'Memory Limit', 'kr-toolkit' ),
				'current' => size_format( $memory_limit ),
				'required' => '128MB',
				'recommended' => '256MB',
				'status' => $status,
				'required' => true,
				'message' => $is_optimal 
					? __( 'Your memory limit is optimal.', 'kr-toolkit' )
					: ( $status 
						? __( 'Your memory limit meets minimum requirements but could be higher for better performance.', 'kr-toolkit' )
						: __( 'Your memory limit is too low. Please increase it to at least 128MB.', 'kr-toolkit' ) ),
				'type' => $is_optimal ? 'success' : ( $status ? 'warning' : 'error' ),
			);
		}

		/**
		 * Check Max Execution Time
		 */
		private function check_max_execution_time() {
			$max_execution_time = (int) ini_get( 'max_execution_time' );
			$required_time = 60;
			$recommended_time = 300;
			
			// 0 means no limit
			$status = ( $max_execution_time === 0 ) || ( $max_execution_time >= $required_time );
			$is_optimal = ( $max_execution_time === 0 ) || ( $max_execution_time >= $recommended_time );
			
			$this->system_status['max_execution_time'] = array(
				'label' => __( 'Max Execution Time', 'kr-toolkit' ),
				'current' => $max_execution_time === 0 ? __( 'Unlimited', 'kr-toolkit' ) : $max_execution_time . 's',
				'required' => $required_time . 's',
				'recommended' => $recommended_time . 's',
				'status' => $status,
				'required' => false,
				'message' => $is_optimal 
					? __( 'Your execution time limit is optimal.', 'kr-toolkit' )
					: ( $status 
						? __( 'Your execution time meets minimum requirements.', 'kr-toolkit' )
						: __( 'Your execution time limit may be too low for demo imports.', 'kr-toolkit' ) ),
				'type' => $is_optimal ? 'success' : ( $status ? 'warning' : 'error' ),
			);
		}

		/**
		 * Check File Permissions
		 */
		private function check_file_permissions() {
			$upload_dir = wp_upload_dir();
			$writable = wp_is_writable( $upload_dir['basedir'] );
			
			$this->system_status['file_permissions'] = array(
				'label' => __( 'File Permissions', 'kr-toolkit' ),
				'current' => $writable ? __( 'Writable', 'kr-toolkit' ) : __( 'Not Writable', 'kr-toolkit' ),
				'required' => __( 'Writable', 'kr-toolkit' ),
				'status' => $writable,
				'required' => true,
				'message' => $writable 
					? __( 'Upload directory is writable.', 'kr-toolkit' )
					: __( 'Upload directory is not writable. Please check file permissions.', 'kr-toolkit' ),
				'type' => $writable ? 'success' : 'error',
			);
		}

		/**
		 * Check Required PHP Functions
		 */
		private function check_required_functions() {
			$required_functions = array(
				'curl_init' => __( 'cURL', 'kr-toolkit' ),
				'json_encode' => __( 'JSON', 'kr-toolkit' ),
				'file_get_contents' => __( 'File Functions', 'kr-toolkit' ),
				'gzinflate' => __( 'Gzip', 'kr-toolkit' ),
			);
			
			$missing_functions = array();
			foreach ( $required_functions as $function => $label ) {
				if ( ! function_exists( $function ) ) {
					$missing_functions[] = $label;
				}
			}
			
			$status = empty( $missing_functions );
			
			$this->system_status['php_functions'] = array(
				'label' => __( 'Required PHP Functions', 'kr-toolkit' ),
				'current' => $status ? __( 'All Available', 'kr-toolkit' ) : sprintf( __( 'Missing: %s', 'kr-toolkit' ), implode( ', ', $missing_functions ) ),
				'required' => __( 'All Functions', 'kr-toolkit' ),
				'status' => $status,
				'required' => true,
				'message' => $status 
					? __( 'All required PHP functions are available.', 'kr-toolkit' )
					: sprintf( __( 'Missing required PHP functions: %s', 'kr-toolkit' ), implode( ', ', $missing_functions ) ),
				'type' => $status ? 'success' : 'error',
			);
		}

		/**
		 * Check PHP Extensions
		 */
		private function check_php_extensions() {
			$required_extensions = array(
				'json' => __( 'JSON Extension', 'kr-toolkit' ),
				'mbstring' => __( 'Multibyte String', 'kr-toolkit' ),
				'openssl' => __( 'OpenSSL', 'kr-toolkit' ),
				'curl' => __( 'cURL Extension', 'kr-toolkit' ),
			);
			
			$missing_extensions = array();
			foreach ( $required_extensions as $extension => $label ) {
				if ( ! extension_loaded( $extension ) ) {
					$missing_extensions[] = $label;
				}
			}
			
			$status = empty( $missing_extensions );
			
			$this->system_status['php_extensions'] = array(
				'label' => __( 'PHP Extensions', 'kr-toolkit' ),
				'current' => $status ? __( 'All Available', 'kr-toolkit' ) : sprintf( __( 'Missing: %s', 'kr-toolkit' ), implode( ', ', $missing_extensions ) ),
				'required' => __( 'All Extensions', 'kr-toolkit' ),
				'status' => $status,
				'required' => false,
				'message' => $status 
					? __( 'All recommended PHP extensions are loaded.', 'kr-toolkit' )
					: sprintf( __( 'Missing recommended PHP extensions: %s', 'kr-toolkit' ), implode( ', ', $missing_extensions ) ),
				'type' => $status ? 'success' : 'warning',
			);
		}

		/**
		 * Check Database Connectivity
		 */
		private function check_database_connectivity() {
			global $wpdb;
			
			$status = true;
			$message = __( 'Database connection is working properly.', 'kr-toolkit' );
			
			// Test database connection
			$test_query = $wpdb->get_var( "SELECT 1" );
			if ( $test_query !== '1' ) {
				$status = false;
				$message = __( 'Database connection test failed.', 'kr-toolkit' );
			}
			
			// Check database version
			$db_version = $wpdb->get_var( "SELECT VERSION()" );
			$mysql_version = '5.6';
			
			if ( $db_version && version_compare( $db_version, $mysql_version, '<' ) ) {
				$status = false;
				$message = sprintf( __( 'Database version (%s) is outdated. MySQL %s+ recommended.', 'kr-toolkit' ), $db_version, $mysql_version );
			}
			
			$this->system_status['database'] = array(
				'label' => __( 'Database', 'kr-toolkit' ),
				'current' => $db_version ? 'MySQL ' . $db_version : __( 'Connected', 'kr-toolkit' ),
				'required' => 'MySQL ' . $mysql_version . '+',
				'status' => $status,
				'required' => true,
				'message' => $message,
				'type' => $status ? 'success' : 'error',
			);
		}

		/**
		 * Check Theme Compatibility
		 */
		private function check_theme_compatibility() {
			$theme = wp_get_theme();
			$is_kr_theme = ( 'KR Theme' === $theme->get( 'Name' ) || 'kr-theme' === $theme->get_template() );
			
			$this->system_status['theme_compatibility'] = array(
				'label' => __( 'Theme Compatibility', 'kr-toolkit' ),
				'current' => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
				'required' => __( 'KR Theme', 'kr-toolkit' ),
				'status' => $is_kr_theme,
				'required' => true,
				'message' => $is_kr_theme 
					? __( 'KR Theme is active and compatible.', 'kr-toolkit' )
					: __( 'KR Toolkit requires KR Theme to function properly.', 'kr-toolkit' ),
				'type' => $is_kr_theme ? 'success' : 'error',
			);
		}

		/**
		 * Check for Plugin Conflicts
		 */
		private function check_plugin_conflicts() {
			$conflicting_plugins = array(
				'wp-super-cache/wp-cache.php' => 'WP Super Cache',
				'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
				'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
			);
			
			$active_conflicts = array();
			foreach ( $conflicting_plugins as $plugin_file => $plugin_name ) {
				if ( is_plugin_active( $plugin_file ) ) {
					$active_conflicts[] = $plugin_name;
				}
			}
			
			$status = empty( $active_conflicts );
			
			$this->system_status['plugin_conflicts'] = array(
				'label' => __( 'Plugin Conflicts', 'kr-toolkit' ),
				'current' => $status ? __( 'None Detected', 'kr-toolkit' ) : sprintf( __( 'Potential conflicts: %s', 'kr-toolkit' ), implode( ', ', $active_conflicts ) ),
				'required' => __( 'None', 'kr-toolkit' ),
				'status' => $status,
				'required' => false,
				'message' => $status 
					? __( 'No potential plugin conflicts detected.', 'kr-toolkit' )
					: __( 'Some active plugins may cause conflicts during import. Consider temporarily deactivating them.', 'kr-toolkit' ),
				'type' => $status ? 'success' : 'warning',
			);
		}

		/**
		 * Get import-specific requirements check
		 */
		public function get_import_requirements_check() {
			$requirements = array();
			
			foreach ( $this->system_status as $key => $check ) {
				if ( $check['required'] && ! $check['status'] ) {
					$requirements[] = array(
						'key' => $key,
						'status' => false,
						'required' => true,
						'message' => $check['message'],
					);
				} elseif ( ! $check['required'] && ! $check['status'] ) {
					$requirements[] = array(
						'key' => $key,
						'status' => false,
						'required' => false,
						'message' => $check['message'],
					);
				}
			}
			
			return $requirements;
		}

		/**
		 * Display admin notices for critical issues
		 */
		public function display_system_notices() {
			$system_status = get_option( 'kr_system_status', array() );
			
			foreach ( $system_status as $check ) {
				if ( $check['required'] && ! $check['status'] ) {
					?>
					<div class="notice notice-error">
						<p><strong><?php echo esc_html( $check['label'] ); ?>:</strong> <?php echo esc_html( $check['message'] ); ?></p>
					</div>
					<?php
				}
			}
		}

		/**
		 * Get comprehensive system report
		 */
		public function get_system_report() {
			global $wp_version, $wpdb;
			
			// Refresh system status
			$this->run_checks();
			
			// Get additional system information
			$theme = wp_get_theme();
			$upload_dir = wp_upload_dir();
			
			$system_report = array(
				'site_info' => array(
					'site_url' => home_url(),
					'admin_url' => admin_url(),
					'wp_version' => $wp_version,
					'is_multisite' => is_multisite(),
					'language' => get_locale(),
					'timezone' => get_option( 'timezone_string' ),
				),
				'theme_info' => array(
					'name' => $theme->get( 'Name' ),
					'version' => $theme->get( 'Version' ),
					'template' => $theme->get_template(),
					'stylesheet' => $theme->get_stylesheet(),
					'parent_theme' => $theme->parent() ? $theme->parent()->get( 'Name' ) : null,
				),
				'server_info' => array(
					'php_version' => PHP_VERSION,
					'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
					'memory_limit' => ini_get( 'memory_limit' ),
					'max_execution_time' => ini_get( 'max_execution_time' ),
					'max_input_vars' => ini_get( 'max_input_vars' ),
					'post_max_size' => ini_get( 'post_max_size' ),
					'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
				),
				'database_info' => array(
					'version' => $wpdb->get_var( "SELECT VERSION()" ),
					'charset' => $wpdb->charset,
					'collate' => $wpdb->collate,
					'table_prefix' => $wpdb->prefix,
				),
				'filesystem_info' => array(
					'upload_dir' => $upload_dir['basedir'],
					'upload_url' => $upload_dir['baseurl'],
					'upload_writable' => wp_is_writable( $upload_dir['basedir'] ),
				),
				'plugin_info' => array(
					'kr_toolkit_version' => KR_TOOLKIT_VERSION,
					'active_plugins' => get_option( 'active_plugins' ),
				),
				'system_checks' => $this->system_status,
				'generated_at' => current_time( 'Y-m-d H:i:s' ),
			);
			
			return $system_report;
		}

		/**
		 * Get system status summary
		 */
		public function get_status_summary() {
			$total_checks = count( $this->system_status );
			$passed_checks = 0;
			$failed_critical = 0;
			$warnings = 0;
			
			foreach ( $this->system_status as $check ) {
				if ( $check['status'] ) {
					$passed_checks++;
				} elseif ( $check['required'] ) {
					$failed_critical++;
				} else {
					$warnings++;
				}
			}
			
			return array(
				'total' => $total_checks,
				'passed' => $passed_checks,
				'critical_failed' => $failed_critical,
				'warnings' => $warnings,
				'percentage' => round( ( $passed_checks / $total_checks ) * 100 ),
				'ready_for_import' => $failed_critical === 0,
			);
		}
	}
}