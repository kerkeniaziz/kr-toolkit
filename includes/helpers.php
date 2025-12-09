<?php
/**
 * Helper Functions
 *
 * @package KR_Toolkit
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if license is active
 *
 * @return bool
 */
function kr_toolkit_is_license_active() {
	$settings = get_option( 'kr_toolkit_settings', array() );
	return isset( $settings['license_status'] ) && 'active' === $settings['license_status'];
}

/**
 * Get plugin setting
 *
 * @param string $key     Setting key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function kr_toolkit_get_setting( $key, $default = '' ) {
	$settings = get_option( 'kr_toolkit_settings', array() );
	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Update plugin setting
 *
 * @param string $key   Setting key.
 * @param mixed  $value Setting value.
 * @return bool
 */
function kr_toolkit_update_setting( $key, $value ) {
	$settings = get_option( 'kr_toolkit_settings', array() );
	$settings[ $key ] = $value;
	return update_option( 'kr_toolkit_settings', $settings );
}

/**
 * Get demo importer instance
 *
 * @return KR_Demo_Importer
 */
function kr_toolkit_demo_importer() {
	return new KR_Demo_Importer();
}

/**
 * Get child theme manager instance
 *
 * @return KR_Child_Theme_Manager
 */
function kr_toolkit_child_theme_manager() {
	return new KR_Child_Theme_Manager();
}

/**
 * Get license manager instance
 *
 * @return KR_License_Manager
 */
function kr_toolkit_license_manager() {
	return new KR_License_Manager();
}

/**
 * Get system requirements instance
 *
 * @return KR_System_Requirements
 */
function kr_toolkit_system_requirements() {
	return new KR_System_Requirements();
}

/**
 * Log message
 *
 * @param string $message Message to log.
 * @param string $level   Log level (info, warning, error).
 */
function kr_toolkit_log( $message, $level = 'info' ) {
	if ( ! WP_DEBUG || ! WP_DEBUG_LOG ) {
		return;
	}

	error_log( sprintf( '[KR Toolkit %s] %s', strtoupper( $level ), $message ) );
}

/**
 * Get admin page URL
 *
 * @param string $page Page slug.
 * @return string
 */
function kr_toolkit_admin_url( $page = 'dashboard' ) {
	return admin_url( 'admin.php?page=kr-toolkit-' . $page );
}

/**
 * Format bytes to human readable
 *
 * @param int $bytes Bytes.
 * @return string
 */
function kr_toolkit_format_bytes( $bytes ) {
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
	$bytes = max( $bytes, 0 );
	$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
	$pow = min( $pow, count( $units ) - 1 );
	$bytes /= pow( 1024, $pow );
	
	return round( $bytes, 2 ) . ' ' . $units[ $pow ];
}

/**
 * Check if plugin is installed
 *
 * @param string $slug Plugin slug.
 * @return bool
 */
function kr_toolkit_is_plugin_installed( $slug ) {
	$installed_plugins = get_plugins();
	return isset( $installed_plugins[ $slug . '/' . $slug . '.php' ] );
}

/**
 * Get plugin install URL
 *
 * @param string $slug Plugin slug.
 * @return string
 */
function kr_toolkit_get_plugin_install_url( $slug ) {
	return wp_nonce_url(
		self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ),
		'install-plugin_' . $slug
	);
}

/**
 * Get plugin activation URL
 *
 * @param string $slug Plugin slug.
 * @return string
 */
function kr_toolkit_get_plugin_activation_url( $slug ) {
	$plugin = $slug . '/' . $slug . '.php';
	return wp_nonce_url(
		self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin ),
		'activate-plugin_' . $plugin
	);
}
