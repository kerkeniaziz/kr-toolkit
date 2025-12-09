<?php
/**
 * Demos Import Page View
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$demo_importer = new KR_Demo_Importer();
$demos = $demo_importer->get_available_demos();
$license_manager = new KR_License_Manager();
$is_license_active = $license_manager->is_license_active();

// Check for import warning
$import_warning = get_transient( 'kr_toolkit_import_warning' );
?>

<div class="wrap kr-toolkit-admin">
	<h1><?php esc_html_e( 'Demo Import', 'kr-toolkit' ); ?></h1>
	
	<?php if ( $import_warning ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><?php esc_html_e( 'System Requirements Warning:', 'kr-toolkit' ); ?></strong></p>
			<p><?php echo esc_html( $import_warning ); ?></p>
		</div>
		<?php delete_transient( 'kr_toolkit_import_warning' ); ?>
	<?php endif; ?>
	
	<div class="kr-toolkit-welcome">
		<h2><?php esc_html_e( 'Import Pre-Built Demo Sites', 'kr-toolkit' ); ?></h2>
		<p><?php esc_html_e( 'Choose from our collection of professionally designed starter templates. Import with one click and customize to match your brand.', 'kr-toolkit' ); ?></p>
		
		<?php if ( ! $is_license_active ) : ?>
			<div class="notice notice-info inline" style="margin-top: 15px;">
				<p>
					<strong><?php esc_html_e( '🎁 Want More Demos?', 'kr-toolkit' ); ?></strong><br>
					<?php esc_html_e( 'Activate your license to unlock premium demo templates with advanced features.', 'kr-toolkit' ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=kr-toolkit-license' ) ); ?>" class="button button-primary" style="margin-left: 10px;">
						<?php esc_html_e( 'Activate License', 'kr-toolkit' ); ?>
					</a>
				</p>
			</div>
		<?php endif; ?>
	</div>

	<!-- Filter Tabs -->
	<div class="kr-demo-filters" style="margin: 30px 0;">
		<button class="button" data-filter="all"><?php esc_html_e( 'All Demos', 'kr-toolkit' ); ?></button>
		<button class="button" data-filter="free"><?php esc_html_e( 'Free', 'kr-toolkit' ); ?></button>
		<?php if ( $is_license_active ) : ?>
			<button class="button" data-filter="pro"><?php esc_html_e( 'Pro', 'kr-toolkit' ); ?></button>
		<?php endif; ?>
	</div>

	<!-- Demos Grid -->
	<div class="kr-demos-grid">
		<?php if ( ! empty( $demos ) ) : ?>
			<?php foreach ( $demos as $demo ) : ?>
				<?php
				$is_pro = isset( $demo['is_pro'] ) && $demo['is_pro'];
				$can_import = ! $is_pro || $is_license_active;
				$demo_slug = $demo['slug'];
				$demo_name = $demo['name'];
				$demo_description = $demo['description'];
				$demo_url = isset( $demo['demo_url'] ) ? $demo['demo_url'] : '#';
				$screenshot = isset( $demo['screenshot'] ) ? KR_TOOLKIT_URL . 'demos/' . $demo_slug . '/' . $demo['screenshot'] : KR_TOOLKIT_URL . 'assets/images/demo-placeholder.png';
				?>
				<div class="kr-demo-card" data-category="<?php echo $is_pro ? 'pro' : 'free'; ?>">
					<div class="kr-demo-image">
						<img src="<?php echo esc_url( $screenshot ); ?>" alt="<?php echo esc_attr( $demo_name ); ?>" loading="lazy">
						<div class="kr-demo-overlay">
							<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" rel="noopener" class="button">
								<?php esc_html_e( 'Preview', 'kr-toolkit' ); ?>
							</a>
						</div>
					</div>
					<div class="kr-demo-content">
						<span class="kr-demo-badge <?php echo $is_pro ? 'pro' : 'free'; ?>">
							<?php echo $is_pro ? esc_html__( 'Pro', 'kr-toolkit' ) : esc_html__( 'Free', 'kr-toolkit' ); ?>
						</span>
						<h3 class="kr-demo-title"><?php echo esc_html( $demo_name ); ?></h3>
						<p><?php echo esc_html( $demo_description ); ?></p>
						
						<?php if ( isset( $demo['features'] ) && is_array( $demo['features'] ) ) : ?>
							<ul class="kr-demo-features" style="font-size: 13px; color: #64748b; list-style: none; padding: 0; margin: 10px 0;">
								<?php foreach ( array_slice( $demo['features'], 0, 3 ) as $feature ) : ?>
									<li style="padding: 3px 0;">✓ <?php echo esc_html( $feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						
						<div class="kr-demo-actions">
							<?php if ( $can_import ) : ?>
								<button class="button button-primary kr-import-demo" data-demo-slug="<?php echo esc_attr( $demo_slug ); ?>">
									<span class="dashicons dashicons-download"></span>
									<?php esc_html_e( 'Import', 'kr-toolkit' ); ?>
								</button>
							<?php else : ?>
								<button class="button button-secondary" disabled>
									<span class="dashicons dashicons-lock"></span>
									<?php esc_html_e( 'Pro Only', 'kr-toolkit' ); ?>
								</button>
							<?php endif; ?>
							<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" rel="noopener" class="button">
								<?php esc_html_e( 'Preview', 'kr-toolkit' ); ?>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="notice notice-warning">
				<p><?php esc_html_e( 'No demos available at the moment. Please check back later.', 'kr-toolkit' ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<!-- Import Instructions -->
	<div class="kr-import-info" style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 40px;">
		<h2><?php esc_html_e( 'Before You Import', 'kr-toolkit' ); ?></h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
			<div>
				<h3 style="margin-top: 0;">✓ <?php esc_html_e( 'Check Requirements', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Make sure your server meets all requirements for successful import.', 'kr-toolkit' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=kr-toolkit-system-info' ) ); ?>" class="button">
					<?php esc_html_e( 'View System Info', 'kr-toolkit' ); ?>
				</a>
			</div>
			<div>
				<h3 style="margin-top: 0;">💾 <?php esc_html_e( 'Backup Your Site', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'Always create a backup before importing demo content to your live site.', 'kr-toolkit' ); ?></p>
			</div>
			<div>
				<h3 style="margin-top: 0;">🚀 <?php esc_html_e( 'Fresh Installation', 'kr-toolkit' ); ?></h3>
				<p><?php esc_html_e( 'For best results, import demos on a fresh WordPress installation.', 'kr-toolkit' ); ?></p>
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

<style>
.kr-demo-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.7);
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: 0;
	transition: opacity 0.3s ease;
}

.kr-demo-image:hover .kr-demo-overlay {
	opacity: 1;
}

.kr-demo-image {
	position: relative;
}

.kr-demo-filters button {
	margin-right: 10px;
}

.kr-demo-filters button.active {
	background: #2563eb;
	color: #fff;
	border-color: #2563eb;
}

.kr-demo-card[data-category].hidden {
	display: none;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Demo filter
	$('.kr-demo-filters button').on('click', function() {
		var filter = $(this).data('filter');
		
		$('.kr-demo-filters button').removeClass('active');
		$(this).addClass('active');
		
		if (filter === 'all') {
			$('.kr-demo-card').removeClass('hidden');
		} else {
			$('.kr-demo-card').addClass('hidden');
			$('.kr-demo-card[data-category="' + filter + '"]').removeClass('hidden');
		}
	});
	
	// Set default active filter
	$('.kr-demo-filters button[data-filter="all"]').addClass('active');
});
</script>
