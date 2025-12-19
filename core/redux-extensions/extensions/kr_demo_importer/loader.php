<?php
/**
 * KR Demo Importer Extension Loader
 *
 * @package KR Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the main extension class
require_once plugin_dir_path( __FILE__ ) . 'extension_kr_demo_importer.php';

// Hook the extension initialization
if ( function_exists( 'add_action' ) ) {
	add_action( 'redux/register', 'kr_demo_importer_register' );
}

/**
 * Register demo importer extension with Redux Framework
 *
 * @param ReduxFramework $redux Redux instance
 * @return void
 */
function kr_demo_importer_register( $redux ) {
	if ( class_exists( 'ReduxFramework' ) && ! empty( $redux->args['opt_name'] ) ) {
		if ( $redux->args['opt_name'] === 'kr_theme_options' ) {
			new ReduxFramework_extension_kr_demo_importer( $redux );
		}
	}
}
