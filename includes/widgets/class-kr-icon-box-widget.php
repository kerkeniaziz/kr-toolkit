<?php
/**
 * KR Icon Box Widget
 *
 * @since 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';

class KR_Icon_Box_Widget extends KR_Base_Widget {

	public function get_title() {
		return esc_html__( 'KR Icon Box', 'kr-toolkit' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
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
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Feature Title', 'kr-toolkit' ),
				'default'     => esc_html__( 'Feature Title', 'kr-toolkit' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Description', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Feature description', 'kr-toolkit' ),
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
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#667eea',
				'selectors' => array(
					'{{WRAPPER}} .kr-icon-box-icon' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'kr-toolkit' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px'  => array( 'min' => 10, 'max' => 200 ),
					'rem' => array( 'min' => 0.625, 'max' => 12.5 ),
				),
				'default'    => array(
					'size' => 2.5,
					'unit' => 'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .kr-icon-box-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => array(
					'{{WRAPPER}} .kr-icon-box-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="kr-icon-box" style="text-align: center;">
			<div class="kr-icon-box-icon" style="margin-bottom: 1rem;">
				<?php \Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
			</div>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h3 class="kr-icon-box-title" style="margin-bottom: 0.5rem;">
					<?php echo esc_html( $settings['title'] ); ?>
				</h3>
			<?php endif; ?>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="kr-icon-box-description" style="color: #64748b; line-height: 1.6;">
					<?php echo wp_kses_post( $settings['description'] ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kr-icon-box" style="text-align: center;">
			<div class="kr-icon-box-icon" style="margin-bottom: 1rem;">
				<# if ( settings.icon.value ) { #>
					{{{ elementor.getIconsHTML( settings.icon ) }}}
				<# } #>
			</div>
			<# if ( settings.title ) { #>
				<h3 class="kr-icon-box-title" style="margin-bottom: 0.5rem;">
					{{{ settings.title }}}
				</h3>
			<# } #>
			<# if ( settings.description ) { #>
				<p class="kr-icon-box-description">
					{{{ settings.description }}}
				</p>
			<# } #>
		</div>
		<?php
	}
}
