<?php
/**
 * System Requirements Class
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_System_Requirements Class
 */
class KR_System_Requirements {

	/**
	 * Minimum requirements
	 *
	 * @var array
	 */
	private $requirements = array(
		'php_version'       => '7.4',
		'wp_version'        => '5.8',
		'memory_limit'      => 128, // MB
		'max_execution_time' => 300, // seconds
		'upload_max_size'   => 64,  // MB
	);

	/**
	 * Get system info
	 *
	 * @return array
	 */
	public function get_system_info() {
		global $wpdb;

		return array(
			'php_version'          => PHP_VERSION,
			'wp_version'           => get_bloginfo( 'version' ),
			'memory_limit'         => ini_get( 'memory_limit' ),
			'max_execution_time'   => ini_get( 'max_execution_time' ),
			'upload_max_filesize'  => ini_get( 'upload_max_filesize' ),
			'post_max_size'        => ini_get( 'post_max_size' ),
			'server_software'      => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
			'mysql_version'        => $wpdb->db_version(),
			'curl_version'         => function_exists( 'curl_version' ) ? curl_version()['version'] : 'N/A',
			'allow_url_fopen'      => ini_get( 'allow_url_fopen' ) ? 'Yes' : 'No',
			'zip_archive'          => class_exists( 'ZipArchive' ) ? 'Yes' : 'No',
			'dom_document'         => class_exists( 'DOMDocument' ) ? 'Yes' : 'No',
			'xmlreader'            => class_exists( 'XMLReader' ) ? 'Yes' : 'No',
			'wp_debug'             => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Yes' : 'No',
			'wp_debug_log'         => defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? 'Yes' : 'No',
			'multisite'            => is_multisite() ? 'Yes' : 'No',
			'theme'                => wp_get_theme()->get( 'Name' ) . ' ' . wp_get_theme()->get( 'Version' ),
			'active_plugins'       => count( get_option( 'active_plugins' ) ),
		);
	}

	/**
	 * Check PHP version
	 *
	 * @return bool
	 */
	public function check_php_version() {
		return version_compare( PHP_VERSION, $this->requirements['php_version'], '>=' );
	}

	/**
	 * Check WordPress version
	 *
	 * @return bool
	 */
	public function check_wp_version() {
		return version_compare( get_bloginfo( 'version' ), $this->requirements['wp_version'], '>=' );
	}

	/**
	 * Check memory limit
	 *
	 * @return bool
	 */
	public function check_memory_limit() {
		$memory_limit = ini_get( 'memory_limit' );
		
		if ( $memory_limit === '-1' ) {
			return true; // Unlimited
		}

		$memory_limit_mb = $this->convert_to_mb( $memory_limit );
		return $memory_limit_mb >= $this->requirements['memory_limit'];
	}

	/**
	 * Check max execution time
	 *
	 * @return bool
	 */
	public function check_execution_time() {
		$max_execution_time = ini_get( 'max_execution_time' );
		
		if ( $max_execution_time === '0' ) {
			return true; // Unlimited
		}

		return (int) $max_execution_time >= $this->requirements['max_execution_time'];
	}

	/**
	 * Check upload max size
	 *
	 * @return bool
	 */
	public function check_upload_size() {
		$upload_max = ini_get( 'upload_max_filesize' );
		$upload_max_mb = $this->convert_to_mb( $upload_max );
		return $upload_max_mb >= $this->requirements['upload_max_size'];
	}

	/**
	 * Check if ZipArchive is available
	 *
	 * @return bool
	 */
	public function check_zip_archive() {
		return class_exists( 'ZipArchive' );
	}

	/**
	 * Check if DOMDocument is available
	 *
	 * @return bool
	 */
	public function check_dom_document() {
		return class_exists( 'DOMDocument' );
	}

	/**
	 * Check if XMLReader is available
	 *
	 * @return bool
	 */
	public function check_xml_reader() {
		return class_exists( 'XMLReader' );
	}

	/**
	 * Check if cURL is available
	 *
	 * @return bool
	 */
	public function check_curl() {
		return function_exists( 'curl_version' );
	}

	/**
	 * Check all requirements
	 *
	 * @return array
	 */
	public function check_all_requirements() {
		return array(
			'php_version'      => $this->check_php_version(),
			'wp_version'       => $this->check_wp_version(),
			'memory_limit'     => $this->check_memory_limit(),
			'execution_time'   => $this->check_execution_time(),
			'upload_size'      => $this->check_upload_size(),
			'zip_archive'      => $this->check_zip_archive(),
			'dom_document'     => $this->check_dom_document(),
			'xml_reader'       => $this->check_xml_reader(),
			'curl'             => $this->check_curl(),
		);
	}

	/**
	 * Check if all requirements are met
	 *
	 * @return bool
	 */
	public function all_requirements_met() {
		$checks = $this->check_all_requirements();
		return ! in_array( false, $checks, true );
	}

	/**
	 * Get requirements status with details
	 *
	 * @return array
	 */
	public function get_requirements_status() {
		return array(
			array(
				'label'    => 'PHP Version',
				'required' => $this->requirements['php_version'] . '+',
				'current'  => PHP_VERSION,
				'status'   => $this->check_php_version(),
			),
			array(
				'label'    => 'WordPress Version',
				'required' => $this->requirements['wp_version'] . '+',
				'current'  => get_bloginfo( 'version' ),
				'status'   => $this->check_wp_version(),
			),
			array(
				'label'    => 'PHP Memory Limit',
				'required' => $this->requirements['memory_limit'] . 'M',
				'current'  => ini_get( 'memory_limit' ),
				'status'   => $this->check_memory_limit(),
			),
			array(
				'label'    => 'Max Execution Time',
				'required' => $this->requirements['max_execution_time'] . 's',
				'current'  => ini_get( 'max_execution_time' ) . 's',
				'status'   => $this->check_execution_time(),
			),
			array(
				'label'    => 'Upload Max Size',
				'required' => $this->requirements['upload_max_size'] . 'M',
				'current'  => ini_get( 'upload_max_filesize' ),
				'status'   => $this->check_upload_size(),
			),
			array(
				'label'    => 'ZipArchive',
				'required' => 'Required',
				'current'  => $this->check_zip_archive() ? 'Available' : 'Not Available',
				'status'   => $this->check_zip_archive(),
			),
			array(
				'label'    => 'DOMDocument',
				'required' => 'Required',
				'current'  => $this->check_dom_document() ? 'Available' : 'Not Available',
				'status'   => $this->check_dom_document(),
			),
			array(
				'label'    => 'XMLReader',
				'required' => 'Required',
				'current'  => $this->check_xml_reader() ? 'Available' : 'Not Available',
				'status'   => $this->check_xml_reader(),
			),
			array(
				'label'    => 'cURL',
				'required' => 'Recommended',
				'current'  => $this->check_curl() ? 'Available' : 'Not Available',
				'status'   => $this->check_curl(),
			),
		);
	}

	/**
	 * Convert value to megabytes
	 *
	 * @param string $value Value with unit (e.g., '128M', '1G').
	 * @return int
	 */
	private function convert_to_mb( $value ) {
		$value = trim( $value );
		$unit = strtolower( substr( $value, -1 ) );
		$number = (int) substr( $value, 0, -1 );

		switch ( $unit ) {
			case 'g':
				return $number * 1024;
			case 'm':
				return $number;
			case 'k':
				return $number / 1024;
			default:
				return (int) $value / 1024 / 1024;
		}
	}

	/**
	 * Get active plugins info
	 *
	 * @return array
	 */
	public function get_active_plugins() {
		$active_plugins = get_option( 'active_plugins' );
		$plugins_info = array();

		foreach ( $active_plugins as $plugin ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$plugins_info[] = array(
				'name'    => $plugin_data['Name'],
				'version' => $plugin_data['Version'],
				'author'  => $plugin_data['Author'],
			);
		}

		return $plugins_info;
	}

	/**
	 * Export system info as text
	 *
	 * @return string
	 */
	public function export_system_info() {
		$info = $this->get_system_info();
		$output = "=== KR Theme System Information ===\n\n";

		$output .= "-- WordPress Environment --\n";
		$output .= "WordPress Version: " . $info['wp_version'] . "\n";
		$output .= "Multisite: " . $info['multisite'] . "\n";
		$output .= "Active Theme: " . $info['theme'] . "\n";
		$output .= "Active Plugins: " . $info['active_plugins'] . "\n\n";

		$output .= "-- Server Environment --\n";
		$output .= "Server Software: " . $info['server_software'] . "\n";
		$output .= "PHP Version: " . $info['php_version'] . "\n";
		$output .= "MySQL Version: " . $info['mysql_version'] . "\n\n";

		$output .= "-- PHP Configuration --\n";
		$output .= "Memory Limit: " . $info['memory_limit'] . "\n";
		$output .= "Max Execution Time: " . $info['max_execution_time'] . "\n";
		$output .= "Upload Max Filesize: " . $info['upload_max_filesize'] . "\n";
		$output .= "Post Max Size: " . $info['post_max_size'] . "\n";
		$output .= "cURL Version: " . $info['curl_version'] . "\n";
		$output .= "Allow URL fopen: " . $info['allow_url_fopen'] . "\n\n";

		$output .= "-- PHP Extensions --\n";
		$output .= "ZipArchive: " . $info['zip_archive'] . "\n";
		$output .= "DOMDocument: " . $info['dom_document'] . "\n";
		$output .= "XMLReader: " . $info['xmlreader'] . "\n\n";

		$output .= "-- Debug Settings --\n";
		$output .= "WP_DEBUG: " . $info['wp_debug'] . "\n";
		$output .= "WP_DEBUG_LOG: " . $info['wp_debug_log'] . "\n\n";

		return $output;
	}
}
