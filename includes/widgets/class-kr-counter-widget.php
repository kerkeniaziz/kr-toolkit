<?php
/**
 * KR Counter Widget
 *
 * @since 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';

class KR_Counter_Widget extends KR_Base_Widget {

	public function get_title() {
		return esc_html__( 'KR Counter', 'kr-toolkit' );
	}

	public function get_icon() {
		return 'eicon-counter';
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
			'number',
			array(
				'label'       => esc_html__( 'Number', 'kr-toolkit' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 100,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'kr-toolkit' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Projects Done', 'kr-toolkit' ),
			)
		);

		$this->add_control(
			'prefix',
			array(
				'label' => esc_html__( 'Prefix', 'kr-toolkit' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'suffix',
			array(
				'label' => esc_html__( 'Suffix', 'kr-toolkit' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
				'default' => '+',
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
			'number_color',
			array(
				'label'     => esc_html__( 'Number Color', 'kr-toolkit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#667eea',
				'selectors' => array(
					'{{WRAPPER}} .kr-counter-number' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .kr-counter-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$prefix = ! empty( $settings['prefix'] ) ? esc_html( $settings['prefix'] ) : '';
		$suffix = ! empty( $settings['suffix'] ) ? esc_html( $settings['suffix'] ) : '';
		?>
		<div class="kr-counter" style="text-align: center;">
			<div class="kr-counter-number" style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
				<?php echo esc_html( $prefix . $settings['number'] . $suffix ); ?>
			</div>
			<h4 class="kr-counter-title" style="margin: 0;">
				<?php echo esc_html( $settings['title'] ); ?>
			</h4>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kr-counter" style="text-align: center;">
			<div class="kr-counter-number" style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
				{{{ settings.prefix }}}{{{ settings.number }}}{{{ settings.suffix }}}
			</div>
			<h4 class="kr-counter-title" style="margin: 0;">
				{{{ settings.title }}}
			</h4>
		</div>
		<?php
	}
}
