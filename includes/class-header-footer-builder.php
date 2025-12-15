<?php
/**
 * KR Toolkit Header & Footer Builder Class
 *
 * @since 1.2.8
 * @version 1.2.8
 * @author KR Theme <support@krtheme.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'KR_Header_Footer_Builder' ) ) {
	class KR_Header_Footer_Builder {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ) );
			add_action( 'init', array( $this, 'register_taxonomies' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			
			// Elementor support
			add_action( 'elementor/documents/register', array( $this, 'register_elementor_documents' ) );
		}

		/**
		 * Register custom post types
		 */
		public function register_post_types() {
			// Header Builder Post Type
			register_post_type( 'kr_header', array(
				'labels' => array(
					'name'          => esc_html__( 'Headers', 'kr-toolkit' ),
					'singular_name' => esc_html__( 'Header', 'kr-toolkit' ),
					'add_new'       => esc_html__( 'Add New Header', 'kr-toolkit' ),
					'add_new_item'  => esc_html__( 'Add New Header', 'kr-toolkit' ),
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'supports'           => array( 'title', 'editor', 'custom-fields' ),
				'capability_type'    => 'post',
			) );

			// Footer Builder Post Type
			register_post_type( 'kr_footer', array(
				'labels' => array(
					'name'          => esc_html__( 'Footers', 'kr-toolkit' ),
					'singular_name' => esc_html__( 'Footer', 'kr-toolkit' ),
					'add_new'       => esc_html__( 'Add New Footer', 'kr-toolkit' ),
					'add_new_item'  => esc_html__( 'Add New Footer', 'kr-toolkit' ),
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'supports'           => array( 'title', 'editor', 'custom-fields' ),
				'capability_type'    => 'post',
			) );
		}

		/**
		 * Register taxonomies
		 */
		public function register_taxonomies() {
			// Register taxonomy for headers if needed
			register_taxonomy( 'kr_header_category', 'kr_header', array(
				'labels' => array(
					'name' => esc_html__( 'Header Categories', 'kr-toolkit' ),
				),
				'public'            => false,
				'show_ui'           => false,
				'show_in_menu'      => false,
				'show_in_rest'      => true,
			) );
		}

		/**
		 * Add admin menus
		 */
		public function add_admin_menus() {
			add_submenu_page(
				'kr-toolkit',
				esc_html__( 'Header Builder', 'kr-toolkit' ),
				esc_html__( 'Header Builder', 'kr-toolkit' ),
				'manage_options',
				'kr-headers',
				array( $this, 'render_headers_page' )
			);

			add_submenu_page(
				'kr-toolkit',
				esc_html__( 'Footer Builder', 'kr-toolkit' ),
				esc_html__( 'Footer Builder', 'kr-toolkit' ),
				'manage_options',
				'kr-footers',
				array( $this, 'render_footers_page' )
			);
		}

		/**
		 * Render Headers Management Page
		 */
		public function render_headers_page() {
			?>
			<div class="wrap kr-builder-wrap">
				<h1><?php esc_html_e( 'Header Builder', 'kr-toolkit' ); ?></h1>
				
				<div style="margin-bottom: 2rem;">
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=kr-headers&action=new' ), 'kr_header_nonce' ) ); ?>" class="button button-primary">
						<?php esc_html_e( '+ Add New Header', 'kr-toolkit' ); ?>
					</a>
				</div>

				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Header Name', 'kr-toolkit' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'kr-toolkit' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'kr-toolkit' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $this->list_headers(); ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * Render Footers Management Page
		 */
		public function render_footers_page() {
			?>
			<div class="wrap kr-builder-wrap">
				<h1><?php esc_html_e( 'Footer Builder', 'kr-toolkit' ); ?></h1>
				
				<div style="margin-bottom: 2rem;">
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=kr-footers&action=new' ), 'kr_footer_nonce' ) ); ?>" class="button button-primary">
						<?php esc_html_e( '+ Add New Footer', 'kr-toolkit' ); ?>
					</a>
				</div>

				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Footer Name', 'kr-toolkit' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'kr-toolkit' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'kr-toolkit' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $this->list_footers(); ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * List all headers
		 */
		private function list_headers() {
			$headers = get_posts( array(
				'post_type'   => 'kr_header',
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'DESC',
			) );

			if ( empty( $headers ) ) {
				echo '<tr><td colspan="3">' . esc_html__( 'No headers found. Create your first header!', 'kr-toolkit' ) . '</td></tr>';
				return;
			}

			foreach ( $headers as $header ) {
				$edit_link = add_query_arg( array(
					'action' => 'edit',
					'id'     => $header->ID,
				), admin_url( 'admin.php?page=kr-headers' ) );

				$delete_link = wp_nonce_url( add_query_arg( array(
					'action' => 'delete',
					'id'     => $header->ID,
				), admin_url( 'admin.php?page=kr-headers' ) ), 'kr_header_delete_' . $header->ID );

				echo '<tr>';
				echo '<td><strong>' . esc_html( $header->post_title ) . '</strong></td>';
				echo '<td><code>[kr_header id="' . intval( $header->ID ) . '"]</code></td>';
				echo '<td>';
				echo '<a href="' . esc_url( $edit_link ) . '" class="button button-small">' . esc_html__( 'Edit', 'kr-toolkit' ) . '</a> ';
				echo '<a href="' . esc_url( $delete_link ) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_attr__( 'Are you sure?', 'kr-toolkit' ) . '\');">' . esc_html__( 'Delete', 'kr-toolkit' ) . '</a>';
				echo '</td>';
				echo '</tr>';
			}
		}

		/**
		 * List all footers
		 */
		private function list_footers() {
			$footers = get_posts( array(
				'post_type'   => 'kr_footer',
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'DESC',
			) );

			if ( empty( $footers ) ) {
				echo '<tr><td colspan="3">' . esc_html__( 'No footers found. Create your first footer!', 'kr-toolkit' ) . '</td></tr>';
				return;
			}

			foreach ( $footers as $footer ) {
				$edit_link = add_query_arg( array(
					'action' => 'edit',
					'id'     => $footer->ID,
				), admin_url( 'admin.php?page=kr-footers' ) );

				$delete_link = wp_nonce_url( add_query_arg( array(
					'action' => 'delete',
					'id'     => $footer->ID,
				), admin_url( 'admin.php?page=kr-footers' ) ), 'kr_footer_delete_' . $footer->ID );

				echo '<tr>';
				echo '<td><strong>' . esc_html( $footer->post_title ) . '</strong></td>';
				echo '<td><code>[kr_footer id="' . intval( $footer->ID ) . '"]</code></td>';
				echo '<td>';
				echo '<a href="' . esc_url( $edit_link ) . '" class="button button-small">' . esc_html__( 'Edit', 'kr-toolkit' ) . '</a> ';
				echo '<a href="' . esc_url( $delete_link ) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_attr__( 'Are you sure?', 'kr-toolkit' ) . '\');">' . esc_html__( 'Delete', 'kr-toolkit' ) . '</a>';
				echo '</td>';
				echo '</tr>';
			}
		}

		/**
		 * Admin scripts and styles
		 */
		public function admin_scripts() {
			wp_enqueue_style( 'kr-builder-admin', plugin_dir_url( __FILE__ ) . '../admin/css/builder.css', array(), KR_TOOLKIT_VERSION );
		}

		/**
		 * Register Elementor documents (requires Elementor)
		 */
		public function register_elementor_documents() {
			if ( ! did_action( 'elementor/loaded' ) ) {
				return;
			}
			// Will be extended when Elementor is active
		}

		/**
		 * Get header by ID
		 */
		public static function get_header( $header_id ) {
			if ( ! $header_id ) {
				return '';
			}

			$header = get_post( $header_id );
			if ( ! $header || 'kr_header' !== $header->post_type ) {
				return '';
			}

			return do_shortcode( $header->post_content );
		}

		/**
		 * Get footer by ID
		 */
		public static function get_footer( $footer_id ) {
			if ( ! $footer_id ) {
				return '';
			}

			$footer = get_post( $footer_id );
			if ( ! $footer || 'kr_footer' !== $footer->post_type ) {
				return '';
			}

			return do_shortcode( $footer->post_content );
		}

		/**
		 * Get default header ID from options
		 */
		public static function get_default_header_id() {
			return get_option( 'kr_default_header_id', 0 );
		}

		/**
		 * Get default footer ID from options
		 */
		public static function get_default_footer_id() {
			return get_option( 'kr_default_footer_id', 0 );
		}
	}
}
