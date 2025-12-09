<?php
/**
 * Settings Page
 *
 * @package KR_Toolkit
 * @since 1.2.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Save settings
if ( isset( $_POST['kr_save_settings'] ) && check_admin_referer( 'kr_toolkit_settings' ) ) {
	update_option( 'kr_auto_update_plugin', isset( $_POST['kr_auto_update_plugin'] ) ? '1' : '0' );
	update_option( 'kr_auto_update_theme', isset( $_POST['kr_auto_update_theme'] ) ? '1' : '0' );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully!', 'kr-toolkit' ) . '</p></div>';
}

$auto_update_plugin = get_option( 'kr_auto_update_plugin', '1' );
$auto_update_theme = get_option( 'kr_auto_update_theme', '1' );
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'KR Toolkit Settings', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Settings & Configuration', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Manage automatic updates and configure your theme and plugin settings.', 'kr-toolkit' ); ?></p>
	</div>
	
	<div class="kr-settings-container">
		<div class="kr-settings-content">
			<form method="post" action="">
				<?php wp_nonce_field( 'kr_toolkit_settings' ); ?>
				
				<div class="kr-card">
					<h2><?php esc_html_e( 'Automatic Updates', 'kr-toolkit' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Enable or disable automatic updates for KR Theme and KR Toolkit plugin. When enabled, new versions will be automatically checked and available in your WordPress Updates page.', 'kr-toolkit' ); ?>
					</p>
					
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<?php esc_html_e( 'Auto-Update Plugin', 'kr-toolkit' ); ?>
								</th>
								<td>
									<label>
										<input type="checkbox" name="kr_auto_update_plugin" value="1" <?php checked( $auto_update_plugin, '1' ); ?>>
										<?php esc_html_e( 'Enable automatic update checks for KR Toolkit plugin', 'kr-toolkit' ); ?>
									</label>
									<p class="description">
										<?php esc_html_e( 'When enabled, WordPress will check for new plugin versions from GitHub releases.', 'kr-toolkit' ); ?>
									</p>
								</td>
							</tr>
							
							<tr>
								<th scope="row">
									<?php esc_html_e( 'Auto-Update Theme', 'kr-toolkit' ); ?>
								</th>
								<td>
									<label>
										<input type="checkbox" name="kr_auto_update_theme" value="1" <?php checked( $auto_update_theme, '1' ); ?>>
										<?php esc_html_e( 'Enable automatic update checks for KR Theme', 'kr-toolkit' ); ?>
									</label>
									<p class="description">
										<?php esc_html_e( 'When enabled, WordPress will check for new theme versions from GitHub releases.', 'kr-toolkit' ); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="kr-card">
					<h2><?php esc_html_e( 'Update Information', 'kr-toolkit' ); ?></h2>
					<table class="form-table kr-info-table" role="presentation">
						<tbody>
							<tr>
								<th><?php esc_html_e( 'Current Plugin Version', 'kr-toolkit' ); ?></th>
								<td><code><?php echo esc_html( KR_TOOLKIT_VERSION ); ?></code></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Current Theme Version', 'kr-toolkit' ); ?></th>
								<td><code><?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></code></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Update Check Frequency', 'kr-toolkit' ); ?></th>
								<td><?php esc_html_e( 'Every 12 hours', 'kr-toolkit' ); ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Last Checked', 'kr-toolkit' ); ?></th>
								<td>
									<?php
									$last_check = get_site_transient( 'update_plugins' );
									if ( $last_check && isset( $last_check->last_checked ) ) {
										echo esc_html( human_time_diff( $last_check->last_checked ) ) . ' ' . esc_html__( 'ago', 'kr-toolkit' );
									} else {
										esc_html_e( 'Never', 'kr-toolkit' );
									}
									?>
								</td>
							</tr>
						</tbody>
					</table>
					
					<p class="kr-manual-check">
						<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>" class="button button-secondary">
							<span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
							<?php esc_html_e( 'Check for Updates Now', 'kr-toolkit' ); ?>
						</a>
					</p>
				</div>

				<p class="submit">
					<button type="submit" name="kr_save_settings" class="button button-primary button-large">
						<?php esc_html_e( 'Save Settings', 'kr-toolkit' ); ?>
					</button>
				</p>
			</form>
		</div>
		
		<div class="kr-settings-sidebar">
			<div class="kr-card">
				<h3><?php esc_html_e( 'Need Help?', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'If you encounter any issues with updates:', 'kr-toolkit' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'Clear your browser cache', 'kr-toolkit' ); ?></li>
					<li><?php esc_html_e( 'Check WordPress Updates page', 'kr-toolkit' ); ?></li>
					<li><?php esc_html_e( 'Verify GitHub releases are published', 'kr-toolkit' ); ?></li>
					<li><?php esc_html_e( 'Wait 12 hours for automatic check', 'kr-toolkit' ); ?></li>
				</ul>
			</div>
			
			<div class="kr-card">
				<h3><?php esc_html_e( 'Resources', 'kr-toolkit' ); ?></h3>
				<p><strong><?php esc_html_e( 'Documentation:', 'kr-toolkit' ); ?></strong><br>
				<a href="https://krtheme.com/docs" target="_blank">https://krtheme.com/docs</a></p>
				<p><strong><?php esc_html_e( 'Support:', 'kr-toolkit' ); ?></strong><br>
				<a href="https://krtheme.com/support" target="_blank">https://krtheme.com/support</a></p>
			</div>
		</div>
	</div>
</div>

<style>
.kr-toolkit-settings {
	max-width: 1200px;
}

.kr-settings-container {
	display: flex;
	gap: 20px;
	margin-top: 20px;
}

.kr-settings-content {
	flex: 1;
}

.kr-settings-sidebar {
	width: 300px;
}

.kr-card {
	background: #fff;
	border: 1px solid #ccd0d4;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	padding: 20px;
	margin-bottom: 20px;
}

.kr-card h2 {
	margin-top: 0;
	padding-bottom: 10px;
	border-bottom: 1px solid #eee;
}

.kr-card h3 {
	margin-top: 0;
}

.kr-card ul {
	margin-left: 20px;
}

.kr-info-table th {
	font-weight: 600;
	width: 200px;
}

.kr-info-table code {
	background: #f0f0f1;
	padding: 3px 6px;
	border-radius: 3px;
}

.kr-manual-check {
	margin-top: 15px;
	padding-top: 15px;
	border-top: 1px solid #eee;
}

.kr-manual-check .button {
	display: inline-flex;
	align-items: center;
	gap: 5px;
}
</style>
