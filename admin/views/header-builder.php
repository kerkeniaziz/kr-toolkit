<?php
/**
 * Header Builder Page View
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'Header Builder', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Create Custom Headers', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Design professional headers with drag-and-drop interface. Add logos, menus, search bars, buttons, and more without coding.', 'kr-toolkit' ); ?></p>
	</div>

	<!-- Header Builder Features -->
	<div class="kr-builder-features" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 40px;">
		<h2><?php esc_html_e( 'Header Builder Features', 'kr-toolkit' ); ?></h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
			<div>
				<h3>🎯 <?php esc_html_e( 'Drag & Drop Interface', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Build custom headers by dragging and dropping elements into your design. No coding required.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>🎨 <?php esc_html_e( 'Design Elements', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Add logos, navigation menus, search bars, buttons, social icons, and custom text.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📱 <?php esc_html_e( 'Responsive Layout', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Create headers that look perfect on desktop, tablet, and mobile devices automatically.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>🎪 <?php esc_html_e( 'Header Variants', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Create different header layouts for different page types with custom headers feature.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>⚙️ <?php esc_html_e( 'Sticky Headers', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Enable sticky header that stays at the top when users scroll down the page.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>🎭 <?php esc_html_e( 'Styling Options', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Customize colors, backgrounds, spacing, animations, and typography for each element.', 'kr-toolkit' ); ?></p>
			</div>
		</div>
	</div>

	<!-- Coming Soon Banner -->
	<div style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 40px; border-radius: 8px; margin-top: 40px; text-align: center;">
		<h3 style="margin-top: 0;">🚀 <?php esc_html_e( 'Advanced Header Builder', 'kr-toolkit' ); ?></h3>
		<p><?php esc_html_e( 'The advanced header builder with full drag-and-drop functionality is available in the Pro version.', 'kr-toolkit' ); ?></p>
		<p><?php esc_html_e( 'For now, use the Theme Customizer to configure your header settings.', 'kr-toolkit' ); ?></p>
		<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-light" style="background: white; color: #2563eb; border: none; margin-top: 15px; padding: 10px 20px; font-weight: bold; border-radius: 4px; text-decoration: none; cursor: pointer;">
			<?php esc_html_e( 'Open Customizer', 'kr-toolkit' ); ?>
		</a>
	</div>

	<!-- How To Use -->
	<div style="background: #f9fafb; padding: 30px; border-radius: 8px; border-left: 4px solid #2563eb; margin-top: 40px;">
		<h3><?php esc_html_e( 'How to Customize Your Header:', 'kr-toolkit' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'Go to Customizer', 'kr-toolkit' ); ?> → <?php esc_html_e( 'Header Settings', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Configure logo, menu, and layout options', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Choose header style (standard, minimal, modern)', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Enable sticky header if needed', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Save and publish your changes', 'kr-toolkit' ); ?></li>
		</ol>
	</div>

	<div class="kr-toolkit-footer-info">
		<p style="text-align: center; color: #666; margin-top: 30px;">
			<?php
			printf(
				esc_html__( '%1$s v%2$s - Developed by %3$s', 'kr-toolkit' ),
				'<strong>KR Toolkit</strong>',
				KR_TOOLKIT_VERSION,
				'<a href="https://www.krtheme.com/" target="_blank" rel="noopener">KR Theme</a>'
			);
			?>
			<br>
			<small><?php printf( esc_html__( 'Copyright © %s KR Theme. All rights reserved.', 'kr-toolkit' ), date( 'Y' ) ); ?></small>
		</p>
	</div>
</div>

<style>
.kr-builder-features h3 {
	color: #2563eb;
	margin-top: 0;
	font-size: 18px;
}

.kr-builder-features p {
	color: #666;
	line-height: 1.6;
	margin: 0;
}
</style>
