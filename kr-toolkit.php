<?php
/**
 * Plugin Name: KR Toolkit
 * Plugin URI: https://krtheme.com
 * Description: Essential companion plugin for KR Theme. Features one-click demo import, child theme manager, license management, and system requirements checker. Unlock the full potential of KR Theme with this powerful toolkit.
 * Version: 1.2.4
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
 * @since 1.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin constants
define( 'KR_TOOLKIT_VERSION', '1.2.4' );
define( 'KR_TOOLKIT_FILE', __FILE__ );
define( 'KR_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );
define( 'KR_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );
define( 'KR_TOOLKIT_BASENAME', plugin_basename( __FILE__ ) );
define( 'KR_TOOLKIT_DIR', KR_TOOLKIT_PATH ); // Alias for compatibility

// ============================================
// PLUGIN COMPATIBILITY & ERROR HANDLING
// ============================================

/**
 * KR Toolkit Compatibility and Error Handler
 * 
 * Comprehensive system to check plugin compatibility, handle errors,
 * and provide detailed logging for debugging purposes.
 */
class KR_Toolkit_Compatibility {
    
    private static $errors = array();
    private static $warnings = array();
    private static $plugin_checks = array();
    
    /**
     * Initialize compatibility checks
     */
    public static function init() {
        // Run all compatibility checks
        self::check_php_version();
        self::check_wordpress_version();
        self::check_theme_compatibility();
        self::check_required_functions();
        self::check_memory_limit();
        self::check_file_permissions();
        self::check_plugin_conflicts();
        self::check_database_access();
        
        // Set up error handling
        if ( self::has_critical_errors() ) {
            add_action( 'admin_notices', array( __CLASS__, 'display_critical_errors' ) );
        }
        
        if ( self::has_warnings() ) {
            add_action( 'admin_notices', array( __CLASS__, 'display_warnings' ) );
        }
        
        // Log all checks for debugging
        self::log_compatibility_check();
    }
    
    /**
     * Check PHP version compatibility
     */
    private static function check_php_version() {
        $required_php = '7.4';
        $current_php = PHP_VERSION;
        
        if ( version_compare( $current_php, $required_php, '<' ) ) {
            self::$errors[] = array(
                'type' => 'php_version',
                'message' => sprintf(
                    'KR Toolkit requires PHP %s or higher. Current version: %s',
                    $required_php,
                    $current_php
                ),
                'critical' => true
            );
        }
    }
    
    /**
     * Check WordPress version compatibility
     */
    private static function check_wordpress_version() {
        global $wp_version;
        $required_wp = '6.0';
        
        if ( version_compare( $wp_version, $required_wp, '<' ) ) {
            self::$errors[] = array(
                'type' => 'wp_version',
                'message' => sprintf(
                    'KR Toolkit requires WordPress %s or higher. Current version: %s',
                    $required_wp,
                    $wp_version
                ),
                'critical' => true
            );
        }
    }
    
    /**
     * Check theme compatibility
     */
    private static function check_theme_compatibility() {
        $theme = wp_get_theme();
        $theme_name = $theme->get( 'Name' );
        $template = $theme->get_template();
        
        // Check if KR Theme is active
        if ( 'KR Theme' !== $theme_name && 'kr-theme' !== $template ) {
            self::$warnings[] = array(
                'type' => 'theme_compatibility',
                'message' => sprintf(
                    'KR Toolkit is designed for KR Theme. Current theme: %s. Some features may not work properly.',
                    $theme_name
                ),
                'critical' => false
            );
        }
        
        // Check theme version if KR Theme is active
        if ( 'KR Theme' === $theme_name || 'kr-theme' === $template ) {
            $theme_version = $theme->get( 'Version' );
            $required_theme_version = '1.2.0';
            
            if ( version_compare( $theme_version, $required_theme_version, '<' ) ) {
                self::$warnings[] = array(
                    'type' => 'theme_version',
                    'message' => sprintf(
                        'KR Toolkit works best with KR Theme %s or higher. Current theme version: %s',
                        $required_theme_version,
                        $theme_version
                    ),
                    'critical' => false
                );
            }
        }
    }
    
    /**
     * Check required PHP functions
     */
    private static function check_required_functions() {
        $required_functions = array(
            'curl_init' => 'cURL extension for GitHub updates and external API calls',
            'json_encode' => 'JSON extension for data processing',
            'file_get_contents' => 'File operations for demo import',
            'wp_remote_get' => 'WordPress HTTP API for external requests',
            'wp_filesystem' => 'WordPress Filesystem API for file operations',
            'zip_open' => 'ZIP extension for demo import archives',
            'simplexml_load_string' => 'XML extension for WordPress content import'
        );
        
        foreach ( $required_functions as $function => $purpose ) {
            if ( ! function_exists( $function ) ) {
                $is_critical = in_array( $function, array( 'wp_filesystem', 'json_encode' ) );
                
                $error_data = array(
                    'type' => 'missing_function',
                    'message' => sprintf(
                        'Function %s is not available. Required for: %s',
                        $function,
                        $purpose
                    ),
                    'critical' => $is_critical
                );
                
                if ( $is_critical ) {
                    self::$errors[] = $error_data;
                } else {
                    self::$warnings[] = $error_data;
                }
            }
        }
    }
    
    /**
     * Check memory limit
     */
    private static function check_memory_limit() {
        $memory_limit = wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) );
        $required_memory = 128 * 1024 * 1024; // 128MB for demo import
        
        if ( $memory_limit < $required_memory && $memory_limit > 0 ) {
            self::$warnings[] = array(
                'type' => 'memory_limit',
                'message' => sprintf(
                    'Memory limit is %s. Recommended: 128MB or higher for demo import functionality.',
                    size_format( $memory_limit )
                ),
                'critical' => false
            );
        }
    }
    
    /**
     * Check file permissions
     */
    private static function check_file_permissions() {
        $upload_dir = wp_upload_dir();
        $check_paths = array(
            KR_TOOLKIT_PATH => 'Plugin directory',
            $upload_dir['basedir'] => 'WordPress uploads directory',
            get_template_directory() => 'Active theme directory'
        );
        
        foreach ( $check_paths as $path => $description ) {
            if ( ! is_readable( $path ) ) {
                self::$errors[] = array(
                    'type' => 'file_permissions',
                    'message' => sprintf(
                        '%s is not readable: %s',
                        $description,
                        $path
                    ),
                    'critical' => true
                );
            }
            
            // Check write permissions for uploads directory
            if ( strpos( $path, 'uploads' ) !== false && ! is_writable( $path ) ) {
                self::$errors[] = array(
                    'type' => 'file_permissions',
                    'message' => sprintf(
                        '%s is not writable. Required for demo import and file operations.',
                        $description
                    ),
                    'critical' => true
                );
            }
        }
    }
    
    /**
     * Check for plugin conflicts
     */
    private static function check_plugin_conflicts() {
        $conflicting_plugins = array(
            'duplicate-post/duplicate-post.php' => 'May conflict with demo import functionality',
            'wordpress-importer/wordpress-importer.php' => 'May cause conflicts during demo import',
            'widget-importer-exporter/widget-importer-exporter.php' => 'May interfere with widget import'
        );
        
        foreach ( $conflicting_plugins as $plugin_file => $issue ) {
            if ( is_plugin_active( $plugin_file ) ) {
                self::$warnings[] = array(
                    'type' => 'plugin_conflict',
                    'message' => sprintf(
                        'Plugin %s is active. %s',
                        $plugin_file,
                        $issue
                    ),
                    'critical' => false
                );
            }
        }
        
        // Store plugin check results
        self::$plugin_checks = array(
            'active_plugins' => get_option( 'active_plugins', array() ),
            'network_plugins' => is_multisite() ? get_site_option( 'active_sitewide_plugins', array() ) : array()
        );
    }
    
    /**
     * Check database access
     */
    private static function check_database_access() {
        global $wpdb;
        
        // Test database connection
        $result = $wpdb->get_var( "SELECT 1" );
        
        if ( $result !== '1' ) {
            self::$errors[] = array(
                'type' => 'database_access',
                'message' => 'Cannot connect to WordPress database. Check database configuration.',
                'critical' => true
            );
        }
        
        // Check database permissions
        $tables_check = array(
            $wpdb->posts => 'Posts table access required for demo import',
            $wpdb->postmeta => 'Post meta table access required for demo import',
            $wpdb->options => 'Options table access required for settings'
        );
        
        foreach ( $tables_check as $table => $purpose ) {
            $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );
            
            if ( ! $table_exists ) {
                self::$errors[] = array(
                    'type' => 'database_table',
                    'message' => sprintf(
                        'Database table %s not found. %s',
                        $table,
                        $purpose
                    ),
                    'critical' => true
                );
            }
        }
    }
    
    /**
     * Check if there are critical errors
     */
    private static function has_critical_errors() {
        foreach ( self::$errors as $error ) {
            if ( $error['critical'] ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if there are warnings
     */
    private static function has_warnings() {
        return ! empty( self::$warnings ) || ! empty( array_filter( self::$errors, function( $error ) {
            return ! $error['critical'];
        } ) );
    }
    
    /**
     * Display critical errors in admin
     */
    public static function display_critical_errors() {
        $critical_errors = array_filter( self::$errors, function( $error ) {
            return $error['critical'];
        } );
        
        if ( empty( $critical_errors ) ) {
            return;
        }
        
        echo '<div class="notice notice-error"><p><strong>KR Toolkit - Critical Errors:</strong></p><ul>';
        foreach ( $critical_errors as $error ) {
            echo '<li>' . esc_html( $error['message'] ) . '</li>';
        }
        echo '</ul><p>Please resolve these issues for proper plugin functionality.</p></div>';
    }
    
    /**
     * Display warnings in admin
     */
    public static function display_warnings() {
        $warnings = self::$warnings;
        $non_critical_errors = array_filter( self::$errors, function( $error ) {
            return ! $error['critical'];
        } );
        
        $all_warnings = array_merge( $warnings, $non_critical_errors );
        
        if ( empty( $all_warnings ) ) {
            return;
        }
        
        echo '<div class="notice notice-warning is-dismissible"><p><strong>KR Toolkit - Recommendations:</strong></p><ul>';
        foreach ( $all_warnings as $warning ) {
            echo '<li>' . esc_html( $warning['message'] ) . '</li>';
        }
        echo '</ul><p>These are recommendations for optimal functionality.</p></div>';
    }
    
    /**
     * Log compatibility check results
     */
    private static function log_compatibility_check() {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $log_data = array(
                'timestamp' => current_time( 'Y-m-d H:i:s' ),
                'php_version' => PHP_VERSION,
                'wp_version' => get_bloginfo( 'version' ),
                'plugin_version' => KR_TOOLKIT_VERSION,
                'theme_info' => array(
                    'name' => wp_get_theme()->get( 'Name' ),
                    'version' => wp_get_theme()->get( 'Version' ),
                    'template' => get_template()
                ),
                'errors' => self::$errors,
                'warnings' => self::$warnings,
                'plugin_checks' => self::$plugin_checks,
                'system_info' => array(
                    'memory_limit' => ini_get( 'memory_limit' ),
                    'max_execution_time' => ini_get( 'max_execution_time' ),
                    'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
                    'post_max_size' => ini_get( 'post_max_size' )
                )
            );
            
            error_log( 'KR Toolkit Compatibility Check: ' . wp_json_encode( $log_data, JSON_PRETTY_PRINT ) );
        }
    }
    
    /**
     * Get compatibility report for debugging
     */
    public static function get_compatibility_report() {
        return array(
            'errors' => self::$errors,
            'warnings' => self::$warnings,
            'plugin_checks' => self::$plugin_checks,
            'system_info' => array(
                'php_version' => PHP_VERSION,
                'wp_version' => get_bloginfo( 'version' ),
                'plugin_version' => KR_TOOLKIT_VERSION,
                'theme_name' => wp_get_theme()->get( 'Name' ),
                'theme_version' => wp_get_theme()->get( 'Version' ),
                'memory_limit' => ini_get( 'memory_limit' ),
                'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
                'post_max_size' => ini_get( 'post_max_size' ),
                'max_execution_time' => ini_get( 'max_execution_time' ),
                'max_input_vars' => ini_get( 'max_input_vars' )
            )
        );
    }
}

// Initialize compatibility checks
KR_Toolkit_Compatibility::init();

// ============================================
// ENHANCED GITHUB AUTO-UPDATE SYSTEM
// ============================================

/**
 * Enhanced GitHub auto-update system with comprehensive error handling
 */
function kr_toolkit_init_github_updater() {
    $updater_path = KR_TOOLKIT_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';
    
    if ( ! file_exists( $updater_path ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KR Toolkit: Update checker library not found at: ' . $updater_path );
        }
        
        // Show admin notice about missing updater
        add_action( 'admin_notices', function() {
            if ( current_user_can( 'manage_options' ) ) {
                echo '<div class="notice notice-info is-dismissible"><p>';
                echo '<strong>KR Toolkit:</strong> Auto-update system is not available. ';
                echo '<a href="https://github.com/YahnisElsts/plugin-update-checker" target="_blank">Download the Plugin Update Checker library</a> for automatic updates.';
                echo '</p></div>';
            }
        });
        
        return false;
    }
    
    try {
        require_once $updater_path;
        
        if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
            throw new Exception( 'PucFactory class not found in update checker library' );
        }
        
        use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
        
        $kr_toolkit_update_checker = PucFactory::buildUpdateChecker(
            'https://github.com/kerkeniaziz/kr-toolkit',
            __FILE__,
            'kr-toolkit'
        );
        
        $kr_toolkit_update_checker->setBranch('main');
        $kr_toolkit_update_checker->getVcsApi()->enableReleaseAssets();
        
        // Add error handling for update check failures
        add_action( 'puc_api_error', function( $error, $url, $slug ) {
            if ( $slug === 'kr-toolkit' && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'KR Toolkit Update Error: ' . $error . ' - URL: ' . $url );
            }
        }, 10, 3 );
        
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KR Toolkit: GitHub updater initialized successfully' );
        }
        
        return true;
        
    } catch ( Exception $e ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KR Toolkit: Failed to initialize GitHub updater: ' . $e->getMessage() );
        }
        
        add_action( 'admin_notices', function() use ( $e ) {
            if ( current_user_can( 'manage_options' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                echo '<div class="notice notice-warning"><p>';
                echo '<strong>KR Toolkit:</strong> Update system error: ' . esc_html( $e->getMessage() );
                echo '</p></div>';
            }
        });
        
        return false;
    }
}

// Initialize GitHub updater
kr_toolkit_init_github_updater();

/**
 * Safe file loading function with error handling
 */
function kr_toolkit_safe_require( $file_path, $description = '' ) {
    if ( ! file_exists( $file_path ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( "KR Toolkit: Missing file - {$description}: {$file_path}" );
        }
        
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'admin_notices', function() use ( $description, $file_path ) {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>KR Toolkit:</strong> Missing required file: ' . esc_html( $description );
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    echo '<br><code>' . esc_html( $file_path ) . '</code>';
                }
                echo '</p></div>';
            });
        }
        
        return false;
    }
    
    try {
        require_once $file_path;
        return true;
    } catch ( Exception $e ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( "KR Toolkit: Error loading {$description}: " . $e->getMessage() );
        }
        
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'admin_notices', function() use ( $description, $e ) {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>KR Toolkit:</strong> Error loading ' . esc_html( $description ) . ': ';
                echo esc_html( $e->getMessage() );
                echo '</p></div>';
            });
        }
        
        return false;
    }
}

/**
 * Enhanced Main KR Toolkit Class with Error Handling
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
	 * Load required dependencies with error handling
	 */
	private function load_dependencies() {
		$dependencies = array(
			KR_TOOLKIT_PATH . 'includes/class-kr-toolkit.php' => 'KR Toolkit Main Class',
			KR_TOOLKIT_PATH . 'includes/class-demo-importer.php' => 'Demo Importer',
			KR_TOOLKIT_PATH . 'includes/class-child-theme-manager.php' => 'Child Theme Manager',
			KR_TOOLKIT_PATH . 'includes/class-license-manager.php' => 'License Manager',
			KR_TOOLKIT_PATH . 'includes/class-system-requirements.php' => 'System Requirements',
			KR_TOOLKIT_PATH . 'includes/class-customizer-extensions.php' => 'Customizer Extensions',
			KR_TOOLKIT_PATH . 'includes/helpers.php' => 'Helper Functions'
		);

		foreach ( $dependencies as $file_path => $description ) {
			kr_toolkit_safe_require( $file_path, $description );
		}

		// Admin classes
		if ( is_admin() ) {
			kr_toolkit_safe_require( KR_TOOLKIT_PATH . 'admin/class-admin.php', 'Admin Interface' );
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
