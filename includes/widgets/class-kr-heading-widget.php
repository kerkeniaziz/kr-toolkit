<?php
/**
 * KR Heading Widget
 *
 * @since 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';

class KR_Heading_Widget extends KR_Base_Widget {

	public function get_title() {
		return esc_html__( 'KR Heading', 'kr-toolkit' );
	}

	public function get_icon() {
		return 'eicon-heading';
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
			'title',
			array(
				'label'       => esc_html__( 'Title', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your title', 'kr-toolkit' ),
				'default'     => esc_html__( 'This is a heading', 'kr-toolkit' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your subtitle', 'kr-toolkit' ),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
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
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => array(
					'{{WRAPPER}} .kr-heading-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'kr-toolkit' ),
				'selector' => '{{WRAPPER}} .kr-heading-title',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle Color', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .kr-heading-subtitle' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Subtitle Typography', 'kr-toolkit' ),
				'selector' => '{{WRAPPER}} .kr-heading-subtitle',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag      = ! empty( $settings['html_tag'] ) ? tag_escape( $settings['html_tag'] ) : 'h2';
		$align    = ! empty( $settings['alignment'] ) ? sanitize_text_field( $settings['alignment'] ) : 'left';
		?>
		<div class="kr-heading" style="text-align: <?php echo esc_attr( $align ); ?>;">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_attr( $tag ); ?> class="kr-heading-title">
					<?php echo wp_kses_post( $settings['title'] ); ?>
				</<?php echo esc_attr( $tag ); ?>>
			<?php endif; ?>
			<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
				<p class="kr-heading-subtitle">
					<?php echo wp_kses_post( $settings['subtitle'] ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kr-heading" style="text-align: {{{ settings.alignment }}};">
			<# if ( settings.title ) { #>
				<{{{ settings.html_tag }}} class="kr-heading-title">{{{ settings.title }}}</{{{ settings.html_tag }}}>
			<# } #>
			<# if ( settings.subtitle ) { #>
				<p class="kr-heading-subtitle">{{{ settings.subtitle }}}</p>
			<# } #>
		</div>
		<?php
	}
}
