<?php
/**
 * Main Plugin Class
 *
 * @package KR_Toolkit
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_Toolkit_Main Class
 */
class KR_Toolkit_Main {

	/**
	 * Instance
	 *
	 * @var KR_Toolkit_Main
	 */
	private static $instance = null;

	/**
	 * Demo importer instance
	 *
	 * @var KR_Demo_Importer
	 */
	public $demo_importer;

	/**
	 * Child theme manager instance
	 *
	 * @var KR_Child_Theme_Manager
	 */
	public $child_theme_manager;

	/**
	 * License manager instance
	 *
	 * @var KR_License_Manager
	 */
	public $license_manager;

	/**
	 * System requirements instance
	 *
	 * @var KR_System_Requirements
	 */
	public $system_requirements;

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
		$this->init_components();
		$this->init_hooks();
	}

	/**
	 * Initialize components
	 */
	private function init_components() {
		$this->demo_importer        = new KR_Demo_Importer();
		$this->child_theme_manager  = new KR_Child_Theme_Manager();
		$this->license_manager      = new KR_License_Manager();
		$this->system_requirements  = new KR_System_Requirements();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// AJAX handlers
		add_action( 'wp_ajax_kr_import_demo', array( $this, 'ajax_import_demo' ) );
		add_action( 'wp_ajax_kr_create_child_theme', array( $this, 'ajax_create_child_theme' ) );
		add_action( 'wp_ajax_kr_activate_license', array( $this, 'ajax_activate_license' ) );
		add_action( 'wp_ajax_kr_check_requirements', array( $this, 'ajax_check_requirements' ) );
	}

	/**
	 * AJAX: Import demo
	 */
	public function ajax_import_demo() {
		check_ajax_referer( 'kr_toolkit_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'kr-toolkit' ) ) );
		}

		$demo_slug = isset( $_POST['demo_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['demo_slug'] ) ) : '';

		if ( empty( $demo_slug ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid demo slug.', 'kr-toolkit' ) ) );
		}

		$result = $this->demo_importer->import_demo( $demo_slug );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Demo imported successfully!', 'kr-toolkit' ) ) );
	}

	/**
	 * AJAX: Create child theme
	 */
	public function ajax_create_child_theme() {
		check_ajax_referer( 'kr_toolkit_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'kr-toolkit' ) ) );
		}

		$theme_name = isset( $_POST['theme_name'] ) ? sanitize_text_field( wp_unslash( $_POST['theme_name'] ) ) : '';

		$result = $this->child_theme_manager->create_child_theme( $theme_name );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Child theme created successfully!', 'kr-toolkit' ) ) );
	}

	/**
	 * AJAX: Activate license
	 */
	public function ajax_activate_license() {
		check_ajax_referer( 'kr_toolkit_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'kr-toolkit' ) ) );
		}

		$license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

		$result = $this->license_manager->activate_license( $license_key );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'License activated successfully!', 'kr-toolkit' ) ) );
	}

	/**
	 * AJAX: Check system requirements
	 */
	public function ajax_check_requirements() {
		check_ajax_referer( 'kr_toolkit_nonce', 'nonce' );

		$demo_slug = isset( $_POST['demo_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['demo_slug'] ) ) : '';

		$result = $this->system_requirements->check_demo_requirements( $demo_slug );

		wp_send_json_success( $result );
	}
}
