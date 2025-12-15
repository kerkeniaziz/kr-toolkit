<?php
/**
 * KR Toolkit Elementor Widgets Manager
 *
 * Simplified and production-ready widget collection for kr-theme
 *
 * @since 1.2.8
 * @version 1.2.8
 * @author KR Theme <support@krtheme.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Elementor_Widgets' ) ) {
	class KR_Elementor_Widgets {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Only load if Elementor is active
			if ( ! did_action( 'elementor/loaded' ) ) {
				return;
			}

			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ), 99 );
			add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_categories' ) );
			add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_widget_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widget_scripts' ) );
		}

		/**
		 * Register Widget Category
		 */
		public function register_widget_categories( $elements_manager ) {
			$elements_manager->add_category(
				'krtheme',
				array(
					'title' => esc_html__( 'KR Theme', 'kr-toolkit' ),
					'icon'  => 'fa fa-plug',
				)
			);
		}

		/**
		 * Register Widgets
		 */
		public function register_widgets( $widgets_manager ) {
			// List of widgets to register
			$widgets = array(
				'heading',
				'icon-box',
				'button',
				'testimonial',
				'counter',
				'team-member',
				'text-list',
				'image-gallery',
				'divider',
				'social',
			);

			foreach ( $widgets as $widget ) {
				$file = plugin_dir_path( __FILE__ ) . 'widgets/class-kr-' . str_replace( '_', '-', $widget ) . '-widget.php';
				if ( file_exists( $file ) ) {
					require_once $file;
					$class_name = 'KR_' . str_replace( '-', '_', $widget ) . '_Widget';
					$class_name = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $class_name ) ) );
					
					if ( class_exists( $class_name ) ) {
						$widgets_manager->register( new $class_name() );
					}
				}
			}
		}

		/**
		 * Enqueue Widget Styles
		 */
		public function enqueue_widget_styles() {
			wp_enqueue_style( 'kr-widgets', plugin_dir_url( __FILE__ ) . '../admin/css/widgets.css', array(), KR_TOOLKIT_VERSION );
		}

		/**
		 * Enqueue Widget Scripts
		 */
		public function enqueue_widget_scripts() {
			wp_enqueue_script( 'kr-widgets', plugin_dir_url( __FILE__ ) . '../admin/js/widgets.js', array( 'jquery' ), KR_TOOLKIT_VERSION, true );
		}
	}
}

// Initialize widgets manager
if ( did_action( 'elementor/loaded' ) || doing_action( 'elementor/loaded' ) ) {
	new KR_Elementor_Widgets();
} else {
	add_action( 'elementor/loaded', function() {
		new KR_Elementor_Widgets();
	} );
}
