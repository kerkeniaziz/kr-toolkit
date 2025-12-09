<?php
/**
 * KR Child Theme Manager
 * 
 * Handles child theme creation and management
 * Following WordPress best practices and Astra's proven patterns
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Child_Theme' ) ) {

	/**
	 * KR_Child_Theme class
	 */
	final class KR_Child_Theme {

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
			// Child theme functionality is primarily handled via AJAX
		}

		/**
		 * Create child theme
		 */
		public function create_child_theme( $child_name = '', $child_description = '' ) {
			try {
				// Validate permissions
				if ( ! current_user_can( 'install_themes' ) ) {
					throw new Exception( __( 'You do not have permission to create child themes.', 'kr-toolkit' ) );
				}

				// Check if parent theme is KR Theme
				$parent_theme = wp_get_theme();
				if ( 'kr-theme' !== $parent_theme->get_template() && 'KR Theme' !== $parent_theme->get( 'Name' ) ) {
					throw new Exception( __( 'Child theme can only be created for KR Theme.', 'kr-toolkit' ) );
				}

				// Set default values
				if ( empty( $child_name ) ) {
					$child_name = $parent_theme->get( 'Name' ) . ' Child';
				}
				
				if ( empty( $child_description ) ) {
					$child_description = sprintf( 
						__( 'Child theme of %s, created by KR Toolkit', 'kr-toolkit' ),
						$parent_theme->get( 'Name' )
					);
				}

				// Generate child theme directory name
				$child_slug = $this->generate_child_slug( $child_name );
				$child_dir = get_theme_root() . '/' . $child_slug;

				// Check if child theme already exists
				if ( file_exists( $child_dir ) ) {
					throw new Exception( sprintf( __( 'Child theme directory "%s" already exists.', 'kr-toolkit' ), $child_slug ) );
				}

				// Create child theme directory
				if ( ! wp_mkdir_p( $child_dir ) ) {
					throw new Exception( __( 'Could not create child theme directory. Please check file permissions.', 'kr-toolkit' ) );
				}

				// Create style.css
				$this->create_child_stylesheet( $child_dir, $child_name, $child_description, $parent_theme );

				// Create functions.php
				$this->create_child_functions( $child_dir );

				// Create screenshot (copy from parent if available)
				$this->copy_screenshot( $child_dir, $parent_theme );

				// Refresh themes
				wp_clean_themes_cache();

				return array(
					'success' => true,
					'message' => sprintf( __( 'Child theme "%s" created successfully!', 'kr-toolkit' ), $child_name ),
					'child_slug' => $child_slug,
					'child_name' => $child_name,
					'activate_url' => wp_nonce_url( admin_url( 'themes.php?action=activate&stylesheet=' . $child_slug ), 'switch-theme_' . $child_slug ),
				);

			} catch ( Exception $e ) {
				return new WP_Error( 'child_theme_creation_failed', $e->getMessage() );
			}
		}

		/**
		 * Generate child theme slug
		 */
		private function generate_child_slug( $child_name ) {
			$slug = sanitize_title( $child_name );
			
			// Ensure it's unique
			$original_slug = $slug;
			$counter = 1;
			
			while ( file_exists( get_theme_root() . '/' . $slug ) ) {
				$slug = $original_slug . '-' . $counter;
				$counter++;
			}
			
			return $slug;
		}

		/**
		 * Create child theme stylesheet
		 */
		private function create_child_stylesheet( $child_dir, $child_name, $child_description, $parent_theme ) {
			$stylesheet_content = sprintf(
				'<?php
/*
Theme Name: %1$s
Description: %2$s
Template: %3$s
Version: 1.0.0
Text Domain: %4$s
*/

/* ==========================================================================
   Add your custom styles below this line
   ========================================================================== */

',
				$child_name,
				$child_description,
				$parent_theme->get_template(),
				sanitize_title( $child_name )
			);

			$stylesheet_file = $child_dir . '/style.css';
			
			if ( ! file_put_contents( $stylesheet_file, $stylesheet_content ) ) {
				throw new Exception( __( 'Could not create style.css file.', 'kr-toolkit' ) );
			}
		}

		/**
		 * Create child theme functions.php
		 */
		private function create_child_functions( $child_dir ) {
			$functions_content = '<?php
/**
 * Child Theme Functions
 * 
 * Add your custom PHP code below this line
 */

if ( ! defined( \'ABSPATH\' ) ) {
	exit;
}

/**
 * Enqueue parent and child theme styles
 */
function kr_child_theme_styles() {
	// Enqueue parent theme style
	wp_enqueue_style( 
		\'kr-theme-style\', 
		get_template_directory_uri() . \'/style.css\',
		array(),
		wp_get_theme()->parent()->get( \'Version\' )
	);
	
	// Enqueue child theme style
	wp_enqueue_style( 
		\'kr-child-theme-style\', 
		get_stylesheet_directory_uri() . \'/style.css\',
		array( \'kr-theme-style\' ),
		wp_get_theme()->get( \'Version\' )
	);
}
add_action( \'wp_enqueue_scripts\', \'kr_child_theme_styles\', 15 );

/**
 * Add your custom functions below this line
 * ========================================
 */

';

			$functions_file = $child_dir . '/functions.php';
			
			if ( ! file_put_contents( $functions_file, $functions_content ) ) {
				throw new Exception( __( 'Could not create functions.php file.', 'kr-toolkit' ) );
			}
		}

		/**
		 * Copy screenshot from parent theme
		 */
		private function copy_screenshot( $child_dir, $parent_theme ) {
			$parent_dir = $parent_theme->get_template_directory();
			$screenshot_extensions = array( 'png', 'jpg', 'jpeg', 'gif' );
			
			foreach ( $screenshot_extensions as $ext ) {
				$parent_screenshot = $parent_dir . '/screenshot.' . $ext;
				
				if ( file_exists( $parent_screenshot ) ) {
					$child_screenshot = $child_dir . '/screenshot.' . $ext;
					copy( $parent_screenshot, $child_screenshot );
					break;
				}
			}
		}

		/**
		 * Check if current theme is a child theme
		 */
		public function is_child_theme() {
			return is_child_theme();
		}

		/**
		 * Get child theme information
		 */
		public function get_child_theme_info() {
			if ( ! $this->is_child_theme() ) {
				return false;
			}

			$theme = wp_get_theme();
			$parent = $theme->parent();

			return array(
				'name' => $theme->get( 'Name' ),
				'version' => $theme->get( 'Version' ),
				'description' => $theme->get( 'Description' ),
				'stylesheet' => $theme->get_stylesheet(),
				'template' => $theme->get_template(),
				'parent_name' => $parent ? $parent->get( 'Name' ) : '',
				'parent_version' => $parent ? $parent->get( 'Version' ) : '',
			);
		}

		/**
		 * Get child theme creation status
		 */
		public function get_child_theme_status() {
			$status = array(
				'has_child_theme' => $this->is_child_theme(),
				'can_create' => current_user_can( 'install_themes' ),
				'parent_theme' => wp_get_theme()->get( 'Name' ),
				'is_kr_theme' => $this->is_kr_theme_active(),
			);

			if ( $status['has_child_theme'] ) {
				$status['child_info'] = $this->get_child_theme_info();
			}

			return $status;
		}

		/**
		 * Check if KR Theme is active
		 */
		private function is_kr_theme_active() {
			$theme = wp_get_theme();
			return ( 'KR Theme' === $theme->get( 'Name' ) || 'kr-theme' === $theme->get_template() );
		}

		/**
		 * Backup current theme customizations
		 */
		public function backup_customizations() {
			try {
				$customizations = array(
					'theme_mods' => get_theme_mods(),
					'custom_css' => wp_get_custom_css(),
					'active_widgets' => wp_get_sidebars_widgets(),
					'menus' => wp_get_nav_menus(),
					'menu_locations' => get_nav_menu_locations(),
				);

				$backup_data = array(
					'timestamp' => current_time( 'timestamp' ),
					'theme' => wp_get_theme()->get( 'Name' ),
					'version' => wp_get_theme()->get( 'Version' ),
					'customizations' => $customizations,
				);

				update_option( 'kr_theme_backup_' . time(), $backup_data );

				return array(
					'success' => true,
					'message' => __( 'Theme customizations backed up successfully.', 'kr-toolkit' ),
				);

			} catch ( Exception $e ) {
				return new WP_Error( 'backup_failed', $e->getMessage() );
			}
		}

		/**
		 * Get available backups
		 */
		public function get_available_backups() {
			global $wpdb;

			$backups = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s ORDER BY option_id DESC",
					'kr_theme_backup_%'
				)
			);

			$backup_list = array();
			foreach ( $backups as $backup ) {
				$data = maybe_unserialize( $backup->option_value );
				$timestamp = str_replace( 'kr_theme_backup_', '', $backup->option_name );
				
				$backup_list[] = array(
					'key' => $backup->option_name,
					'timestamp' => (int) $timestamp,
					'date' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $timestamp ),
					'theme' => $data['theme'] ?? __( 'Unknown', 'kr-toolkit' ),
					'version' => $data['version'] ?? '',
				);
			}

			return $backup_list;
		}

		/**
		 * Restore from backup
		 */
		public function restore_from_backup( $backup_key ) {
			try {
				$backup_data = get_option( $backup_key );
				
				if ( ! $backup_data ) {
					throw new Exception( __( 'Backup not found.', 'kr-toolkit' ) );
				}

				$customizations = $backup_data['customizations'];

				// Restore theme mods
				if ( isset( $customizations['theme_mods'] ) ) {
					$current_mods = get_theme_mods();
					foreach ( $customizations['theme_mods'] as $mod => $value ) {
						set_theme_mod( $mod, $value );
					}
				}

				// Restore custom CSS
				if ( isset( $customizations['custom_css'] ) ) {
					wp_update_custom_css_post( $customizations['custom_css'] );
				}

				// Restore widgets
				if ( isset( $customizations['active_widgets'] ) ) {
					wp_set_sidebars_widgets( $customizations['active_widgets'] );
				}

				// Restore menu locations
				if ( isset( $customizations['menu_locations'] ) ) {
					set_theme_mod( 'nav_menu_locations', $customizations['menu_locations'] );
				}

				return array(
					'success' => true,
					'message' => __( 'Theme customizations restored successfully.', 'kr-toolkit' ),
				);

			} catch ( Exception $e ) {
				return new WP_Error( 'restore_failed', $e->getMessage() );
			}
		}
	}
}