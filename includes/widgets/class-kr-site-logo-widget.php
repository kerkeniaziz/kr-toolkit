<?php
/**
 * Elementor Site Logo Widget
 *
 * @package KR_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * KR_Site_Logo_Widget
 */
class KR_Site_Logo_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'kr-site-logo';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Site Logo', 'kr-toolkit' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-site-logo';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'krtheme-header' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'kr-toolkit' ),
			]
		);

		$this->add_control(
			'logo_type',
			[
				'label'   => esc_html__( 'Logo Type', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default Logo', 'kr-toolkit' ),
					'custom'  => esc_html__( 'Custom Logo', 'kr-toolkit' ),
				],
			]
		);

		$this->add_control(
			'custom_logo',
			[
				'label'     => esc_html__( 'Choose Logo', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'logo_type' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__( 'Alignment', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'kr-toolkit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'kr-toolkit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'kr-toolkit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .kr-site-logo' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$logo_url = '';

		if ( 'custom' === $settings['logo_type'] && ! empty( $settings['custom_logo']['url'] ) ) {
			$logo_url = $settings['custom_logo']['url'];
		} else {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
			} else {
				$logo_url = get_template_directory_uri() . '/assets/images/logo.png';
			}
		}
		?>
		<div class="kr-site-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			</a>
		</div>
		<?php
	}
}
