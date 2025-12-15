<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';
class KR_Divider_Widget extends KR_Base_Widget {
	public function get_title() { return esc_html__( 'KR Divider', 'kr-toolkit' ); }
	public function get_icon() { return 'eicon-divider'; }
	protected function register_controls() {
		$this->start_controls_section( 'style', array( 'label' => esc_html__( 'Divider', 'kr-toolkit' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'color', array( 'label' => esc_html__( 'Color', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#e2e8f0', 'selectors' => array( '{{WRAPPER}} .kr-divider' => 'border-top-color: {{VALUE}}' ) ) );
		$this->add_control( 'height', array( 'label' => esc_html__( 'Height (px)', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => array( 'size' => 1 ), 'range' => array( 'px' => array( 'min' => 1, 'max' => 20 ) ), 'selectors' => array( '{{WRAPPER}} .kr-divider' => 'border-top-width: {{SIZE}}px' ) ) );
		$this->add_control( 'margin', array( 'label' => esc_html__( 'Spacing', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => array( 'size' => 20 ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .kr-divider' => 'margin: {{SIZE}}px 0' ) ) );
		$this->end_controls_section();
	}
	protected function render() { ?>
		<div class="kr-divider" style="border-top: 1px solid #e2e8f0; margin: 20px 0;"></div>
		<?php
	}
}
