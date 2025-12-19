<?php
/**
 * KR Demo Importer Extension for Redux Framework
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 * @link https://github.com/FrankM1/radium-one-click-demo-install
 *
 * @package     KR_Demo_Importer - Redux Extension for Importing demo content
 * @author      KR Theme
 * @version     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_extension_kr_demo_importer' ) ) {

	class ReduxFramework_extension_kr_demo_importer {

		public static $instance;

		static $version = "1.0.0";

		protected $parent;
		
		protected $field_name;

		private $filesystem = array();

		public $extension_url;

		public $extension_dir;

		public $demo_data_dir;

		public $kr_import_files = array();

		public $active_import_id;

		public $active_import;

		/**
		 * Class Constructor
		 *
		 * @since       1.0
		 * @access      public
		 * @return      void
		 */
		public function __construct( $parent ) {

			$this->parent = $parent;

			if ( ! is_admin() ) {
				return;
			}

			// Abort if filter returns false
			if ( true !== apply_filters( 'kr_demo_importer_abort', true ) ) {
				return;
			}

			if ( empty( $this->extension_dir ) ) {
				$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
				$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
				$this->demo_data_dir = apply_filters( "kr_demo_importer_dir_path", $this->extension_dir . 'demo-data/' );
			}

			$this->getImports();

			$this->field_name = 'kr_demo_importer';

			self::$instance = $this;

			add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, array( $this, 'overload_field_path' ) );

			add_action( 'wp_ajax_redux_kr_demo_importer', array( $this, 'ajax_importer' ) );

			add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/kr_demo_importer_files', array( $this, 'addImportFiles' ) );

			// Add importer section to panel
			$this->add_importer_section();

			add_action( 'kr_demo_importer_after_content_import', array( $this, 'kr_extended_example' ), 10, 2 );
		}

		/**
		 * Get the demo folders/files
		 *
		 * @return array list of files for demos
		 */
		public function demoFiles() {

			$dir_array = $this->dirToArray( $this->demo_data_dir );

			if ( ! empty( $dir_array ) && is_array( $dir_array ) ) {
				uksort( $dir_array, 'strcasecmp' );
				return $dir_array;
			} else {

				$dir_array = array();

				$demo_directory = array_diff( scandir( $this->demo_data_dir ), array( '..', '.' ) );

				if ( ! empty( $demo_directory ) && is_array( $demo_directory ) ) {
					foreach ( $demo_directory as $key => $value ) {
						if ( is_dir( $this->demo_data_dir . $value ) ) {

							$dir_array[ $value ] = array( 'name' => $value, 'type' => 'd', 'files' => array() );

							$demo_content = array_diff( scandir( $this->demo_data_dir . $value ), array( '..', '.' ) );

							foreach ( $demo_content as $d_key => $d_value ) {
								if ( is_file( $this->demo_data_dir . $value . '/' . $d_value ) ) {
									$dir_array[ $value ]['files'][ $d_value ] = array( 'name' => $d_value, 'type' => 'f' );
								}
							}
						}
					}

					uksort( $dir_array, 'strcasecmp' );
				}
			}
			return $dir_array;
		}

		/**
		 * Get imports and check if already imported
		 *
		 * @return void
		 */
		public function getImports() {

			if ( ! empty( $this->kr_import_files ) ) {
				return $this->kr_import_files;
			}

			$imports = $this->demoFiles();

			$imported = get_option( 'kr_imported_demos' );

			if ( ! empty( $imports ) && is_array( $imports ) ) {
				$x = 1;
				foreach ( $imports as $import ) {

					if ( ! isset( $import['files'] ) || empty( $import['files'] ) ) {
						continue;
					}

					if ( 'd' == $import['type'] && ! empty( $import['name'] ) ) {
						$this->kr_import_files[ 'kr-import-' . $x ] = isset( $this->kr_import_files[ 'kr-import-' . $x ] ) ? $this->kr_import_files[ 'kr-import-' . $x ] : array();
						$this->kr_import_files[ 'kr-import-' . $x ]['directory'] = $import['name'];

						if ( ! empty( $imported ) && is_array( $imported ) ) {
							if ( array_key_exists( 'kr-import-' . $x, $imported ) ) {
								$this->kr_import_files[ 'kr-import-' . $x ]['imported'] = 'imported';
							}
						}

						foreach ( $import['files'] as $file ) {
							switch ( $file['name'] ) {
							case 'content.xml':
								$this->kr_import_files[ 'kr-import-' . $x ]['content_file'] = $file['name'];
								break;

							case 'theme-options.txt':
							case 'theme-options.json':
								$this->kr_import_files[ 'kr-import-' . $x ]['theme_options'] = $file['name'];
								break;

							case 'widgets.json':
							case 'widgets.txt':
								$this->kr_import_files[ 'kr-import-' . $x ]['widgets'] = $file['name'];
								break;

							case 'screen-image.png':
							case 'screen-image.jpg':
							case 'screen-image.gif':
								$this->kr_import_files[ 'kr-import-' . $x ]['image'] = $file['name'];
								break;
							}
						}

						if ( ! isset( $this->kr_import_files[ 'kr-import-' . $x ]['content_file'] ) ) {
							unset( $this->kr_import_files[ 'kr-import-' . $x ] );
							continue;
						}

						$x++;
					}
				}
			}
		}

		/**
		 * Get singleton instance
		 *
		 * @return ReduxFramework_extension_kr_demo_importer
		 */
		public static function get_instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Overload field path
		 *
		 * @param mixed $value
		 * @return string
		 */
		public function overload_field_path( $value ) {
			return dirname( __FILE__ ) . '/kr_demo_importer/field_kr_demo_importer.php';
		}

		/**
		 * Add importer section to theme options
		 *
		 * @return void
		 */
		public function add_importer_section() {

			if ( isset( $this->parent->sections ) ) {
				$this->parent->sections[] = array(
					'title'  => esc_html__( 'Demo Importer', 'kr-theme' ),
					'desc'   => esc_html__( 'Import pre-built demo websites to get started quickly', 'kr-theme' ),
					'icon'   => 'el el-download-alt',
					'fields' => array(
						array(
							'id'   => 'kr_demo_importer',
							'type' => 'kr_demo_importer',
							'title' => esc_html__( 'Demo Importer', 'kr-theme' ),
							'desc' => esc_html__( 'Select a demo and click Import Demo to get started', 'kr-theme' ),
						),
					),
				);
			}
		}

		/**
		 * Add import files for display
		 *
		 * @param mixed $value
		 * @return array
		 */
		public function addImportFiles( $value ) {
			return $this->kr_import_files;
		}

		/**
		 * Directory to array
		 *
		 * @param mixed $dir
		 * @return array
		 */
		public function dirToArray( $dir ) {
			$result = array();

			if ( is_dir( $dir ) ) {
				if ( $dh = opendir( $dir ) ) {
					while ( ( $file = readdir( $dh ) ) !== false ) {
						if ( $file != "." && $file != ".." ) {
							if ( is_dir( $dir . "/" . $file ) ) {
								$result[ $file ] = $this->dirToArray( $dir . "/" . $file );
							} else {
								$result[] = $file;
							}
						}
					}
					closedir( $dh );
				}
			}
			return $result;
		}

		/**
		 * AJAX importer handler
		 *
		 * @return void
		 */
		public function ajax_importer() {

			if ( ! isset( $_REQUEST['nonce'] ) ) {
				wp_die( 'Security nonce missing' );
			}

			if ( ! wp_verify_nonce( $_REQUEST['nonce'], "redux_{$this->parent->args['opt_name']}_kr_demo_importer" ) ) {
				wp_die( 'Security check failed' );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'Insufficient permissions' );
			}

			$this->active_import_id = isset( $_REQUEST['demo_import_id'] ) ? sanitize_text_field( $_REQUEST['demo_import_id'] ) : '';

			if ( empty( $this->active_import_id ) || ! array_key_exists( $this->active_import_id, $this->kr_import_files ) ) {
				wp_die( 'Invalid demo ID' );
			}

			$this->active_import = $this->kr_import_files[ $this->active_import_id ];

			$type = isset( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : '';

			if ( $type == "import-demo-content" ) {
				$this->import_demo_content();
			}
		}

		/**
		 * Import demo content
		 *
		 * @return void
		 */
		public function import_demo_content() {

			$return_data = array( 'status' => true, 'message' => '' );

			if ( isset( $this->active_import['content_file'] ) && ! empty( $this->active_import['content_file'] ) ) {

				$content_file = $this->demo_data_dir . $this->active_import['directory'] . '/' . $this->active_import['content_file'];

				if ( ! file_exists( $content_file ) ) {
					$return_data['status'] = false;
					$return_data['message'] = esc_html__( 'Content file not found', 'kr-theme' );
					wp_send_json( $return_data );
				}

				// Import WordPress content via XML
				if ( function_exists( 'wp_import_posts' ) ) {
					include_once( ABSPATH . 'wp-admin/includes/import.php' );
				}

				// Import content
				$this->import_content( $content_file );

				// Import theme options
				if ( isset( $this->active_import['theme_options'] ) && ! empty( $this->active_import['theme_options'] ) ) {
					$this->import_theme_options();
				}

				// Import widgets
				if ( isset( $this->active_import['widgets'] ) && ! empty( $this->active_import['widgets'] ) ) {
					$this->import_widgets();
				}

				// Save imported status
				$imported = get_option( 'kr_imported_demos' );
				if ( ! is_array( $imported ) ) {
					$imported = array();
				}
				$imported[ $this->active_import_id ] = $this->active_import['directory'];
				update_option( 'kr_imported_demos', $imported );

				$return_data['status'] = true;
				$return_data['message'] = esc_html__( 'Demo imported successfully!', 'kr-theme' );

				do_action( 'kr_demo_importer_after_content_import', $this->active_import, $this->active_import_id );
			}

			wp_send_json( $return_data );
		}

		/**
		 * Import content from XML
		 *
		 * @param string $file_path
		 * @return void
		 */
		public function import_content( $file_path ) {

			// WordPress XML importer
			if ( file_exists( $file_path ) ) {
				include_once( ABSPATH . 'wp-admin/includes/import.php' );

				if ( function_exists( 'wp_import_posts' ) ) {
					wp_import_posts( $file_path );
				}
			}
		}

		/**
		 * Import theme options
		 *
		 * @return void
		 */
		public function import_theme_options() {

			if ( ! isset( $this->active_import['theme_options'] ) ) {
				return;
			}

			$file_path = $this->demo_data_dir . $this->active_import['directory'] . '/' . $this->active_import['theme_options'];

			if ( ! file_exists( $file_path ) ) {
				return;
			}

			$content = file_get_contents( $file_path );
			$options = json_decode( $content, true );

			if ( ! empty( $options ) && is_array( $options ) ) {
				update_option( 'kr_theme_options', $options );
			}
		}

		/**
		 * Import widgets
		 *
		 * @return void
		 */
		public function import_widgets() {

			if ( ! isset( $this->active_import['widgets'] ) ) {
				return;
			}

			$file_path = $this->demo_data_dir . $this->active_import['directory'] . '/' . $this->active_import['widgets'];

			if ( ! file_exists( $file_path ) ) {
				return;
			}

			$content = file_get_contents( $file_path );
			$widgets = json_decode( $content, true );

			if ( ! empty( $widgets ) && is_array( $widgets ) ) {
				// Import widget instances
				foreach ( $widgets as $sidebar_id => $sidebar_widgets ) {
					update_option( 'sidebars_widgets_' . $sidebar_id, $sidebar_widgets );
				}
			}
		}

		/**
		 * Extended example hook
		 *
		 * @param mixed $demo
		 * @param mixed $demo_id
		 * @return void
		 */
		public function kr_extended_example( $demo, $demo_id ) {
			// Hook for custom post-import logic
		}
	}
}
