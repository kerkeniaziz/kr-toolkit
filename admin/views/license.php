<?php
/**
 * License Management Page View
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$license_manager = new KR_License_Manager();
$license_key = $license_manager->get_license_key();
$license_data = $license_manager->get_license_data();
$is_active = $license_manager->is_license_active();
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'License Management', 'kr-toolkit' ); ?></h1>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Activate Your License', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Enter your license key to unlock premium features, pro demos, and automatic updates.', 'kr-toolkit' ); ?></p>
	</div>

	<!-- License Status -->
	<?php if ( $is_active && $license_data ) : ?>
		<div class="kr-license-status active" style="background: #dcfce7; border: 2px solid #16a34a; border-radius: 8px; padding: 30px; margin: 20px 0;">
			<div style="display: flex; align-items: center; gap: 20px;">
				<span class="dashicons dashicons-yes-alt" style="font-size: 48px; color: #16a34a;"></span>
				<div style="flex: 1;">
					<h2 style="margin: 0 0 10px 0; color: #16a34a;">
						<?php esc_html_e( '✓ License Active', 'kr-toolkit' ); ?>
					</h2>
					<p style="margin: 0; font-size: 15px;">
						<?php esc_html_e( 'Your license is active and all premium features are unlocked!', 'kr-toolkit' ); ?>
					</p>
					<div style="margin-top: 15px; font-size: 14px; color: #16a34a;">
						<strong><?php esc_html_e( 'License Type:', 'kr-toolkit' ); ?></strong> 
						<?php echo esc_html( isset( $license_data['license_type'] ) ? ucfirst( $license_data['license_type'] ) : 'Regular' ); ?>
						<span style="margin: 0 10px;">|</span>
						<strong><?php esc_html_e( 'Expires:', 'kr-toolkit' ); ?></strong> 
						<?php 
						if ( isset( $license_data['expires_at'] ) && ! empty( $license_data['expires_at'] ) ) {
							echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license_data['expires_at'] ) ) );
						} else {
							esc_html_e( 'Never', 'kr-toolkit' );
						}
						?>
					</div>
				</div>
				<button class="button button-secondary kr-deactivate-license">
					<?php esc_html_e( 'Deactivate', 'kr-toolkit' ); ?>
				</button>
			</div>
		</div>
	<?php else : ?>
		<div class="kr-license-status inactive" style="background: #fef2f2; border: 2px solid #fca5a5; border-radius: 8px; padding: 30px; margin: 20px 0;">
			<div style="display: flex; align-items: center; gap: 20px;">
				<span class="dashicons dashicons-lock" style="font-size: 48px; color: #dc2626;"></span>
				<div>
					<h2 style="margin: 0 0 10px 0; color: #dc2626;">
						<?php esc_html_e( 'License Not Active', 'kr-toolkit' ); ?>
					</h2>
					<p style="margin: 0; font-size: 15px;">
						<?php esc_html_e( 'Activate your license to unlock premium demos, features, and automatic updates.', 'kr-toolkit' ); ?>
					</p>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- License Activation Form -->
	<?php if ( ! $is_active ) : ?>
		<div class="kr-license-form" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;">
			<h3><?php esc_html_e( 'Enter License Key', 'kr-toolkit' ); ?></h3>
			<form id="kr-license-form" method="post">
				<div class="form-group" style="margin-bottom: 20px;">
					<label for="license-key" style="display: block; font-weight: 600; margin-bottom: 10px;">
						<?php esc_html_e( 'License Key', 'kr-toolkit' ); ?>
					</label>
					<input 
						type="text" 
						id="license-key" 
						name="license_key" 
						placeholder="XXXX-XXXX-XXXX-XXXX"
						value="<?php echo esc_attr( $license_key ); ?>"
						style="width: 100%; max-width: 500px; padding: 12px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 6px;"
						required
					>
					<p class="description" style="margin-top: 8px;">
						<?php esc_html_e( 'Enter your license key from your purchase confirmation email.', 'kr-toolkit' ); ?>
					</p>
				</div>
				<button type="submit" class="button button-primary button-hero kr-activate-license" style="font-size: 16px; padding: 12px 30px; height: auto;">
					<span class="dashicons dashicons-unlock" style="margin-top: 4px;"></span>
					<?php esc_html_e( 'Activate License', 'kr-toolkit' ); ?>
				</button>
			</form>
		</div>
	<?php endif; ?>

	<!-- Premium Features -->
	<div class="kr-premium-features" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 40px; border-radius: 8px; margin: 40px 0;">
		<h2 style="color: #fff; margin-top: 0; text-align: center;">
			<?php esc_html_e( '🎁 Premium Features', 'kr-toolkit' ); ?>
		</h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 30px;">
			<div style="text-align: center;">
				<span class="dashicons dashicons-star-filled" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Pro Demos', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Access 20+ premium demo templates with advanced features and layouts.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center;">
				<span class="dashicons dashicons-update" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Auto Updates', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Get automatic theme and plugin updates delivered right to your dashboard.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center;">
				<span class="dashicons dashicons-sos" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Priority Support', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Get help from our dedicated support team within 24 hours.', 'kr-toolkit' ); ?></p>
			</div>
			<div style="text-align: center;">
				<span class="dashicons dashicons-admin-customizer" style="font-size: 48px; margin-bottom: 15px;"></span>
				<h3 style="color: #fff;"><?php esc_html_e( 'Pro Widgets', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Unlock advanced Elementor widgets and customization options.', 'kr-toolkit' ); ?></p>
			</div>
		</div>
		<div style="text-align: center; margin-top: 40px;">
			<a href="https://krtheme.com/pricing" target="_blank" rel="noopener" class="button button-secondary button-hero" style="background: #fff; color: #667eea; border: none; font-size: 16px; padding: 12px 30px; height: auto;">
				<?php esc_html_e( 'Get Premium License', 'kr-toolkit' ); ?>
			</a>
		</div>
	</div>

	<!-- FAQ -->
	<div class="kr-license-faq" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0;">
		<h2><?php esc_html_e( 'Frequently Asked Questions', 'kr-toolkit' ); ?></h2>
		
		<div class="faq-item" style="margin: 20px 0; padding: 20px; background: #f8fafc; border-radius: 6px;">
			<h3 style="margin-top: 0; color: #2563eb;"><?php esc_html_e( 'Where can I find my license key?', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Your license key is in the purchase confirmation email you received after buying the theme. You can also find it in your account dashboard at krtheme.com.', 'kr-toolkit' ); ?></p>
		</div>

		<div class="faq-item" style="margin: 20px 0; padding: 20px; background: #f8fafc; border-radius: 6px;">
			<h3 style="margin-top: 0; color: #2563eb;"><?php esc_html_e( 'Can I use one license on multiple sites?', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Regular licenses are for single site use. Extended licenses allow multiple site installations. Check your license terms for details.', 'kr-toolkit' ); ?></p>
		</div>

		<div class="faq-item" style="margin: 20px 0; padding: 20px; background: #f8fafc; border-radius: 6px;">
			<h3 style="margin-top: 0; color: #2563eb;"><?php esc_html_e( 'What happens when my license expires?', 'kr-toolkit' ); ?></h3>
			<p><?php esc_html_e( 'Your site will continue to work, but you will lose access to updates and premium features. You can renew your license at any time to restore access.', 'kr-toolkit' ); ?></p>
		</div>

		<div class="faq-item" style="margin: 20px 0; padding: 20px; background: #f8fafc; border-radius: 6px;">
			<h3 style="margin-top: 0; color: #2563eb;"><?php esc_html_e( 'Need help?', 'kr-toolkit' ); ?></h3>
			<p>
				<?php 
				printf(
					esc_html__( 'Contact our support team at %s for assistance with license activation or any other questions.', 'kr-toolkit' ),
					'<a href="mailto:support@krtheme.com">support@krtheme.com</a>'
				);
				?>
			</p>
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
