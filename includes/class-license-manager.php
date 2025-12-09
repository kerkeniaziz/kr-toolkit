<?php
/**
 * License Manager Class
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_License_Manager Class
 */
class KR_License_Manager {

	/**
	 * License API URL
	 *
	 * @var string
	 */
	private $api_url = 'https://krtheme.com/api/';

	/**
	 * License option key
	 *
	 * @var string
	 */
	private $license_key = 'kr_theme_license';

	/**
	 * Activate license
	 *
	 * @param string $license_key License key.
	 * @return bool|WP_Error
	 */
	public function activate_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return new WP_Error( 'empty_key', __( 'Please enter a license key.', 'kr-toolkit' ) );
		}

		// Validate format
		if ( ! $this->validate_license_format( $license_key ) ) {
			return new WP_Error( 'invalid_format', __( 'Invalid license key format.', 'kr-toolkit' ) );
		}

		// Check with API
		$response = $this->call_api( 'activate', array( 'license_key' => $license_key ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Save license data
		$license_data = array(
			'key'           => $license_key,
			'status'        => 'active',
			'activated_at'  => current_time( 'mysql' ),
			'expires_at'    => isset( $response['expires_at'] ) ? $response['expires_at'] : '',
			'support_until' => isset( $response['support_until'] ) ? $response['support_until'] : '',
			'license_type'  => isset( $response['license_type'] ) ? $response['license_type'] : 'regular',
		);

		update_option( $this->license_key, $license_data );

		return true;
	}

	/**
	 * Deactivate license
	 *
	 * @return bool|WP_Error
	 */
	public function deactivate_license() {
		$license_data = $this->get_license_data();

		if ( ! $license_data || empty( $license_data['key'] ) ) {
			return new WP_Error( 'no_license', __( 'No active license found.', 'kr-toolkit' ) );
		}

		// Call API
		$response = $this->call_api( 'deactivate', array( 'license_key' => $license_data['key'] ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Remove license data
		delete_option( $this->license_key );

		return true;
	}

	/**
	 * Check license status
	 *
	 * @return bool|WP_Error
	 */
	public function check_license() {
		$license_data = $this->get_license_data();

		if ( ! $license_data || empty( $license_data['key'] ) ) {
			return new WP_Error( 'no_license', __( 'No license found.', 'kr-toolkit' ) );
		}

		// Call API
		$response = $this->call_api( 'check', array( 'license_key' => $license_data['key'] ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Update license data
		if ( isset( $response['status'] ) ) {
			$license_data['status'] = $response['status'];
			$license_data['last_checked'] = current_time( 'mysql' );
			update_option( $this->license_key, $license_data );
		}

		return $response;
	}

	/**
	 * Get license data
	 *
	 * @return array|false
	 */
	public function get_license_data() {
		return get_option( $this->license_key, false );
	}

	/**
	 * Get license key
	 *
	 * @return string|false
	 */
	public function get_license_key() {
		$license_data = $this->get_license_data();
		if ( ! $license_data || empty( $license_data['key'] ) ) {
			return false;
		}
		return $license_data['key'];
	}

	/**
	 * Check if license is active
	 *
	 * @return bool
	 */
	public function is_license_active() {
		$license_data = $this->get_license_data();

		if ( ! $license_data || empty( $license_data['status'] ) ) {
			return false;
		}

		return $license_data['status'] === 'active';
	}

	/**
	 * Check if license has support
	 *
	 * @return bool
	 */
	public function has_support() {
		$license_data = $this->get_license_data();

		if ( ! $license_data || empty( $license_data['support_until'] ) ) {
			return false;
		}

		$support_until = strtotime( $license_data['support_until'] );
		return $support_until > time();
	}

	/**
	 * Get license expiry date
	 *
	 * @return string|false
	 */
	public function get_expiry_date() {
		$license_data = $this->get_license_data();

		if ( ! $license_data || empty( $license_data['expires_at'] ) ) {
			return false;
		}

		return $license_data['expires_at'];
	}

	/**
	 * Get days until expiry
	 *
	 * @return int|false
	 */
	public function get_days_until_expiry() {
		$expiry_date = $this->get_expiry_date();

		if ( ! $expiry_date ) {
			return false;
		}

		$expiry_timestamp = strtotime( $expiry_date );
		$now = time();
		$diff = $expiry_timestamp - $now;

		return floor( $diff / DAY_IN_SECONDS );
	}

	/**
	 * Validate license key format
	 *
	 * @param string $license_key License key.
	 * @return bool
	 */
	private function validate_license_format( $license_key ) {
		// Format: XXXX-XXXX-XXXX-XXXX (letters and numbers)
		return (bool) preg_match( '/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $license_key );
	}

	/**
	 * Call license API
	 *
	 * @param string $action API action.
	 * @param array  $args API arguments.
	 * @return array|WP_Error
	 */
	private function call_api( $action, $args = array() ) {
		$api_endpoint = $this->api_url . 'license/' . $action;

		// Add site URL
		$args['site_url'] = home_url();

		// Make request
		$response = wp_remote_post( $api_endpoint, array(
			'timeout' => 30,
			'body'    => $args,
		) );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'api_error', __( 'Could not connect to license server.', 'kr-toolkit' ) );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$data = json_decode( $response_body, true );

		if ( $response_code !== 200 ) {
			$message = isset( $data['message'] ) ? $data['message'] : __( 'License validation failed.', 'kr-toolkit' );
			return new WP_Error( 'api_error', $message );
		}

		return $data;
	}

	/**
	 * Get license status badge HTML
	 *
	 * @return string
	 */
	public function get_status_badge() {
		if ( ! $this->is_license_active() ) {
			return '<span class="kr-badge kr-badge-inactive">' . __( 'Inactive', 'kr-toolkit' ) . '</span>';
		}

		$days_left = $this->get_days_until_expiry();

		if ( $days_left === false ) {
			return '<span class="kr-badge kr-badge-lifetime">' . __( 'Lifetime', 'kr-toolkit' ) . '</span>';
		}

		if ( $days_left < 30 ) {
			return '<span class="kr-badge kr-badge-expiring">' . sprintf( __( 'Expires in %d days', 'kr-toolkit' ), $days_left ) . '</span>';
		}

		return '<span class="kr-badge kr-badge-active">' . __( 'Active', 'kr-toolkit' ) . '</span>';
	}

	/**
	 * Schedule license check cron
	 */
	public function schedule_license_check() {
		if ( ! wp_next_scheduled( 'kr_license_check' ) ) {
			wp_schedule_event( time(), 'daily', 'kr_license_check' );
		}
	}

	/**
	 * Unschedule license check cron
	 */
	public function unschedule_license_check() {
		wp_clear_scheduled_hook( 'kr_license_check' );
	}
}
