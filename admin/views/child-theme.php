<?php
/**
 * Child Theme Generator Page View
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$child_theme_manager = new KR_Child_Theme_Manager();
$has_child_theme = $child_theme_manager->has_child_theme();
$child_theme_info = $has_child_theme ? $child_theme_manager->get_child_theme_info() : null;
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'Child Theme Generator', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Create a Child Theme', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'A child theme allows you to customize your site without modifying the parent theme files. This ensures your changes won\'t be lost during theme updates.', 'kr-toolkit' ); ?></p>
	</div>

	<?php if ( $has_child_theme && $child_theme_info ) : ?>
		<!-- Existing Child Theme -->
		<div class="kr-child-theme-exists" style="background: #dcfce7; border: 2px solid #16a34a; border-radius: 8px; padding: 30px; margin: 20px 0;">
			<div style="display: flex; align-items: center; gap: 20px;">
				<span class="dashicons dashicons-yes-alt" style="font-size: 48px; color: #16a34a;"></span>
				<div style="flex: 1;">
					<h2 style="margin: 0 0 10px 0; color: #16a34a;">
						<?php esc_html_e( '✓ Child Theme Active', 'kr-toolkit' ); ?>
					</h2>
					<p style="margin: 0; font-size: 15px;">
						<?php 
						printf(
							esc_html__( 'You are currently using the child theme: %s', 'kr-toolkit' ),
							'<strong>' . esc_html( $child_theme_info['name'] ) . '</strong>'
						);
						?>
					</p>
					<div style="margin-top: 15px; font-size: 14px; color: #16a34a;">
						<strong><?php esc_html_e( 'Version:', 'kr-toolkit' ); ?></strong> 
						<?php echo esc_html( $child_theme_info['version'] ); ?>
						<span style="margin: 0 10px;">|</span>
						<strong><?php esc_html_e( 'Folder:', 'kr-toolkit' ); ?></strong> 
						<code><?php echo esc_html( $child_theme_info['folder'] ); ?></code>
					</div>
				</div>
			</div>
		</div>

		<!-- Child Theme Details -->
		<div class="kr-child-theme-details" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;">
			<h3><?php esc_html_e( 'Child Theme Information', 'kr-toolkit' ); ?></h3>
			<table class="widefat" style="border-radius: 8px; overflow: hidden;">
				<tbody>
					<tr>
						<td style="width: 30%; font-weight: 600;"><?php esc_html_e( 'Theme Name', 'kr-toolkit' ); ?></td>
						<td><?php echo esc_html( $child_theme_info['name'] ); ?></td>
					</tr>
					<tr>
						<td style="font-weight: 600;"><?php esc_html_e( 'Description', 'kr-toolkit' ); ?></td>
						<td><?php echo esc_html( $child_theme_info['description'] ); ?></td>
					</tr>
					<tr>
						<td style="font-weight: 600;"><?php esc_html_e( 'Author', 'kr-toolkit' ); ?></td>
						<td><?php echo esc_html( $child_theme_info['author'] ); ?></td>
					</tr>
					<tr>
						<td style="font-weight: 600;"><?php esc_html_e( 'Version', 'kr-toolkit' ); ?></td>
						<td><?php echo esc_html( $child_theme_info['version'] ); ?></td>
					</tr>
					<tr>
						<td style="font-weight: 600;"><?php esc_html_e( 'Template', 'kr-toolkit' ); ?></td>
						<td><?php echo esc_html( $child_theme_info['template'] ); ?></td>
					</tr>
					<tr>
						<td style="font-weight: 600;"><?php esc_html_e( 'Directory', 'kr-toolkit' ); ?></td>
						<td><code><?php echo esc_html( $child_theme_info['path'] ); ?></code></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php else : ?>
		<!-- No Child Theme - Show Creation Form -->
		<div class="kr-no-child-theme" style="background: #fef3c7; border: 2px solid #fbbf24; border-radius: 8px; padding: 30px; margin: 20px 0;">
			<div style="display: flex; align-items: center; gap: 20px;">
				<span class="dashicons dashicons-info" style="font-size: 48px; color: #d97706;"></span>
				<div>
					<h2 style="margin: 0 0 10px 0; color: #d97706;">
						<?php esc_html_e( 'No Child Theme Detected', 'kr-toolkit' ); ?>
					</h2>
					<p style="margin: 0; font-size: 15px;">
						<?php esc_html_e( 'Create a child theme to safely customize your site without losing changes during updates.', 'kr-toolkit' ); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- Child Theme Creation Form -->
		<div class="kr-child-theme-form" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;">
			<h3><?php esc_html_e( 'Create New Child Theme', 'kr-toolkit' ); ?></h3>
			<form id="kr-child-theme-form" method="post">
				<div class="form-group" style="margin-bottom: 20px;">
					<label for="child-theme-name" style="display: block; font-weight: 600; margin-bottom: 10px;">
						<?php esc_html_e( 'Child Theme Name', 'kr-toolkit' ); ?> <span style="color: #dc2626;">*</span>
					</label>
					<input 
						type="text" 
						id="child-theme-name" 
						name="child_theme_name" 
						placeholder="My Custom KR Theme"
						value="KR Theme Child"
						style="width: 100%; max-width: 500px; padding: 12px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 6px;"
						required
					>
					<p class="description" style="margin-top: 8px;">
						<?php esc_html_e( 'Enter a name for your child theme. This will appear in the theme list.', 'kr-toolkit' ); ?>
					</p>
				</div>

				<div class="form-group" style="margin-bottom: 20px;">
					<label for="child-theme-author" style="display: block; font-weight: 600; margin-bottom: 10px;">
						<?php esc_html_e( 'Author Name', 'kr-toolkit' ); ?>
					</label>
					<input 
						type="text" 
						id="child-theme-author" 
						name="child_theme_author" 
						placeholder="Your Name"
						value="<?php echo esc_attr( wp_get_current_user()->display_name ); ?>"
						style="width: 100%; max-width: 500px; padding: 12px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 6px;"
					>
					<p class="description" style="margin-top: 8px;">
						<?php esc_html_e( 'Optional: Your name will be listed as the theme author.', 'kr-toolkit' ); ?>
					</p>
				</div>

				<div class="form-group" style="margin-bottom: 20px;">
					<label for="child-theme-description" style="display: block; font-weight: 600; margin-bottom: 10px;">
						<?php esc_html_e( 'Description', 'kr-toolkit' ); ?>
					</label>
					<textarea 
						id="child-theme-description" 
						name="child_theme_description" 
						rows="3"
						placeholder="A custom child theme for KR Theme"
						style="width: 100%; max-width: 500px; padding: 12px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 6px;"
					>A custom child theme for KR Theme. Safe to customize without losing changes on updates.</textarea>
					<p class="description" style="margin-top: 8px;">
						<?php esc_html_e( 'Optional: A brief description of your child theme.', 'kr-toolkit' ); ?>
					</p>
				</div>

				<div class="form-group" style="margin-bottom: 20px;">
					<label style="display: flex; align-items: center; gap: 8px;">
						<input type="checkbox" id="activate-child-theme" name="activate_child_theme" value="1" checked>
						<span><?php esc_html_e( 'Activate child theme after creation', 'kr-toolkit' ); ?></span>
					</label>
				</div>

				<button type="submit" class="button button-primary button-hero kr-create-child-theme" style="font-size: 16px; padding: 12px 30px; height: auto;">
					<span class="dashicons dashicons-admin-appearance" style="margin-top: 4px;"></span>
					<?php esc_html_e( 'Create Child Theme', 'kr-toolkit' ); ?>
				</button>
			</form>
		</div>
	<?php endif; ?>

	<!-- Why Use a Child Theme -->
	<div class="kr-child-theme-benefits" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: #fff; padding: 40px; border-radius: 8px; margin: 40px 0;">
		<h2 style="color: #fff; margin-top: 0; text-align: center;">
			<?php esc_html_e( '✨ Why Use a Child Theme?', 'kr-toolkit' ); ?>
		</h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 30px;">
			<div style="text-align: center;">
				<span class="dashicons dashicons-update" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Safe Updates', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Update the parent theme without losing your customizations.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center;">
				<span class="dashicons dashicons-edit" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Easy Customization', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Modify templates, styles, and functions without touching parent files.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center;">
				<span class="dashicons dashicons-backup" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Fallback Protection', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'If something breaks, you can always switch back to the parent theme.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center;">
				<span class="dashicons dashicons-wordpress" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Best Practice', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Recommended by WordPress for all theme customizations.', 'kr-toolkit' ); ?></p>
			</div>
		</div>
	</div>

	<!-- How It Works -->
	<div class="kr-how-it-works" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0;">
		<h2><?php esc_html_e( 'How It Works', 'kr-toolkit' ); ?></h2>
		
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
			<div style="text-align: center; padding: 20px;">
				<div style="background: #3b82f6; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 15px;">1</div>
				<h3><?php esc_html_e( 'Create', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Generate a child theme with one click.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center; padding: 20px;">
				<div style="background: #8b5cf6; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 15px;">2</div>
				<h3><?php esc_html_e( 'Activate', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Automatically activate or do it manually later.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center; padding: 20px;">
				<div style="background: #ec4899; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 15px;">3</div>
				<h3><?php esc_html_e( 'Customize', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Start customizing your site safely.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center; padding: 20px;">
				<div style="background: #10b981; color: #fff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 15px;">4</div>
				<h3><?php esc_html_e( 'Update', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Update parent theme without worries.', 'kr-toolkit' ); ?></p>
			</div>
		</div>
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
