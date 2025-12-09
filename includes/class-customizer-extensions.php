<?php
/**
 * Customizer Extensions Class
 *
 * @package KR_Toolkit
 * @since 4.2.1
 * @author krtheme.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_Customizer_Extensions Class
 */
class KR_Customizer_Extensions {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'add_pro_sections' ), 999 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_scripts' ) );
	}

	/**
	 * Add Pro sections to customizer
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	public function add_pro_sections( $wp_customize ) {
		// Add Pro Features panel
		$wp_customize->add_panel(
			'kr_pro_features',
			array(
				'title'       => __( 'KR Pro Features', 'kr-toolkit' ),
				'description' => __( 'Upgrade to KR Theme Pro to unlock these features.', 'kr-toolkit' ),
				'priority'    => 5,
			)
		);

		// Header Builder Section
		$wp_customize->add_section(
			'kr_header_builder',
			array(
				'title'       => __( 'Header Builder (Pro)', 'kr-toolkit' ),
				'description' => $this->get_pro_description( 'header-builder' ),
				'panel'       => 'kr_pro_features',
				'priority'    => 10,
			)
		);

		// Footer Builder Section
		$wp_customize->add_section(
			'kr_footer_builder',
			array(
				'title'       => __( 'Footer Builder (Pro)', 'kr-toolkit' ),
				'description' => $this->get_pro_description( 'footer-builder' ),
				'panel'       => 'kr_pro_features',
				'priority'    => 20,
			)
		);

		// Typography Section
		$wp_customize->add_section(
			'kr_advanced_typography',
			array(
				'title'       => __( 'Advanced Typography (Pro)', 'kr-toolkit' ),
				'description' => $this->get_pro_description( 'typography' ),
				'panel'       => 'kr_pro_features',
				'priority'    => 30,
			)
		);

		// Performance Section
		$wp_customize->add_section(
			'kr_performance',
			array(
				'title'       => __( 'Performance Optimization (Pro)', 'kr-toolkit' ),
				'description' => $this->get_pro_description( 'performance' ),
				'panel'       => 'kr_pro_features',
				'priority'    => 40,
			)
		);

		// WooCommerce Extended Section
		$wp_customize->add_section(
			'kr_woocommerce_extended',
			array(
				'title'       => __( 'WooCommerce Extended (Pro)', 'kr-toolkit' ),
				'description' => $this->get_pro_description( 'woocommerce' ),
				'panel'       => 'kr_pro_features',
				'priority'    => 50,
			)
		);

		// Custom Widgets Section
		$wp_customize->add_section(
			'kr_custom_widgets',
			array(
				'title'       => __( 'Custom Widgets (Pro)', 'kr-toolkit' ),
				'description' => $this->get_pro_description( 'widgets' ),
				'panel'       => 'kr_pro_features',
				'priority'    => 60,
			)
		);
	}

	/**
	 * Get pro feature description
	 *
	 * @param string $feature Feature name.
	 * @return string
	 */
	private function get_pro_description( $feature ) {
		$descriptions = array(
			'header-builder' => sprintf(
				'<div class="kr-pro-feature">
					<p>%s</p>
					<ul>
						<li>✓ Drag & Drop Header Builder</li>
						<li>✓ Multiple Header Layouts</li>
						<li>✓ Sticky Header Options</li>
						<li>✓ Transparent Header</li>
						<li>✓ Mobile Header Customization</li>
					</ul>
					<a href="%s" target="_blank" class="button button-primary">%s</a>
				</div>',
				__( 'Build custom headers with an intuitive drag & drop interface.', 'kr-toolkit' ),
				'https://kerkeni.com/kr-theme-pro',
				__( 'Upgrade to Pro', 'kr-toolkit' )
			),
			'footer-builder' => sprintf(
				'<div class="kr-pro-feature">
					<p>%s</p>
					<ul>
						<li>✓ Drag & Drop Footer Builder</li>
						<li>✓ Multiple Footer Layouts</li>
						<li>✓ Footer Widgets Areas</li>
						<li>✓ Copyright Customization</li>
						<li>✓ Social Icons Integration</li>
					</ul>
					<a href="%s" target="_blank" class="button button-primary">%s</a>
				</div>',
				__( 'Create stunning footers with advanced customization options.', 'kr-toolkit' ),
				'https://kerkeni.com/kr-theme-pro',
				__( 'Upgrade to Pro', 'kr-toolkit' )
			),
			'typography' => sprintf(
				'<div class="kr-pro-feature">
					<p>%s</p>
					<ul>
						<li>✓ Google Fonts Integration (1000+ fonts)</li>
						<li>✓ Custom Font Upload</li>
						<li>✓ Typography Controls for All Elements</li>
						<li>✓ Font Display Optimization</li>
						<li>✓ Adobe Fonts Support</li>
					</ul>
					<a href="%s" target="_blank" class="button button-primary">%s</a>
				</div>',
				__( 'Complete control over typography with advanced font options.', 'kr-toolkit' ),
				'https://kerkeni.com/kr-theme-pro',
				__( 'Upgrade to Pro', 'kr-toolkit' )
			),
			'performance' => sprintf(
				'<div class="kr-pro-feature">
					<p>%s</p>
					<ul>
						<li>✓ Advanced Caching</li>
						<li>✓ Image Lazy Loading</li>
						<li>✓ CSS/JS Minification</li>
						<li>✓ Critical CSS Generation</li>
						<li>✓ Database Optimization</li>
					</ul>
					<a href="%s" target="_blank" class="button button-primary">%s</a>
				</div>',
				__( 'Boost your site speed with performance optimization features.', 'kr-toolkit' ),
				'https://kerkeni.com/kr-theme-pro',
				__( 'Upgrade to Pro', 'kr-toolkit' )
			),
			'woocommerce' => sprintf(
				'<div class="kr-pro-feature">
					<p>%s</p>
					<ul>
						<li>✓ Custom Product Layouts</li>
						<li>✓ Quick View</li>
						<li>✓ Wishlist Integration</li>
						<li>✓ Product Filters</li>
						<li>✓ Cart Drawer</li>
					</ul>
					<a href="%s" target="_blank" class="button button-primary">%s</a>
				</div>',
				__( 'Extended WooCommerce features for better online stores.', 'kr-toolkit' ),
				'https://kerkeni.com/kr-theme-pro',
				__( 'Upgrade to Pro', 'kr-toolkit' )
			),
			'widgets' => sprintf(
				'<div class="kr-pro-feature">
					<p>%s</p>
					<ul>
						<li>✓ Advanced Post Grid</li>
						<li>✓ Testimonials Slider</li>
						<li>✓ Team Members</li>
						<li>✓ Pricing Tables</li>
						<li>✓ Call to Action</li>
					</ul>
					<a href="%s" target="_blank" class="button button-primary">%s</a>
				</div>',
				__( 'Custom widgets to enhance your site with rich content.', 'kr-toolkit' ),
				'https://kerkeni.com/kr-theme-pro',
				__( 'Upgrade to Pro', 'kr-toolkit' )
			),
		);

		return isset( $descriptions[ $feature ] ) ? $descriptions[ $feature ] : '';
	}

	/**
	 * Enqueue customizer scripts
	 */
	public function enqueue_customizer_scripts() {
		wp_enqueue_style(
			'kr-customizer-extensions',
			plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/admin.css',
			array(),
			KR_TOOLKIT_VERSION
		);

		// Add custom CSS for pro features
		$custom_css = '
			.kr-pro-feature {
				padding: 15px;
				background: #f9f9f9;
				border-left: 3px solid #2271b1;
				margin: 10px 0;
			}
			.kr-pro-feature p {
				margin-bottom: 10px;
				font-weight: 500;
			}
			.kr-pro-feature ul {
				list-style: none;
				margin: 10px 0;
				padding: 0;
			}
			.kr-pro-feature ul li {
				margin: 5px 0;
				color: #46b450;
			}
			.kr-pro-feature .button-primary {
				margin-top: 10px;
			}
		';
		wp_add_inline_style( 'kr-customizer-extensions', $custom_css );
	}

	/**
	 * Add custom control types
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	public function add_custom_controls( $wp_customize ) {
		// Can be extended to add custom control types
		// For now, this is a placeholder for future enhancements
	}

	/**
	 * Get pro features list
	 *
	 * @return array
	 */
	public function get_pro_features() {
		return array(
			'header_builder'       => __( 'Header Builder', 'kr-toolkit' ),
			'footer_builder'       => __( 'Footer Builder', 'kr-toolkit' ),
			'advanced_typography'  => __( 'Advanced Typography', 'kr-toolkit' ),
			'performance'          => __( 'Performance Optimization', 'kr-toolkit' ),
			'woocommerce_extended' => __( 'WooCommerce Extended', 'kr-toolkit' ),
			'custom_widgets'       => __( 'Custom Widgets', 'kr-toolkit' ),
			'mega_menu'            => __( 'Mega Menu', 'kr-toolkit' ),
			'blog_layouts'         => __( 'Blog Layouts', 'kr-toolkit' ),
			'portfolio'            => __( 'Portfolio', 'kr-toolkit' ),
			'white_label'          => __( 'White Label', 'kr-toolkit' ),
		);
	}

	/**
	 * Check if a pro feature is available
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public function is_pro_feature_available( $feature ) {
		// Check if theme is pro version
		$theme = wp_get_theme();
		$is_pro = ( strpos( strtolower( $theme->get( 'Name' ) ), 'pro' ) !== false );

		// Check license
		$license_manager = new KR_License_Manager();
		$is_licensed = $license_manager->is_license_valid();

		return $is_pro && $is_licensed;
	}
}

// Initialize
new KR_Customizer_Extensions();
