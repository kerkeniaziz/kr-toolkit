<?php
/**
 * Dashboard Page View
 *
 * @package KR_Toolkit
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$license_manager = new KR_License_Manager();
$is_license_active = $license_manager->is_license_active();
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'KR Toolkit Dashboard', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Welcome to KR Toolkit!', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Thank you for choosing KR Theme. Get started by importing a demo or creating a child theme.', 'kr-toolkit' ); ?></p>
	</div>

	<div class="kr-toolkit-cards">
		<div class="kr-toolkit-card">
			<div class="kr-toolkit-card-icon">
				<span class="dashicons dashicons-download"></span>
			</div>
			<h3><?php esc_html_e( 'Import Demo', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Import pre-built starter templates with one click. Choose from curated free demos.', 'kr-toolkit' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=kr-toolkit-demos' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Browse Demos', 'kr-toolkit' ); ?>
			</a>
		</div>

		<div class="kr-toolkit-card">
			<div class="kr-toolkit-card-icon">
				<span class="dashicons dashicons-admin-appearance"></span>
			</div>
			<h3><?php esc_html_e( 'Child Theme', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Create a child theme to safely customize your site without losing changes on theme updates.', 'kr-toolkit' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=kr-toolkit-child-theme' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Create Child Theme', 'kr-toolkit' ); ?>
			</a>
		</div>

		<div class="kr-toolkit-card">
			<div class="kr-toolkit-card-icon">
				<span class="dashicons dashicons-admin-tools"></span>
			</div>
			<h3><?php esc_html_e( 'System Info', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Check your server configuration and ensure it meets the requirements for demo import.', 'kr-toolkit' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=kr-toolkit-system-info' ) ); ?>" class="button">
				<?php esc_html_e( 'View System Info', 'kr-toolkit' ); ?>
			</a>
		</div>

		<div class="kr-toolkit-card">
			<div class="kr-toolkit-card-icon">
				<span class="dashicons dashicons-admin-network"></span>
			</div>
			<h3><?php esc_html_e( 'License', 'kr-toolkit' ); ?></h3>
			<p>
				<?php if ( $is_license_active ) : ?>
					<span class="kr-license-status active"><?php esc_html_e( 'Active', 'kr-toolkit' ); ?></span>
					<?php esc_html_e( 'Your license is active. Enjoy all pro features!', 'kr-toolkit' ); ?>
				<?php else : ?>
					<span class="kr-license-status inactive"><?php esc_html_e( 'Inactive', 'kr-toolkit' ); ?></span>
					<?php esc_html_e( 'Activate your license to unlock pro demos and features.', 'kr-toolkit' ); ?>
				<?php endif; ?>
			</p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=kr-toolkit-license' ) ); ?>" class="button">
				<?php esc_html_e( 'Manage License', 'kr-toolkit' ); ?>
			</a>
		</div>
	</div>

	<div class="kr-toolkit-resources">
		<h2><?php esc_html_e( 'Helpful Resources', 'kr-toolkit' ); ?></h2>
		<div class="kr-toolkit-resource-links">
			<a href="https://krtheme.com/docs" target="_blank" rel="noopener">
				<span class="dashicons dashicons-book"></span>
				<?php esc_html_e( 'Documentation', 'kr-toolkit' ); ?>
			</a>
			<a href="https://krtheme.com/support" target="_blank" rel="noopener">
				<span class="dashicons dashicons-sos"></span>
				<?php esc_html_e( 'Support', 'kr-toolkit' ); ?>
			</a>
			<a href="https://krtheme.com/changelog" target="_blank" rel="noopener">
				<span class="dashicons dashicons-megaphone"></span>
				<?php esc_html_e( 'Changelog', 'kr-toolkit' ); ?>
			</a>
			<a href="https://krtheme.com/demos" target="_blank" rel="noopener">
				<span class="dashicons dashicons-desktop"></span>
				<?php esc_html_e( 'View Demos', 'kr-toolkit' ); ?>
			</a>
		</div>
	</div>

	<div class="kr-toolkit-footer-info">
		<p style="text-align: center; color: #666; margin-top: 30px;">
			<?php
			printf(
				/* translators: 1: theme name, 2: version, 3: author name, 4: author URL */
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
