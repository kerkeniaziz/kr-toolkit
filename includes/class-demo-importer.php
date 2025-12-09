<?php
/**
 * Demo Importer Class
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_Demo_Importer Class
 */
class KR_Demo_Importer {

	/**
	 * Available demos
	 *
	 * @var array
	 */
	private $demos = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_demos();
	}

	/**
	 * Load available demos
	 */
	private function load_demos() {
		$this->demos = array(
			array(
				'slug'        => 'free-business',
				'name'        => 'Business',
				'description' => 'Professional business website with services and portfolio',
				'demo_url'    => 'https://demo.krtheme.com/business',
				'screenshot'  => 'screenshot.png',
				'is_pro'      => false,
				'features'    => array(
					'Homepage design',
					'Services section',
					'Portfolio gallery',
					'Contact form',
				),
			),
			array(
				'slug'        => 'free-portfolio',
				'name'        => 'Portfolio',
				'description' => 'Creative portfolio showcase for designers and photographers',
				'demo_url'    => 'https://demo.krtheme.com/portfolio',
				'screenshot'  => 'screenshot.png',
				'is_pro'      => false,
				'features'    => array(
					'Gallery layouts',
					'Project showcase',
					'About page',
					'Contact section',
				),
			),
			array(
				'slug'        => 'pro-restaurant',
				'name'        => 'Restaurant Pro',
				'description' => 'Beautiful restaurant website with menu and reservations',
				'demo_url'    => 'https://demo.krtheme.com/restaurant',
				'screenshot'  => 'screenshot.png',
				'is_pro'      => true,
				'features'    => array(
					'Menu showcase',
					'Online reservations',
					'Gallery',
					'Contact & location',
				),
			),
		);

		// Allow filtering demos
		$this->demos = apply_filters( 'kr_toolkit_demos', $this->demos );
	}

	/**
	 * Get available demos
	 *
	 * @return array
	 */
	public function get_available_demos() {
		return $this->demos;
	}

	/**
	 * Get demo by slug
	 *
	 * @param string $slug Demo slug.
	 * @return array|false
	 */
	public function get_demo( $slug ) {
		foreach ( $this->demos as $demo ) {
			if ( $demo['slug'] === $slug ) {
				return $demo;
			}
		}
		return false;
	}

	/**
	 * Import demo
	 *
	 * @param string $demo_slug Demo slug to import.
	 * @return bool|WP_Error
	 */
	public function import_demo( $demo_slug ) {
		$demo = $this->get_demo( $demo_slug );

		if ( ! $demo ) {
			return new WP_Error( 'invalid_demo', __( 'Invalid demo selected.', 'kr-toolkit' ) );
		}

		// Check if pro demo and license
		if ( isset( $demo['is_pro'] ) && $demo['is_pro'] ) {
			$license_manager = new KR_License_Manager();
			if ( ! $license_manager->is_license_active() ) {
				return new WP_Error( 'license_required', __( 'Active license required for pro demos.', 'kr-toolkit' ) );
			}
		}

		// Check system requirements - show warning but continue
		$system_check = new KR_System_Requirements();
		$requirements_met = $system_check->all_requirements_met();
		
		// Store warning message if requirements not met
		if ( ! $requirements_met ) {
			$warning_message = __( 'Warning: Some system requirements are not met. The import will continue, but you may experience issues. Please check System Info tab for details.', 'kr-toolkit' );
			set_transient( 'kr_toolkit_import_warning', $warning_message, 300 ); // 5 minutes
		}

		// Start import process
		$demo_path = KR_TOOLKIT_PATH . 'demos/' . $demo_slug . '/';

		if ( ! file_exists( $demo_path ) ) {
			return new WP_Error( 'demo_not_found', __( 'Demo files not found.', 'kr-toolkit' ) );
		}

		// Import content
		$this->import_content( $demo_path );

		// Import customizer settings
		$this->import_customizer( $demo_path );

		// Import widgets
		$this->import_widgets( $demo_path );

		// Set homepage
		$this->set_homepage( $demo_slug );

		// Save imported demo info
		$imported_demos = get_option( 'kr_toolkit_imported_demos', array() );
		$imported_demos[] = array(
			'slug'      => $demo_slug,
			'date'      => current_time( 'mysql' ),
			'timestamp' => time(),
			'requirements_met' => $requirements_met, // Track if requirements were met during import
		);
		update_option( 'kr_toolkit_imported_demos', $imported_demos );

		do_action( 'kr_toolkit_after_demo_import', $demo_slug );

		return true;
	}

	/**
	 * Import content (posts, pages, etc.)
	 *
	 * @param string $demo_path Path to demo files.
	 */
	private function import_content( $demo_path ) {
		$content_file = $demo_path . 'content.xml';

		if ( ! file_exists( $content_file ) ) {
			return;
		}

		// Use WordPress importer if available
		if ( ! class_exists( 'WP_Import' ) ) {
			$importer_path = ABSPATH . 'wp-admin/includes/import.php';
			if ( file_exists( $importer_path ) ) {
				require_once $importer_path;
			}
		}

		// Import content using WP_Import
		if ( class_exists( 'WP_Import' ) ) {
			$importer = new WP_Import();
			$importer->fetch_attachments = true;
			ob_start();
			$importer->import( $content_file );
			ob_end_clean();
		}
	}

	/**
	 * Import customizer settings
	 *
	 * @param string $demo_path Path to demo files.
	 */
	private function import_customizer( $demo_path ) {
		$customizer_file = $demo_path . 'customizer.json';

		if ( ! file_exists( $customizer_file ) ) {
			return;
		}

		$customizer_data = file_get_contents( $customizer_file );
		$settings = json_decode( $customizer_data, true );

		if ( ! empty( $settings ) && is_array( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				set_theme_mod( $key, $value );
			}
		}
	}

	/**
	 * Import widgets
	 *
	 * @param string $demo_path Path to demo files.
	 */
	private function import_widgets( $demo_path ) {
		$widgets_file = $demo_path . 'widgets.json';

		if ( ! file_exists( $widgets_file ) ) {
			return;
		}

		$widgets_data = file_get_contents( $widgets_file );
		$widgets = json_decode( $widgets_data, true );

		if ( ! empty( $widgets ) && is_array( $widgets ) ) {
			foreach ( $widgets as $sidebar_id => $sidebar_widgets ) {
				update_option( 'sidebars_widgets', array( $sidebar_id => $sidebar_widgets ) );
			}
		}
	}

	/**
	 * Set homepage after import
	 *
	 * @param string $demo_slug Demo slug.
	 */
	private function set_homepage( $demo_slug ) {
		// Find the homepage
		$homepage = get_page_by_title( 'Home' );

		if ( ! $homepage ) {
			$homepage = get_page_by_title( 'Homepage' );
		}

		if ( $homepage ) {
			update_option( 'page_on_front', $homepage->ID );
			update_option( 'show_on_front', 'page' );
		}

		// Find blog page
		$blog_page = get_page_by_title( 'Blog' );

		if ( $blog_page ) {
			update_option( 'page_for_posts', $blog_page->ID );
		}
	}

	/**
	 * Reset/remove demo content
	 *
	 * @param string $demo_slug Demo slug to reset.
	 * @return bool|WP_Error
	 */
	public function reset_demo( $demo_slug ) {
		// Get all posts/pages imported from this demo
		$args = array(
			'post_type'      => array( 'post', 'page' ),
			'posts_per_page' => -1,
			'meta_key'       => '_kr_demo_import',
			'meta_value'     => $demo_slug,
		);

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}

		// Remove from imported list
		$imported_demos = get_option( 'kr_toolkit_imported_demos', array() );
		$imported_demos = array_filter( $imported_demos, function( $demo ) use ( $demo_slug ) {
			return $demo['slug'] !== $demo_slug;
		});
		update_option( 'kr_toolkit_imported_demos', $imported_demos );

		return true;
	}

	/**
	 * Get imported demos
	 *
	 * @return array
	 */
	public function get_imported_demos() {
		return get_option( 'kr_toolkit_imported_demos', array() );
	}

	/**
	 * Check if demo is imported
	 *
	 * @param string $demo_slug Demo slug.
	 * @return bool
	 */
	public function is_demo_imported( $demo_slug ) {
		$imported = $this->get_imported_demos();
		foreach ( $imported as $demo ) {
			if ( $demo['slug'] === $demo_slug ) {
				return true;
			}
		}
		return false;
	}
}
