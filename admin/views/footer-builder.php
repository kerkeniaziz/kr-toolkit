<?php
/**
 * Footer Builder Page View
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'Footer Builder', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Create Custom Footers', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Design professional footers with multiple columns, widgets, and custom content. Build stunning footers that match your brand without any coding.', 'kr-toolkit' ); ?></p>
	</div>

	<!-- Footer Builder Features -->
	<div class="kr-builder-features" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 40px;">
		<h2><?php esc_html_e( 'Footer Builder Features', 'kr-toolkit' ); ?></h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
			<div>
				<h3>🎯 <?php esc_html_e( 'Drag & Drop Interface', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Build custom footers by dragging and dropping elements into your design. No coding required.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📚 <?php esc_html_e( 'Footer Widgets', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Add widget areas for content, social links, menus, contact info, and custom text blocks.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📐 <?php esc_html_e( 'Multi-Column Layout', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Create 1, 2, 3, 4, or custom column layouts for your footer content sections.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>🎨 <?php esc_html_e( 'Footer Styling', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Customize colors, backgrounds, text styles, and spacing for your footer sections.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>✏️ <?php esc_html_e( 'Copyright Text', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Edit copyright and footer credit text with dynamic year support and custom markup.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3>📱 <?php esc_html_e( 'Responsive Design', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Footers automatically adapt to mobile, tablet, and desktop screen sizes.', 'kr-toolkit' ); ?></p>
			</div>
		</div>
	</div>

	<!-- Footer Options -->
	<div style="background: #f9fafb; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 40px;">
		<h2><?php esc_html_e( 'Current Footer Options', 'kr-toolkit' ); ?></h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
			<div style="padding: 15px; background: white; border-radius: 4px; border: 1px solid #e2e8f0;">
				<strong><?php esc_html_e( 'Footer Widgets', 'kr-toolkit' ); ?></strong>
				<p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">
					<?php esc_html_e( 'Manage footer widget areas from the Customizer or Widgets page.', 'kr-toolkit' ); ?>
				</p>
			</div>
			<div style="padding: 15px; background: white; border-radius: 4px; border: 1px solid #e2e8f0;">
				<strong><?php esc_html_e( 'Copyright Text', 'kr-toolkit' ); ?></strong>
				<p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">
					<?php esc_html_e( 'Customize copyright notice in Theme Options.', 'kr-toolkit' ); ?>
				</p>
			</div>
			<div style="padding: 15px; background: white; border-radius: 4px; border: 1px solid #e2e8f0;">
				<strong><?php esc_html_e( 'Footer Menu', 'kr-toolkit' ); ?></strong>
				<p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">
					<?php esc_html_e( 'Create footer navigation from Customizer menus.', 'kr-toolkit' ); ?>
				</p>
			</div>
			<div style="padding: 15px; background: white; border-radius: 4px; border: 1px solid #e2e8f0;">
				<strong><?php esc_html_e( 'Footer Colors', 'kr-toolkit' ); ?></strong>
				<p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">
					<?php esc_html_e( 'Customize footer background and text colors.', 'kr-toolkit' ); ?>
				</p>
			</div>
		</div>
	</div>

	<!-- Coming Soon Banner -->
	<div style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); color: white; padding: 40px; border-radius: 8px; margin-top: 40px; text-align: center;">
		<h3 style="margin-top: 0;">🚀 <?php esc_html_e( 'Advanced Footer Builder', 'kr-toolkit' ); ?></h3>
		<p><?php esc_html_e( 'The advanced footer builder with full drag-and-drop functionality is available in the Pro version.', 'kr-toolkit' ); ?></p>
		<p><?php esc_html_e( 'For now, use the Widgets page and Customizer to design your footer.', 'kr-toolkit' ); ?></p>
		<div style="margin-top: 15px;">
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button" style="background: white; color: #a855f7; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; text-decoration: none; margin-right: 10px;">
				<?php esc_html_e( 'Open Customizer', 'kr-toolkit' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>" class="button" style="background: white; color: #a855f7; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; text-decoration: none;">
				<?php esc_html_e( 'Manage Widgets', 'kr-toolkit' ); ?>
			</a>
		</div>
	</div>

	<!-- How To Use -->
	<div style="background: #f9fafb; padding: 30px; border-radius: 8px; border-left: 4px solid #a855f7; margin-top: 40px;">
		<h3><?php esc_html_e( 'How to Customize Your Footer:', 'kr-toolkit' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'Go to Customizer → Footer Settings', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Choose footer layout and columns', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Add widgets to footer areas', 'kr-toolkit' ); ?></li>
			<li><?php esc_html_e( 'Customize footer colors and styling', 'kr-toolkit' ); ?></li>
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
	color: #a855f7;
	margin-top: 0;
	font-size: 18px;
}

.kr-builder-features p {
	color: #666;
	line-height: 1.6;
	margin: 0;
}
</style>
