<?php
/**
 * KR Theme - WooCommerce Elementor Widgets
 *
 * Additional WooCommerce widgets for Elementor
 *
 * @package KR_Theme
 * @since 1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Products List Widget
 */
if ( class_exists( 'Elementor\Widget_Base' ) ) {

	class KR_Products_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'kr-products';
		}

		public function get_title() {
			return esc_html__( 'KR - Products List', 'kr-theme' );
		}

		public function get_icon() {
			return 'eicon-products';
		}

		public function get_categories() {
			return array( 'general' );
		}

		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				array( 'label' => esc_html__( 'Products', 'kr-theme' ) )
			);

			$this->add_control(
				'number',
				array(
					'label'       => esc_html__( 'Number of Products', 'kr-theme' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'default'     => 6,
					'min'         => 1,
					'max'         => 100,
					'step'        => 1,
				)
			);

			$this->add_control(
				'columns',
				array(
					'label'   => esc_html__( 'Columns', 'kr-theme' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 3,
					'options' => array(
						1 => esc_html__( '1', 'kr-theme' ),
						2 => esc_html__( '2', 'kr-theme' ),
						3 => esc_html__( '3', 'kr-theme' ),
						4 => esc_html__( '4', 'kr-theme' ),
						6 => esc_html__( '6', 'kr-theme' ),
					),
				)
			);

			$this->add_control(
				'orderby',
				array(
					'label'   => esc_html__( 'Order By', 'kr-theme' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'date',
					'options' => array(
						'date'       => esc_html__( 'Date', 'kr-theme' ),
						'price'      => esc_html__( 'Price', 'kr-theme' ),
						'popularity' => esc_html__( 'Popularity', 'kr-theme' ),
						'rating'     => esc_html__( 'Rating', 'kr-theme' ),
						'title'      => esc_html__( 'Title', 'kr-theme' ),
					),
				)
			);

			$this->add_control(
				'order',
				array(
					'label'   => esc_html__( 'Order', 'kr-theme' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => array(
						'asc'  => esc_html__( 'Ascending', 'kr-theme' ),
						'desc' => esc_html__( 'Descending', 'kr-theme' ),
					),
				)
			);

			$this->add_control(
				'category',
				array(
					'label'       => esc_html__( 'Product Category', 'kr-theme' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Leave empty for all', 'kr-theme' ),
					'description' => esc_html__( 'Enter category slug', 'kr-theme' ),
				)
			);

			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();

			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => intval( $settings['number'] ),
				'orderby'        => $settings['orderby'],
				'order'          => strtoupper( $settings['order'] ),
			);

			if ( ! empty( $settings['category'] ) ) {
				$args['product_cat'] = sanitize_text_field( $settings['category'] );
			}

			$products = new \WP_Query( $args );

			if ( $products->have_posts() ) {
				echo '<div class="kr-products-list" style="display: grid; grid-template-columns: repeat(' . intval( $settings['columns'] ) . ', 1fr); gap: 2rem;">';

				while ( $products->have_posts() ) {
					$products->the_post();
					wc_get_template_part( 'content', 'product' );
				}

				echo '</div>';
				wp_reset_postdata();
			} else {
				echo '<p>' . esc_html__( 'No products found', 'kr-theme' ) . '</p>';
			}
		}
	}

	\Elementor\Plugin::instance()->widgets_manager->register( new KR_Products_Widget() );
}

/**
 * Featured Products Widget
 */
if ( class_exists( 'Elementor\Widget_Base' ) ) {

	class KR_Featured_Products_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'kr-featured-products';
		}

		public function get_title() {
			return esc_html__( 'KR - Featured Products', 'kr-theme' );
		}

		public function get_icon() {
			return 'eicon-star';
		}

		public function get_categories() {
			return array( 'general' );
		}

		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				array( 'label' => esc_html__( 'Featured Products', 'kr-theme' ) )
			);

			$this->add_control(
				'number',
				array(
					'label'       => esc_html__( 'Number of Products', 'kr-theme' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'default'     => 4,
					'min'         => 1,
					'max'         => 100,
				)
			);

			$this->add_control(
				'columns',
				array(
					'label'   => esc_html__( 'Columns', 'kr-theme' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 4,
					'options' => array(
						1 => esc_html__( '1', 'kr-theme' ),
						2 => esc_html__( '2', 'kr-theme' ),
						3 => esc_html__( '3', 'kr-theme' ),
						4 => esc_html__( '4', 'kr-theme' ),
					),
				)
			);

			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();

			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => intval( $settings['number'] ),
				'meta_key'       => '_featured',
				'meta_value'     => 'yes',
			);

			$products = new \WP_Query( $args );

			if ( $products->have_posts() ) {
				echo '<div class="kr-featured-products" style="display: grid; grid-template-columns: repeat(' . intval( $settings['columns'] ) . ', 1fr); gap: 2rem;">';

				while ( $products->have_posts() ) {
					$products->the_post();
					wc_get_template_part( 'content', 'product' );
				}

				echo '</div>';
				wp_reset_postdata();
			} else {
				echo '<p>' . esc_html__( 'No featured products found', 'kr-theme' ) . '</p>';
			}
		}
	}

	\Elementor\Plugin::instance()->widgets_manager->register( new KR_Featured_Products_Widget() );
}

/**
 * Product Categories Widget
 */
if ( class_exists( 'Elementor\Widget_Base' ) ) {

	class KR_Product_Categories_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'kr-product-categories';
		}

		public function get_title() {
			return esc_html__( 'KR - Product Categories', 'kr-theme' );
		}

		public function get_icon() {
			return 'eicon-product-categories';
		}

		public function get_categories() {
			return array( 'general' );
		}

		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				array( 'label' => esc_html__( 'Categories', 'kr-theme' ) )
			);

			$this->add_control(
				'hide_empty',
				array(
					'label'       => esc_html__( 'Hide Empty Categories', 'kr-theme' ),
					'type'        => \Elementor\Controls_Manager::SWITCHER,
					'default'     => 'yes',
				)
			);

			$this->add_control(
				'columns',
				array(
					'label'   => esc_html__( 'Columns', 'kr-theme' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 3,
					'options' => array(
						1 => esc_html__( '1', 'kr-theme' ),
						2 => esc_html__( '2', 'kr-theme' ),
						3 => esc_html__( '3', 'kr-theme' ),
						4 => esc_html__( '4', 'kr-theme' ),
					),
				)
			);

			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();

			$args = array(
				'taxonomy' => 'product_cat',
				'hide_empty' => ( 'yes' === $settings['hide_empty'] ),
			);

			$categories = get_terms( $args );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				echo '<div class="kr-product-categories" style="display: grid; grid-template-columns: repeat(' . intval( $settings['columns'] ) . ', 1fr); gap: 2rem;">';

				foreach ( $categories as $category ) {
					$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
					$image_url = wp_get_attachment_image_url( $thumbnail_id, 'medium' );

					echo '<div class="kr-category-item" style="text-align: center; text-decoration: none;">';
					
					if ( $image_url ) {
						echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $category->name ) . '" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">';
					}

					echo '<a href="' . esc_url( get_term_link( $category ) ) . '" style="color: #667eea; font-weight: 600; text-decoration: none;">' . esc_html( $category->name ) . '</a>';
					echo '</div>';
				}

				echo '</div>';
			} else {
				echo '<p>' . esc_html__( 'No categories found', 'kr-theme' ) . '</p>';
			}
		}
	}

	\Elementor\Plugin::instance()->widgets_manager->register( new KR_Product_Categories_Widget() );
}
