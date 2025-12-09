<?php
/**
 * System Info Page View
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$system_requirements = new KR_System_Requirements();
$requirements_status = $system_requirements->get_requirements_status();
$system_info = $system_requirements->get_system_info();
$all_passed = $system_requirements->all_requirements_met();
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'System Information', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'System Requirements Check', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Ensure your server meets all requirements for optimal theme and plugin performance.', 'kr-toolkit' ); ?></p>
	</div>

	<!-- Overall Status -->
	<div class="kr-system-status" style="background: <?php echo $all_passed ? '#dcfce7' : '#fef2f2'; ?>; border: 2px solid <?php echo $all_passed ? '#16a34a' : '#dc2626'; ?>; border-radius: 8px; padding: 20px; margin: 20px 0;">
		<h2 style="margin-top: 0; color: <?php echo $all_passed ? '#16a34a' : '#dc2626'; ?>;">
			<span class="dashicons dashicons-<?php echo $all_passed ? 'yes-alt' : 'warning'; ?>" style="font-size: 32px;"></span>
			<?php echo $all_passed ? esc_html__( 'All Requirements Met!', 'kr-toolkit' ) : esc_html__( 'Some Requirements Not Met', 'kr-toolkit' ); ?>
		</h2>
		<p style="font-size: 15px; margin: 0;">
			<?php 
			if ( $all_passed ) {
				esc_html_e( 'Your server meets all requirements. You can proceed with demo import and full theme features.', 'kr-toolkit' );
			} else {
				esc_html_e( 'Some requirements are not met. Please contact your hosting provider to update your server configuration.', 'kr-toolkit' );
			}
			?>
		</p>
	</div>

	<!-- Requirements Check -->
	<div class="kr-requirements-table">
		<h2><?php esc_html_e( 'Requirements Check', 'kr-toolkit' ); ?></h2>
		<table class="widefat" style="border-radius: 8px; overflow: hidden;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Requirement', 'kr-toolkit' ); ?></th>
					<th><?php esc_html_e( 'Required', 'kr-toolkit' ); ?></th>
					<th><?php esc_html_e( 'Current', 'kr-toolkit' ); ?></th>
					<th><?php esc_html_e( 'Status', 'kr-toolkit' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $requirements_status as $req ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $req['label'] ); ?></strong></td>
						<td><?php echo esc_html( $req['required'] ); ?></td>
						<td><code><?php echo esc_html( $req['current'] ); ?></code></td>
						<td>
							<?php if ( $req['status'] ) : ?>
								<span class="kr-status-badge success">
									<span class="dashicons dashicons-yes-alt"></span>
									<?php esc_html_e( 'Passed', 'kr-toolkit' ); ?>
								</span>
							<?php else : ?>
								<span class="kr-status-badge error">
									<span class="dashicons dashicons-warning"></span>
									<?php esc_html_e( 'Failed', 'kr-toolkit' ); ?>
								</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<!-- Server Information -->
	<div class="kr-server-info" style="margin-top: 40px;">
		<h2><?php esc_html_e( 'Server Information', 'kr-toolkit' ); ?></h2>
		<table class="widefat" style="border-radius: 8px; overflow: hidden;">
			<tbody>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'PHP Version', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['php_version'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'WordPress Version', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['wp_version'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'Server Software', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['server_software'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'MySQL Version', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['mysql_version'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'Memory Limit', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['memory_limit'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'Max Execution Time', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['max_execution_time'] ); ?>s</code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'Upload Max Filesize', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['upload_max_filesize'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'Active Theme', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['theme'] ); ?></code></td>
				</tr>
				<tr>
					<td style="width: 35%; font-weight: 600;"><?php esc_html_e( 'Multisite', 'kr-toolkit' ); ?></td>
					<td><code><?php echo esc_html( $system_info['multisite'] ); ?></code></td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- Active Plugins -->
	<div class="kr-active-plugins" style="margin-top: 40px;">
		<h2><?php esc_html_e( 'Active Plugins', 'kr-toolkit' ); ?></h2>
		<table class="widefat" style="border-radius: 8px; overflow: hidden;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Plugin Name', 'kr-toolkit' ); ?></th>
					<th><?php esc_html_e( 'Version', 'kr-toolkit' ); ?></th>
					<th><?php esc_html_e( 'Author', 'kr-toolkit' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$active_plugins_info = $system_requirements->get_active_plugins();
				
				if ( ! empty( $active_plugins_info ) ) :
					foreach ( $active_plugins_info as $plugin ) :
						?>
						<tr>
							<td><strong><?php echo esc_html( $plugin['name'] ); ?></strong></td>
							<td><?php echo esc_html( $plugin['version'] ); ?></td>
							<td><?php echo wp_kses_post( $plugin['author'] ); ?></td>
						</tr>
						<?php
					endforeach;
				else :
					?>
					<tr>
						<td colspan="3"><?php esc_html_e( 'No active plugins.', 'kr-toolkit' ); ?></td>
					</tr>
					<?php
				endif;
				?>
			</tbody>
		</table>
	</div>

	<!-- Copy System Info -->
	<div class="kr-copy-info" style="margin-top: 40px; background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0;">
		<h2><?php esc_html_e( 'Copy System Info', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Click the button below to copy your system information. This is useful when requesting support.', 'kr-toolkit' ); ?></p>
		<button class="button button-secondary" id="kr-copy-system-info">
			<span class="dashicons dashicons-clipboard"></span>
			<?php esc_html_e( 'Copy to Clipboard', 'kr-toolkit' ); ?>
		</button>
		<textarea id="kr-system-info-text" readonly style="width: 100%; height: 300px; margin-top: 15px; font-family: monospace; font-size: 12px; display: none; padding: 15px; border: 1px solid #ddd; border-radius: 4px;"><?php echo esc_textarea( $system_requirements->export_system_info() ); ?></textarea>
	</div>

	<div class="kr-toolkit-footer-info">
		<p style="text-align: center; color: #666; margin-top: 30px;">
			<?php
			printf(
				esc_html__( '%1$s v%2$s - Developed by %3$s', 'kr-toolkit' ),
				'<strong>KR Toolkit</strong>',
				KR_TOOLKIT_VERSION,
				'<a href="https://www.kerkeniaziz.ovh/" target="_blank" rel="noopener">Aziz Kerkeni</a>'
			);
			?>
			<br>
			<small><?php esc_html_e( 'Copyright © 2015-2025 Aziz Kerkeni. All rights reserved.', 'kr-toolkit' ); ?></small>
		</p>
	</div>
</div>

<style>
.kr-status-badge {
	display: inline-flex;
	align-items: center;
	gap: 5px;
	padding: 5px 12px;
	border-radius: 4px;
	font-size: 13px;
	font-weight: 600;
}

.kr-status-badge.success {
	background: #dcfce7;
	color: #16a34a;
}

.kr-status-badge.error {
	background: #fef2f2;
	color: #dc2626;
}

.kr-status-badge .dashicons {
	font-size: 16px;
	width: 16px;
	height: 16px;
}

.widefat th {
	background: #f8fafc;
	font-weight: 600;
}

.widefat tbody tr:hover {
	background: #f8fafc;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Copy system info to clipboard
	$('#kr-copy-system-info').on('click', function() {
		var $textarea = $('#kr-system-info-text');
		$textarea.show();
		$textarea.select();
		
		try {
			document.execCommand('copy');
			
			// Show success message
			var $btn = $(this);
			var originalText = $btn.html();
			$btn.html('<span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( "Copied!", "kr-toolkit" ); ?>');
			
			setTimeout(function() {
				$btn.html(originalText);
				$textarea.hide();
			}, 2000);
		} catch (err) {
			alert('<?php esc_html_e( "Failed to copy. Please manually copy the text from the textarea.", "kr-toolkit" ); ?>');
		}
	});
});
</script>
