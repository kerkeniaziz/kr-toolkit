<?php
/**
 * KR Button Widget
 *
 * @since 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';

class KR_Button_Widget extends KR_Base_Widget {

	public function get_title() {
		return esc_html__( 'KR Button', 'kr-toolkit' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'kr-toolkit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Click me', 'kr-toolkit' ),
				'default'     => esc_html__( 'Click me', 'kr-toolkit' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label'       => esc_html__( 'Link', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://example.com',
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label'   => esc_html__( 'Style', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => array(
					'primary'   => esc_html__( 'Primary', 'kr-toolkit' ),
					'secondary' => esc_html__( 'Secondary', 'kr-toolkit' ),
					'outline'   => esc_html__( 'Outline', 'kr-toolkit' ),
				),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'kr-toolkit' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'kr-toolkit' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'kr-toolkit' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'left',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Style', 'kr-toolkit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button Color', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#667eea',
				'selectors' => array(
					'{{WRAPPER}} .kr-button.primary' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'kr-toolkit' ),
				'selector' => '{{WRAPPER}} .kr-button',
			)
		);

		$this->add_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'kr-toolkit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => '0.875',
					'right'    => '2',
					'bottom'   => '0.875',
					'left'     => '2',
					'unit'     => 'rem',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .kr-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$align    = ! empty( $settings['alignment'] ) ? sanitize_text_field( $settings['alignment'] ) : 'left';
		$style    = ! empty( $settings['button_style'] ) ? sanitize_text_field( $settings['button_style'] ) : 'primary';
		$url      = ! empty( $settings['button_url']['url'] ) ? esc_url( $settings['button_url']['url'] ) : '#';
		$target   = ! empty( $settings['button_url']['is_external'] ) ? '_blank' : '_self';
		?>
		<div class="kr-button-wrapper" style="text-align: <?php echo esc_attr( $align ); ?>;">
			<a href="<?php echo esc_url( $url ); ?>" 
			   target="<?php echo esc_attr( $target ); ?>" 
			   class="kr-button <?php echo esc_attr( $style ); ?>"
			   style="display: inline-block; text-decoration: none; border-radius: 50px; border: 2px solid; transition: all 0.3s ease;">
				<?php echo esc_html( $settings['button_text'] ); ?>
			</a>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kr-button-wrapper" style="text-align: {{{ settings.alignment }}};">
			<a href="{{{ settings.button_url.url }}}" 
			   target="{{{ settings.button_url.is_external ? '_blank' : '_self' }}}" 
			   class="kr-button {{{ settings.button_style }}}">
				{{{ settings.button_text }}}
			</a>
		</div>
		<?php
	}
}
