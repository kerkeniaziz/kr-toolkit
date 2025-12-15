<?php
/**
 * KR Base Widget Class
 *
 * @since 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KR_Base_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'kr-' . strtolower( preg_replace( '/([A-Z])/', '-$1', substr( get_class( $this ), 3 ) ) );
	}

	/**
	 * Get widget category.
	 */
	public function get_categories() {
		return array( 'krtheme' );
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return array( 'kr', 'theme' );
	}

	/**
	 * Get script dependencies
	 */
	public function get_script_depends() {
		return array();
	}

	/**
	 * Get style dependencies
	 */
	public function get_style_depends() {
		return array();
	}
}
