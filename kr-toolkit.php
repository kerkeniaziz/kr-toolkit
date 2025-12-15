<?php
/**
 * Plugin Name: KR Toolkit
 * Plugin URI: https://www.krtheme.com
 * Description: Essential companion plugin for KR Theme. Features one-click demo import, child theme manager, license management, and system requirements checker. Unlock the full potential of KR Theme with this powerful toolkit.
 * Version: 1.3.6
 * Author: KR Theme
 * Author URI: https://www.krtheme.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kr-toolkit
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * 
 * Copyright: 2025 KR Theme
 *
 * @package KR_Toolkit
 * @since 1.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Constants
if ( ! defined( 'KR_TOOLKIT_VERSION' ) ) {
	define( 'KR_TOOLKIT_VERSION', '1.3.6' );
}
if ( ! defined( 'KR_TOOLKIT_FILE' ) ) {
	define( 'KR_TOOLKIT_FILE', __FILE__ );
}
if ( ! defined( 'KR_TOOLKIT_DIR' ) ) {
	define( 'KR_TOOLKIT_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'KR_TOOLKIT_PATH' ) ) {
	define( 'KR_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'KR_TOOLKIT_URL' ) ) {
	define( 'KR_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'KR_TOOLKIT_BASENAME' ) ) {
	define( 'KR_TOOLKIT_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Load Main Plugin Class
 */
require_once KR_TOOLKIT_DIR . 'inc/classes/class-kr-toolkit.php';

/**
 * Initialize Plugin - Following Astra Sites singleton pattern
 */
function kr_toolkit_init() {
	return KR_Toolkit::instance();
}

/**
 * Start the plugin
 */
add_action( 'plugins_loaded', 'kr_toolkit_init' );

/**
 * Initialize Auto-Updates
 */
add_action( 'plugins_loaded', 'kr_toolkit_init_auto_updates' );

function kr_toolkit_init_auto_updates() {
	// Check if auto-updates are enabled
	$plugin_enabled = get_option( 'kr_auto_update_plugin', '1' );
	$theme_enabled = get_option( 'kr_auto_update_theme', '1' );
	
	// Load Plugin Update Checker for plugin if enabled
	if ( $plugin_enabled === '1' && file_exists( KR_TOOLKIT_DIR . 'inc/libraries/plugin-update-checker/plugin-update-checker.php' ) ) {
		require KR_TOOLKIT_DIR . 'inc/libraries/plugin-update-checker/plugin-update-checker.php';
		
		$pluginUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/kerkeniaziz/kr-toolkit/',
			__FILE__,
			'kr-toolkit'
		);
		$pluginUpdateChecker->setBranch( 'main' );
	}
	
	// Load Theme Update Checker if enabled
	if ( $theme_enabled === '1' && file_exists( KR_TOOLKIT_DIR . 'inc/libraries/plugin-update-checker/plugin-update-checker.php' ) ) {
		require_once KR_TOOLKIT_DIR . 'inc/libraries/plugin-update-checker/plugin-update-checker.php';
		
		$themeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/kerkeniaziz/kr-theme/',
			get_template_directory() . '/style.css',
			'kr-theme'
		);
		$themeUpdateChecker->setBranch( 'main' );
	}
}

/**
 * Activation Hook
 */
register_activation_hook( __FILE__, 'kr_toolkit_activate' );

function kr_toolkit_activate() {
	// Set transient for welcome redirect - 2 minutes timeout
	set_transient( 'kr_toolkit_activation_redirect', true, 120 );
	
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
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'kr_toolkit_deactivate' );

function kr_toolkit_deactivate() {
	// Clear scheduled hooks
	wp_clear_scheduled_hook( 'kr_toolkit_daily_tasks' );
	
	// Flush rewrite rules
	flush_rewrite_rules();
}

/**
 * Global function to access plugin instance
 */
function kr_toolkit() {
	return KR_Toolkit::instance();
}
