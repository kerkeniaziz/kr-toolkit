<?php
/**
 * Theme Options Page View
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'Theme Options', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Customize Your KR Theme', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Access all theme customization options from the WordPress Customizer. Adjust colors, fonts, layout, and more to match your brand.', 'kr-toolkit' ); ?></p>
		
		<div style="margin-top: 30px;">
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary button-large" style="padding: 10px 20px; font-size: 16px;">
				<?php esc_html_e( 'Open Theme Customizer', 'kr-toolkit' ); ?>
			</a>
		</div>
	</div>

	<!-- Theme Features -->
	<div class="kr-theme-features" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 40px;">
		<h2><?php esc_html_e( 'Theme Features', 'kr-toolkit' ); ?></h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
			<div>
				<h3>🎨 <?php esc_html_e( 'Color Customization', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Customize primary colors, secondary colors, text colors, and link colors from the customizer.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📝 <?php esc_html_e( 'Typography', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Control font families, sizes, weights, and line heights for all text elements.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📐 <?php esc_html_e( 'Layout Options', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Adjust container width, sidebar position, and page layout options easily.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>🔧 <?php esc_html_e( 'Header Settings', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Configure header layout, logo, navigation menu, and sticky header options.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>👣 <?php esc_html_e( 'Footer Settings', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Customize footer content, widget areas, copyright text, and footer menu.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📱 <?php esc_html_e( 'Responsive Design', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Preview and customize how your site looks on mobile, tablet, and desktop devices.', 'kr-toolkit' ); ?></p>
			</div>
		</div>
	</div>

	<!-- Quick Links -->
	<div class="kr-quick-links" style="background: #f5f5f5; padding: 30px; border-radius: 8px; margin-top: 40px;">
		<h2><?php esc_html_e( 'Quick Access', 'kr-toolkit' ); ?></h2>
		<div style="display: flex; gap: 15px; margin-top: 20px; flex-wrap: wrap;">
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ); ?>" class="button">
				<?php esc_html_e( 'Site Identity', 'kr-toolkit' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=colors' ) ); ?>" class="button">
				<?php esc_html_e( 'Colors', 'kr-toolkit' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=typography' ) ); ?>" class="button">
				<?php esc_html_e( 'Typography', 'kr-toolkit' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=menus' ) ); ?>" class="button">
				<?php esc_html_e( 'Menus', 'kr-toolkit' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=widgets' ) ); ?>" class="button">
				<?php esc_html_e( 'Widgets', 'kr-toolkit' ); ?>
			</a>
		</div>
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
.kr-theme-features h3 {
	color: #2563eb;
	margin-top: 0;
	font-size: 18px;
}

.kr-theme-features p {
	color: #666;
	line-height: 1.6;
	margin: 0;
}

.kr-quick-links .button {
	background-color: #2563eb;
	color: white;
	border-color: #2563eb;
	text-decoration: none;
	padding: 8px 16px;
	border-radius: 4px;
	transition: all 0.3s ease;
}

.kr-quick-links .button:hover {
	background-color: #1d4ed8;
	border-color: #1d4ed8;
	color: white;
}
</style>
