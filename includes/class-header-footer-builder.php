<?php
/**
 * Header Footer Builder
 *
 * @package KR_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_Header_Footer_Builder Class
 */
class KR_Header_Footer_Builder {

	/**
	 * Instance
	 *
	 * @var KR_Header_Footer_Builder
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
		add_action( 'kr_header', [ $this, 'display_header' ] );
		add_action( 'kr_footer', [ $this, 'display_footer' ] );
	}

	/**
	 * Display Header
	 */
	public function display_header() {
		$header_id = $this->get_header_id();

		if ( $header_id ) {
			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_id );
		}
	}

	/**
	 * Display Footer
	 */
	public function display_footer() {
		$footer_id = $this->get_footer_id();

		if ( $footer_id ) {
			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id );
		}
	}

	/**
	 * Get Header ID
	 */
	private function get_header_id() {
		// Get from page meta
		if ( is_page() || is_single() ) {
			$header_id = get_post_meta( get_the_ID(), 'kr_header', true );
			if ( ! empty( $header_id ) ) {
				return $header_id;
			}
		}

		// Get from theme options (key: kr_header)
		$options = get_option( 'kr_theme_options' );
		if ( ! empty( $options['kr_header'] ) ) {
			return $options['kr_header'];
		}

		return false;
	}

	/**
	 * Get Footer ID
	 */
	private function get_footer_id() {
		// Get from page meta
		if ( is_page() || is_single() ) {
			$footer_id = get_post_meta( get_the_ID(), 'kr_footer', true );
			if ( ! empty( $footer_id ) ) {
				return $footer_id;
			}
		}

		// Get from theme options (key: kr_footer)
		$options = get_option( 'kr_theme_options' );
		if ( ! empty( $options['kr_footer'] ) ) {
			return $options['kr_footer'];
		}

		return false;
	}
}

KR_Header_Footer_Builder::instance();
