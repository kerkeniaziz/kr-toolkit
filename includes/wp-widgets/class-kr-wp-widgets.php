<?php
/**
 * KR WordPress Widgets Manager
 *
 * Custom WordPress widgets for sidebars and widget areas
 *
 * @since 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KR_WP_Widgets {

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	public function register_widgets() {
		// Register Navigation Menu Widget
		register_widget( 'KR_Widget_Navigation_Menu' );
		
		// Register Post List Widget
		register_widget( 'KR_Widget_Post_List' );
		
		// Register Recent Posts Widget
		register_widget( 'KR_Widget_Recent_Posts' );
	}
}

/**
 * KR Navigation Menu Widget
 */
class KR_Widget_Navigation_Menu extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'kr_nav_menu',
			esc_html__( 'KR Navigation Menu', 'kr-toolkit' ),
			array( 'description' => esc_html__( 'Display a custom menu', 'kr-toolkit' ) )
		);
	}

	public function widget( $args, $instance ) {
		$menu = ! empty( $instance['menu'] ) ? $instance['menu'] : 0;
		if ( ! $menu ) return;

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		wp_nav_menu( array(
			'menu'       => $menu,
			'fallback_cb' => '__return_false',
		) );

		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		$menus = wp_get_nav_menus();
		$menu = ! empty( $instance['menu'] ) ? $instance['menu'] : 0;
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'kr-toolkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'menu' ) ); ?>"><?php esc_html_e( 'Select Menu:', 'kr-toolkit' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'menu' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'menu' ) ); ?>">
				<option value="">--- Select Menu ---</option>
				<?php foreach ( $menus as $m ) : ?>
					<option value="<?php echo intval( $m->term_id ); ?>" <?php selected( $menu, $m->term_id ); ?>><?php echo esc_html( $m->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['menu'] = ! empty( $new_instance['menu'] ) ? intval( $new_instance['menu'] ) : 0;
		return $instance;
	}
}

/**
 * KR Post List Widget
 */
class KR_Widget_Post_List extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'kr_post_list',
			esc_html__( 'KR Post List', 'kr-toolkit' ),
			array( 'description' => esc_html__( 'Display a list of posts', 'kr-toolkit' ) )
		);
	}

	public function widget( $args, $instance ) {
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'kr-toolkit' );
		$number    = ! empty( $instance['number'] ) ? intval( $instance['number'] ) : 5;
		$show_date = ! empty( $instance['show_date'] ) ? $instance['show_date'] : false;

		echo wp_kses_post( $args['before_widget'] );
		echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'] );

		$posts = get_posts( array(
			'numberposts' => $number,
			'orderby'     => 'date',
			'order'       => 'DESC',
		) );

		if ( ! empty( $posts ) ) {
			echo '<ul style="list-style: none; padding: 0; margin: 0;">';
			foreach ( $posts as $post ) {
				echo '<li style="padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">';
				echo '<a href="' . esc_url( get_permalink( $post ) ) . '" style="color: #667eea; text-decoration: none; font-weight: 500;">' . esc_html( $post->post_title ) . '</a>';
				if ( $show_date ) {
					echo '<br><small style="color: #64748b; font-size: 0.875rem;">' . esc_html( get_the_date( 'M d, Y', $post ) ) . '</small>';
				}
				echo '</li>';
			}
			echo '</ul>';
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'kr-toolkit' );
		$number    = ! empty( $instance['number'] ) ? intval( $instance['number'] ) : 5;
		$show_date = ! empty( $instance['show_date'] ) ? $instance['show_date'] : false;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'kr-toolkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of Posts:', 'kr-toolkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" value="<?php echo intval( $number ); ?>" min="1" max="20">
		</p>
		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" value="1" <?php checked( $show_date, 1 ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>"><?php esc_html_e( 'Show post date', 'kr-toolkit' ); ?></label>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['number'] = ! empty( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;
		$instance['show_date'] = ! empty( $new_instance['show_date'] ) ? 1 : 0;
		return $instance;
	}
}

/**
 * KR Recent Posts Widget (Alternative)
 */
class KR_Widget_Recent_Posts extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'kr_recent_posts',
			esc_html__( 'KR Recent Posts', 'kr-toolkit' ),
			array( 'description' => esc_html__( 'Display recent posts with thumbnails', 'kr-toolkit' ) )
		);
	}

	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'kr-toolkit' );
		$number = ! empty( $instance['number'] ) ? intval( $instance['number'] ) : 5;

		echo wp_kses_post( $args['before_widget'] );
		echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'] );

		$posts = get_posts( array(
			'numberposts' => $number,
			'orderby'     => 'date',
			'order'       => 'DESC',
		) );

		if ( ! empty( $posts ) ) {
			echo '<div style="display: grid; gap: 1rem;">';
			foreach ( $posts as $post ) {
				echo '<div style="display: flex; gap: 0.75rem;">';
				if ( has_post_thumbnail( $post ) ) {
					echo '<div style="flex-shrink: 0; width: 60px; height: 60px;">';
					echo get_the_post_thumbnail( $post, array( 60, 60 ), array( 'style' => 'width: 100%; height: 100%; object-fit: cover; border-radius: 4px;' ) );
					echo '</div>';
				}
				echo '<div style="flex: 1; min-width: 0;">';
				echo '<a href="' . esc_url( get_permalink( $post ) ) . '" style="color: #667eea; text-decoration: none; font-weight: 500; display: block; margin-bottom: 0.25rem;">' . esc_html( $post->post_title ) . '</a>';
				echo '<small style="color: #64748b; font-size: 0.75rem;">' . esc_html( get_the_date( 'M d, Y', $post ) ) . '</small>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'kr-toolkit' );
		$number = ! empty( $instance['number'] ) ? intval( $instance['number'] ) : 5;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'kr-toolkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of Posts:', 'kr-toolkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" value="<?php echo intval( $number ); ?>" min="1" max="20">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['number'] = ! empty( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;
		return $instance;
	}
}
