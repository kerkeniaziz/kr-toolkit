<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';
class KR_Social_Widget extends KR_Base_Widget {
	public function get_title() { return esc_html__( 'KR Social Links', 'kr-toolkit' ); }
	public function get_icon() { return 'eicon-social-icons'; }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Social Links', 'kr-toolkit' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'platform', array( 'label' => esc_html__( 'Platform', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => array( 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn' ), 'default' => 'facebook' ) );
		$repeater->add_control( 'url', array( 'label' => esc_html__( 'URL', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::URL ) );
		$this->add_control( 'social_links', array( 'label' => esc_html__( 'Social Links', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => array( array( 'platform' => 'facebook' ) ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$icons = array( 'facebook' => 'fab fa-facebook', 'twitter' => 'fab fa-twitter', 'instagram' => 'fab fa-instagram', 'linkedin' => 'fab fa-linkedin' );
		?>
		<div class="kr-social-links" style="display: flex; gap: 1rem;">
			<?php foreach ( $settings['social_links'] as $link ) :
				$icon = ! empty( $icons[ $link['platform'] ] ) ? $icons[ $link['platform'] ] : 'fab fa-link';
				$url = ! empty( $link['url']['url'] ) ? esc_url( $link['url']['url'] ) : '#';
			?>
				<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #667eea; color: white; border-radius: 50%; text-decoration: none; transition: all 0.3s ease;">
					<i class="<?php echo esc_attr( $icon ); ?>" style="font-size: 1rem;"></i>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
	protected function content_template() {
		?>
		<div class="kr-social-links" style="display: flex; gap: 1rem;">
			<# _.each( settings.social_links, function( link ) { #>
				<a href="{{{ link.url.url }}}" target="_blank" rel="noopener" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #667eea; color: white; border-radius: 50%;">
					[Social Icon]
				</a>
			<# }); #>
		</div>
		<?php
	}
}
