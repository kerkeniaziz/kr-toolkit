<?php if ( ! defined( 'ABSPATH' ) ) exit;
require_once plugin_dir_path( __FILE__ ) . 'class-kr-base-widget.php';
class KR_Text_List_Widget extends KR_Base_Widget {
	public function get_title() { return esc_html__( 'KR Text List', 'kr-toolkit' ); }
	public function get_icon() { return 'eicon-bullet-list'; }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Items', 'kr-toolkit' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'item_text', array( 'label' => esc_html__( 'Text', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'List item', 'kr-toolkit' ) ) );
		$repeater->add_control( 'item_icon', array( 'label' => esc_html__( 'Icon', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-check', 'library' => 'fa-solid' ) ) );
		$this->add_control( 'items', array( 'label' => esc_html__( 'List Items', 'kr-toolkit' ), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => array( array( 'item_text' => esc_html__( 'Item 1', 'kr-toolkit' ) ), array( 'item_text' => esc_html__( 'Item 2', 'kr-toolkit' ) ) ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<ul class="kr-text-list" style="list-style: none; padding: 0; margin: 0;">
			<?php foreach ( $settings['items'] as $item ) : ?>
				<li style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
					<span style="color: #667eea; font-size: 1.25rem; flex-shrink: 0;">
						<?php \Elementor\Icons_Manager::render_icon( $item['item_icon'] ); ?>
					</span>
					<span style="color: #64748b; line-height: 1.6;"><?php echo esc_html( $item['item_text'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
	protected function content_template() {
		?>
		<ul class="kr-text-list" style="list-style: none; padding: 0; margin: 0;">
			<# _.each( settings.items, function( item ) { #>
				<li style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
					<span style="color: #667eea; font-size: 1.25rem;">{{{ elementor.getIconsHTML( item.item_icon ) }}}</span>
					<span style="color: #64748b;">{{{ item.item_text }}}</span>
				</li>
			<# }); #>
		</ul>
		<?php
	}
}
