<?php
/**
 * KR Toolkit - Header & Footer CPTs
 *
 * Registers custom post types for headers and footers
 *
 * @package KR_Toolkit
 * @since 1.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KR_Header_Footer_CPT {
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_cpts' ) );
	}

	public static function register_cpts() {
		// Header CPT
		register_post_type( 'kr_header', array(
			'labels' => array(
				'name'               => __( 'Headers', 'kr-toolkit' ),
				'singular_name'      => __( 'Header', 'kr-toolkit' ),
				'add_new'            => __( 'Add New', 'kr-toolkit' ),
				'add_new_item'       => __( 'Add New Header', 'kr-toolkit' ),
				'edit_item'          => __( 'Edit Header', 'kr-toolkit' ),
				'new_item'           => __( 'New Header', 'kr-toolkit' ),
				'view_item'          => __( 'View Header', 'kr-toolkit' ),
				'view_items'         => __( 'View Headers', 'kr-toolkit' ),
				'not_found'          => __( 'No headers found', 'kr-toolkit' ),
				'not_found_in_trash' => __( 'No headers found in Trash', 'kr-toolkit' ),
				'all_items'          => __( 'All Headers', 'kr-toolkit' ),
				'menu_name'          => __( 'Header Builder', 'kr-toolkit' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'supports'            => array( 'title', 'editor', 'elementor', 'revisions' ),
			'capability_type'     => 'post',
			'menu_icon'           => 'dashicons-arrow-up-alt',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'show_in_rest'        => true,
		) );

		// Footer CPT
		register_post_type( 'kr_footer', array(
			'labels' => array(
				'name'               => __( 'Footers', 'kr-toolkit' ),
				'singular_name'      => __( 'Footer', 'kr-toolkit' ),
				'add_new'            => __( 'Add New', 'kr-toolkit' ),
				'add_new_item'       => __( 'Add New Footer', 'kr-toolkit' ),
				'edit_item'          => __( 'Edit Footer', 'kr-toolkit' ),
				'new_item'           => __( 'New Footer', 'kr-toolkit' ),
				'view_item'          => __( 'View Footer', 'kr-toolkit' ),
				'view_items'         => __( 'View Footers', 'kr-toolkit' ),
				'not_found'          => __( 'No footers found', 'kr-toolkit' ),
				'not_found_in_trash' => __( 'No footers found in Trash', 'kr-toolkit' ),
				'all_items'          => __( 'All Footers', 'kr-toolkit' ),
				'menu_name'          => __( 'Footer Builder', 'kr-toolkit' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'supports'            => array( 'title', 'editor', 'elementor', 'revisions' ),
			'capability_type'     => 'post',
			'menu_icon'           => 'dashicons-arrow-down-alt',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'show_in_rest'        => true,
		) );
	}
}

// Initialize on plugins_loaded
add_action( 'plugins_loaded', array( 'KR_Header_Footer_CPT', 'init' ) );
