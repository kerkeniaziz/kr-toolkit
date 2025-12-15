<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';
class KR_Team_Member_Widget extends KR_Base_Widget {
	public function get_title() { return esc_html__( 'KR Team Member', 'kr-toolkit' ); }
	public function get_icon() { return 'eicon-person'; }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'kr-toolkit' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'image', array( 'label' => esc_html__( 'Photo', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::MEDIA ) );
		$this->add_control( 'name', array( 'label' => esc_html__( 'Name', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'Team Member', 'kr-toolkit' ) ) );
		$this->add_control( 'position', array( 'label' => esc_html__( 'Position', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'Designer', 'kr-toolkit' ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="kr-team-member" style="text-align: center;">
			<?php if ( ! empty( $settings['image']['url'] ) ) : ?>
				<img src="<?php echo esc_url( $settings['image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['name'] ); ?>" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
			<?php endif; ?>
			<h4 style="margin: 0.5rem 0 0.25rem; color: #1e293b; font-weight: 600;"><?php echo esc_html( $settings['name'] ); ?></h4>
			<p style="margin: 0; color: #64748b; font-size: 0.875rem;"><?php echo esc_html( $settings['position'] ); ?></p>
		</div>
		<?php
	}
	protected function content_template() {
		?>
		<div class="kr-team-member" style="text-align: center;">
			<# if ( settings.image.url ) { #>
				<img src="{{{ settings.image.url }}}" style="width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
			<# } #>
			<h4 style="margin: 0.5rem 0 0.25rem; color: #1e293b; font-weight: 600;">{{{ settings.name }}}</h4>
			<p style="margin: 0; color: #64748b; font-size: 0.875rem;">{{{ settings.position }}}</p>
		</div>
		<?php
	}
}
