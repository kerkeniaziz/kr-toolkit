<?php
/**
 * Elementor Navigation Menu Widget
 *
 * @package KR_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * KR_Navigation_Menu_Widget
 */
class KR_Navigation_Menu_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'kr-navigation-menu';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Navigation Menu', 'kr-toolkit' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
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

		$menus = wp_get_nav_menus();
		$menu_options = [];
		foreach ( $menus as $menu ) {
			$menu_options[ $menu->slug ] = $menu->name;
		}

		$this->add_control(
			'menu',
			[
				'label'   => esc_html__( 'Select Menu', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $menu_options,
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
					'{{WRAPPER}} .kr-nav-menu' => 'text-align: {{VALUE}};',
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

		if ( ! empty( $settings['menu'] ) ) {
			wp_nav_menu( [
				'menu'       => $settings['menu'],
				'container'  => 'nav',
				'container_class' => 'kr-nav-menu',
				'fallback_cb'     => false,
			] );
		}
	}
}
