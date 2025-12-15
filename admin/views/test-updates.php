<?php
/**
 * Test Updates Page
 * 
 * Tests for new versions from GitHub and allows manual checking
 *
 * @package KR_Toolkit
 * @since 1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current versions
$plugin_version = KR_TOOLKIT_VERSION;
$theme_version = wp_get_theme()->get( 'Version' );

// Get latest versions from GitHub
$plugin_latest = get_transient( 'kr_toolkit_latest_version' );
$theme_latest = get_transient( 'kr_theme_latest_version' );

// Handle manual check for updates
$check_message = '';
$check_type = '';

if ( isset( $_POST['kr_check_updates'] ) && check_admin_referer( 'kr_test_updates' ) ) {
	$check_type = 'checking';
	$check_message = __( 'Checking for updates from GitHub...', 'kr-toolkit' );
	
	// Check plugin updates
	$plugin_response = wp_remote_get( 'https://api.github.com/repos/kerkeniaziz/kr-toolkit/releases/latest', array(
		'timeout' => 10,
		'headers' => array( 'Accept' => 'application/vnd.github.v3+json' ),
	) );
	
	if ( ! is_wp_error( $plugin_response ) ) {
		$plugin_data = json_decode( wp_remote_retrieve_body( $plugin_response ), true );
		if ( isset( $plugin_data['tag_name'] ) ) {
			$plugin_latest = str_replace( 'v', '', $plugin_data['tag_name'] );
			set_transient( 'kr_toolkit_latest_version', $plugin_latest, 12 * HOUR_IN_SECONDS );
		}
	}
	
	// Check theme updates
	$theme_response = wp_remote_get( 'https://api.github.com/repos/kerkeniaziz/kr-theme/releases/latest', array(
		'timeout' => 10,
		'headers' => array( 'Accept' => 'application/vnd.github.v3+json' ),
	) );
	
	if ( ! is_wp_error( $theme_response ) ) {
		$theme_data = json_decode( wp_remote_retrieve_body( $theme_response ), true );
		if ( isset( $theme_data['tag_name'] ) ) {
			$theme_latest = str_replace( 'v', '', $theme_data['tag_name'] );
			set_transient( 'kr_theme_latest_version', $theme_latest, 12 * HOUR_IN_SECONDS );
		}
	}
	
	$check_type = 'success';
	$check_message = __( 'Update check completed! Latest versions loaded.', 'kr-toolkit' );
}

// Determine update status
$plugin_has_update = ( $plugin_latest && version_compare( $plugin_version, $plugin_latest, '<' ) ) ? true : false;
$theme_has_update = ( $theme_latest && version_compare( $theme_version, $theme_latest, '<' ) ) ? true : false;
$has_updates = $plugin_has_update || $theme_has_update;

?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'Test Updates', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Check for Updates', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Test the update detection system for KR Theme and KR Toolkit. This page checks GitHub for the latest releases.', 'kr-toolkit' ); ?></p>
	</div>

	<!-- Test Updates Section -->
	<div class="kr-card">
		<div class="kr-test-updates">
			<h3><?php esc_html_e( '🧪 Test Update Detection', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Click the button below to manually check for new versions from GitHub. This simulates what happens when WordPress checks for updates.', 'kr-toolkit' ); ?></p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'kr_test_updates' ); ?>
				
				<div class="kr-test-updates-buttons">
					<button type="submit" name="kr_check_updates" class="button button-primary">
						<span class="dashicons dashicons-update"></span>
						<?php esc_html_e( 'Check for Updates', 'kr-toolkit' ); ?>
					</button>
					
					<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>" class="button button-secondary">
						<span class="dashicons dashicons-admin-tools"></span>
						<?php esc_html_e( 'Go to WordPress Updates', 'kr-toolkit' ); ?>
					</a>
				</div>
			</form>
			
			<?php if ( $check_message ) : ?>
				<div class="kr-test-updates-status show <?php echo esc_attr( $check_type ); ?>">
					<?php echo esc_html( $check_message ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Current Versions -->
	<div class="kr-toolkit-cards">
		<!-- Plugin Version Card -->
		<div class="kr-toolkit-card">
			<div class="kr-toolkit-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
				<span class="dashicons dashicons-plugins-checked"></span>
			</div>
			<h3><?php esc_html_e( 'KR Toolkit Plugin', 'kr-toolkit' ); ?></h3>
			<p style="margin-bottom: 10px;">
				<?php esc_html_e( 'Essential companion plugin for KR Theme.', 'kr-toolkit' ); ?>
			</p>
			
			<table class="kr-version-table" style="width: 100%; font-size: 13px; margin-bottom: 15px;">
				<tbody>
					<tr>
						<td style="padding: 8px 0; color: #64748b;"><strong><?php esc_html_e( 'Current:', 'kr-toolkit' ); ?></strong></td>
						<td style="padding: 8px 0; text-align: right;">
							<code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; color: #8b5cf6;">
								<?php echo esc_html( $plugin_version ); ?>
							</code>
						</td>
					</tr>
					<?php if ( $plugin_latest ) : ?>
						<tr>
							<td style="padding: 8px 0; color: #64748b;"><strong><?php esc_html_e( 'Latest:', 'kr-toolkit' ); ?></strong></td>
							<td style="padding: 8px 0; text-align: right;">
								<code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; color: #8b5cf6;">
									<?php echo esc_html( $plugin_latest ); ?>
								</code>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td colspan="2" style="padding: 8px 0; border-top: 1px solid rgba(139, 92, 246, 0.2); margin-top: 8px; padding-top: 8px;">
							<?php if ( $plugin_has_update ) : ?>
								<span style="color: #ef4444; font-weight: 600;">
									<?php esc_html_e( '⬆️ Update Available!', 'kr-toolkit' ); ?>
								</span>
							<?php else : ?>
								<span style="color: #22c55e; font-weight: 600;">
									<?php esc_html_e( '✓ Up to Date', 'kr-toolkit' ); ?>
								</span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=upload' ) ); ?>" class="button button-secondary" style="width: 100%; text-align: center;">
				<?php esc_html_e( 'View on GitHub', 'kr-toolkit' ); ?>
			</a>
		</div>

		<!-- Theme Version Card -->
		<div class="kr-toolkit-card">
			<div class="kr-toolkit-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
				<span class="dashicons dashicons-admin-appearance"></span>
			</div>
			<h3><?php esc_html_e( 'KR Theme', 'kr-toolkit' ); ?></h3>
			<p style="margin-bottom: 10px;">
				<?php esc_html_e( 'Ultra-lightweight, blazing-fast WordPress theme.', 'kr-toolkit' ); ?>
			</p>
			
			<table class="kr-version-table" style="width: 100%; font-size: 13px; margin-bottom: 15px;">
				<tbody>
					<tr>
						<td style="padding: 8px 0; color: #64748b;"><strong><?php esc_html_e( 'Current:', 'kr-toolkit' ); ?></strong></td>
						<td style="padding: 8px 0; text-align: right;">
							<code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; color: #f59e0b;">
								<?php echo esc_html( $theme_version ); ?>
							</code>
						</td>
					</tr>
					<?php if ( $theme_latest ) : ?>
						<tr>
							<td style="padding: 8px 0; color: #64748b;"><strong><?php esc_html_e( 'Latest:', 'kr-toolkit' ); ?></strong></td>
							<td style="padding: 8px 0; text-align: right;">
								<code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; color: #f59e0b;">
									<?php echo esc_html( $theme_latest ); ?>
								</code>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td colspan="2" style="padding: 8px 0; border-top: 1px solid rgba(245, 158, 11, 0.2); margin-top: 8px; padding-top: 8px;">
							<?php if ( $theme_has_update ) : ?>
								<span style="color: #ef4444; font-weight: 600;">
									<?php esc_html_e( '⬆️ Update Available!', 'kr-toolkit' ); ?>
								</span>
							<?php else : ?>
								<span style="color: #22c55e; font-weight: 600;">
									<?php esc_html_e( '✓ Up to Date', 'kr-toolkit' ); ?>
								</span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" class="button button-secondary" style="width: 100%; text-align: center;">
				<?php esc_html_e( 'View on GitHub', 'kr-toolkit' ); ?>
			</a>
		</div>
	</div>

	<!-- Update Status Summary -->
	<?php if ( $has_updates ) : ?>
		<div class="kr-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fef08a 100%); border-color: #fbbf24;">
			<h3 style="color: #d97706; margin-top: 0;">⬆️ <?php esc_html_e( 'Updates Available!', 'kr-toolkit' ); ?></h3>
			<p style="color: #92400e; margin: 0;">
				<?php 
				$updates_list = array();
				if ( $plugin_has_update ) {
					$updates_list[] = sprintf(
						esc_html__( 'KR Toolkit %s → %s', 'kr-toolkit' ),
						$plugin_version,
						$plugin_latest
					);
				}
				if ( $theme_has_update ) {
					$updates_list[] = sprintf(
						esc_html__( 'KR Theme %s → %s', 'kr-toolkit' ),
						$theme_version,
						$theme_latest
					);
				}
				echo esc_html( implode( ', ', $updates_list ) );
				?>
			</p>
			<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>" class="button" style="margin-top: 15px; background: #fbbf24; color: #000; border: none; font-weight: 600;">
				<?php esc_html_e( 'Go to WordPress Updates', 'kr-toolkit' ); ?>
			</a>
		</div>
	<?php else : ?>
		<div class="kr-card" style="background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%); border-color: #22c55e;">
			<h3 style="color: #16a34a; margin-top: 0;">✓ <?php esc_html_e( 'Everything is Up to Date!', 'kr-toolkit' ); ?></h3>
			<p style="color: #166534; margin: 0;">
				<?php esc_html_e( 'You are running the latest versions of KR Theme and KR Toolkit. New updates will be checked automatically.', 'kr-toolkit' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<!-- Information Section -->
	<div class="kr-card">
		<h3><?php esc_html_e( 'ℹ️ How Updates Work', 'kr-toolkit' ); ?></h3>
		<ul style="list-style: none; padding: 0; margin: 0;">
			<li style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; gap: 12px;">
				<span style="color: #2563eb; font-weight: 600; flex-shrink: 0;">1️⃣</span>
				<div>
					<strong style="color: #0f172a;"><?php esc_html_e( 'Automatic Detection', 'kr-toolkit' ); ?></strong><br>
					<span style="color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'WordPress automatically checks GitHub every 12 hours for new releases.', 'kr-toolkit' ); ?>
					</span>
				</div>
			</li>
			<li style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; gap: 12px;">
				<span style="color: #2563eb; font-weight: 600; flex-shrink: 0;">2️⃣</span>
				<div>
					<strong style="color: #0f172a;"><?php esc_html_e( 'Manual Testing', 'kr-toolkit' ); ?></strong><br>
					<span style="color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'Use the "Check for Updates" button above to manually trigger a check right now.', 'kr-toolkit' ); ?>
					</span>
				</div>
			</li>
			<li style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; gap: 12px;">
				<span style="color: #2563eb; font-weight: 600; flex-shrink: 0;">3️⃣</span>
				<div>
					<strong style="color: #0f172a;"><?php esc_html_e( 'Updates Dashboard', 'kr-toolkit' ); ?></strong><br>
					<span style="color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'When updates are available, they appear on your WordPress Updates page.', 'kr-toolkit' ); ?>
					</span>
				</div>
			</li>
			<li style="padding: 12px 0; display: flex; gap: 12px;">
				<span style="color: #2563eb; font-weight: 600; flex-shrink: 0;">4️⃣</span>
				<div>
					<strong style="color: #0f172a;"><?php esc_html_e( 'One-Click Install', 'kr-toolkit' ); ?></strong><br>
					<span style="color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'Click "Update" to download and install the latest version automatically.', 'kr-toolkit' ); ?>
					</span>
				</div>
			</li>
		</ul>
	</div>

	<!-- Footer -->
	<div class="kr-toolkit-footer-info">
		<p>
			<?php
			printf(
				esc_html__( '%1$s v%2$s - Developed by %3$s', 'kr-toolkit' ),
				'<strong>KR Toolkit</strong>',
				KR_TOOLKIT_VERSION,
				'<a href="https://www.krtheme.com/" target="_blank" rel="noopener">KR Theme</a>'
			);
			?>
			<br>
			<small><?php esc_html_e( 'Copyright © 2025 KR Theme. All rights reserved.', 'kr-toolkit' ); ?></small>
		</p>
	</div>
</div>
