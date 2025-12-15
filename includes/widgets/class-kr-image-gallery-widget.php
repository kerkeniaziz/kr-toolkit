<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';
class KR_Image_Gallery_Widget extends KR_Base_Widget {
	public function get_title() { return esc_html__( 'KR Image Gallery', 'kr-toolkit' ); }
	public function get_icon() { return 'eicon-gallery-grid'; }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Gallery', 'kr-toolkit' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'gallery', array( 'label' => esc_html__( 'Add Images', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::GALLERY ) );
		$this->add_control( 'columns', array( 'label' => esc_html__( 'Columns', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '3', 'options' => array( '2' => '2', '3' => '3', '4' => '4' ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$cols = ! empty( $settings['columns'] ) ? intval( $settings['columns'] ) : 3;
		if ( empty( $settings['gallery'] ) ) return;
		?>
		<div class="kr-gallery" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr( $cols ); ?>, 1fr); gap: 1.5rem;">
			<?php foreach ( $settings['gallery'] as $image ) : ?>
				<div class="gallery-item">
					<img src="<?php echo esc_url( $image['url'] ); ?>" alt="Gallery image" style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px;">
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
	protected function content_template() {
		?>
		<div class="kr-gallery" style="display: grid; grid-template-columns: repeat(<# print( settings.columns || 3 ) #>, 1fr); gap: 1.5rem;">
			<# _.each( settings.gallery, function( image ) { #>
				<img src="{{{ image.url }}}" style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px;">
			<# }); #>
		</div>
		<?php
	}
}
