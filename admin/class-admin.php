<?php
/**
 * Admin Class
 *
 * @package KR_Toolkit
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_Toolkit_Admin Class
 */
class KR_Toolkit_Admin {

	/**
	 * Instance
	 *
	 * @var KR_Toolkit_Admin
	 */
	private static $instance = null;

	/**
	 * Get instance
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
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'welcome_redirect' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'KR Toolkit', 'kr-toolkit' ),
			__( 'KR Toolkit', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-dashboard',
			array( $this, 'render_dashboard_page' ),
			'dashicons-admin-customizer',
			59
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Dashboard', 'kr-toolkit' ),
			__( 'Dashboard', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-dashboard',
			array( $this, 'render_dashboard_page' )
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Demo Import', 'kr-toolkit' ),
			__( 'Demo Import', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-demos',
			array( $this, 'render_demos_page' )
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Child Theme', 'kr-toolkit' ),
			__( 'Child Theme', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-child-theme',
			array( $this, 'render_child_theme_page' )
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'System Info', 'kr-toolkit' ),
			__( 'System Info', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-system-info',
			array( $this, 'render_system_info_page' )
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'License', 'kr-toolkit' ),
			__( 'License', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-license',
			array( $this, 'render_license_page' )
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Settings', 'kr-toolkit' ),
			__( 'Settings', 'kr-toolkit' ),
			'manage_options',
			'kr-toolkit-settings',
			array( $this, 'render_settings_page' )
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Theme Options', 'kr-toolkit' ),
			__( 'Theme Options', 'kr-toolkit' ),
			'manage_options',
			'admin.php?page=kr_theme_options'
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Header Builder', 'kr-toolkit' ),
			__( 'Header Builder', 'kr-toolkit' ),
			'manage_options',
			'edit.php?post_type=kr_header'
		);

		add_submenu_page(
			'kr-toolkit-dashboard',
			__( 'Footer Builder', 'kr-toolkit' ),
			__( 'Footer Builder', 'kr-toolkit' ),
			'manage_options',
			'edit.php?post_type=kr_footer'
		);
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on our admin pages
		if ( strpos( $hook, 'kr-toolkit' ) === false ) {
			return;
		}

		// Enqueue CSS
		wp_enqueue_style( 'kr-toolkit-admin', KR_TOOLKIT_URL . 'admin/css/admin.css', array(), KR_TOOLKIT_VERSION );

		// Enqueue JS
		wp_enqueue_script( 'kr-toolkit-admin', KR_TOOLKIT_URL . 'admin/js/admin.js', array( 'jquery' ), KR_TOOLKIT_VERSION, true );

		// Localize script
		wp_localize_script( 'kr-toolkit-admin', 'krToolkitAdmin', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'kr_toolkit_nonce' ),
			'strings' => array(
				'importing'       => __( 'Importing demo...', 'kr-toolkit' ),
				'importSuccess'   => __( 'Demo imported successfully!', 'kr-toolkit' ),
				'importError'     => __( 'Import failed. Please try again.', 'kr-toolkit' ),
				'confirmImport'   => __( 'This will import demo content to your site. Continue?', 'kr-toolkit' ),
				'creating'        => __( 'Creating child theme...', 'kr-toolkit' ),
				'createSuccess'   => __( 'Child theme created successfully!', 'kr-toolkit' ),
				'createError'     => __( 'Creation failed. Please try again.', 'kr-toolkit' ),
				'activating'      => __( 'Activating license...', 'kr-toolkit' ),
				'activateSuccess' => __( 'License activated successfully!', 'kr-toolkit' ),
				'activateError'   => __( 'Activation failed. Please check your license key.', 'kr-toolkit' ),
			),
		) );
	}

	/**
	 * Welcome redirect
	 */
	public function welcome_redirect() {
		$redirect = get_transient( 'kr_toolkit_activation_redirect' );

		if ( $redirect ) {
			delete_transient( 'kr_toolkit_activation_redirect' );
			wp_safe_redirect( admin_url( 'admin.php?page=kr-toolkit-dashboard' ) );
			exit;
		}
	}

	/**
	 * Admin notices
	 */
	public function admin_notices() {
		// Check if Elementor is installed
		if ( ! kr_toolkit_is_plugin_installed( 'elementor' ) ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					printf(
						/* translators: %s: plugin name */
						esc_html__( '%s is recommended for best experience with KR Theme.', 'kr-toolkit' ),
						'<strong>Elementor</strong>'
					);
					?>
					<a href="<?php echo esc_url( kr_toolkit_get_plugin_install_url( 'elementor' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Install Elementor', 'kr-toolkit' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Render dashboard page
	 */
	public function render_dashboard_page() {
		include KR_TOOLKIT_PATH . 'admin/views/dashboard.php';
	}

	/**
	 * Render demos page
	 */
	public function render_demos_page() {
		include KR_TOOLKIT_PATH . 'admin/views/demos.php';
	}

	/**
	 * Render child theme page
	 */
	public function render_child_theme_page() {
		include KR_TOOLKIT_PATH . 'admin/views/child-theme.php';
	}

	/**
	 * Render system info page
	 */
	public function render_system_info_page() {
		include KR_TOOLKIT_PATH . 'admin/views/system-info.php';
	}

	/**
	 * Render license page
	 */
	public function render_license_page() {
		include KR_TOOLKIT_PATH . 'admin/views/license.php';
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		include KR_TOOLKIT_PATH . 'admin/views/settings.php';
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'kr_toolkit_settings', 'kr_auto_update_plugin' );
		register_setting( 'kr_toolkit_settings', 'kr_auto_update_theme' );
	}
}
