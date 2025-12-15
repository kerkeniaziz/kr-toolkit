<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';
class KR_Testimonial_Widget extends KR_Base_Widget {
	public function get_title() { return esc_html__( 'KR Testimonial', 'kr-toolkit' ); }
	public function get_icon() { return 'eicon-testimonial'; }
	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'Content', 'kr-toolkit' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'quote', array( 'label' => esc_html__( 'Quote', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => esc_html__( 'This product is amazing!', 'kr-toolkit' ) ) );
		$this->add_control( 'author', array( 'label' => esc_html__( 'Author', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'John Doe', 'kr-toolkit' ) ) );
		$this->add_control( 'image', array( 'label' => esc_html__( 'Avatar', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::MEDIA ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="kr-testimonial" style="padding: 2rem; background: #f8fafc; border-radius: 8px; text-align: center;">
			<?php if ( ! empty( $settings['image']['url'] ) ) : ?>
				<img src="<?php echo esc_url( $settings['image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['author'] ); ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
			<?php endif; ?>
			<p style="font-style: italic; color: #64748b; margin-bottom: 1.5rem;">"<?php echo wp_kses_post( $settings['quote'] ); ?>"</p>
			<h4 style="margin: 0; color: #1e293b; font-weight: 600;"><?php echo esc_html( $settings['author'] ); ?></h4>
		</div>
		<?php
	}
	protected function content_template() {
		?>
		<div class="kr-testimonial" style="padding: 2rem; background: #f8fafc; border-radius: 8px; text-align: center;">
			<# if ( settings.image.url ) { #>
				<img src="{{{ settings.image.url }}}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
			<# } #>
			<p style="font-style: italic; color: #64748b; margin-bottom: 1.5rem;">"{{{ settings.quote }}}"</p>
			<h4 style="margin: 0; color: #1e293b; font-weight: 600;">{{{ settings.author }}}</h4>
		</div>
		<?php
	}
}
