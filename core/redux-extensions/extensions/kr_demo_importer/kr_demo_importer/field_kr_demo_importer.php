<?php
/**
 * KR Demo Importer Field for Redux Framework
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * @package     KR_Demo_Importer - Field Class
 * @author      KR Theme
 * @version     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_kr_demo_importer' ) ) {

	class ReduxFramework_kr_demo_importer {

		protected $parent;
		
		protected $field;

		protected $value;

		public $extension_url;

		public $extension_dir;

		public $demo_data_dir;
		
		public $demo_data_url;

		/**
		 * Field Constructor.
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function __construct( $field = array(), $value = '', $parent = '' ) {
			$this->parent = $parent;
			$this->field = $field;
			$this->value = $value;

			$class = ReduxFramework_extension_kr_demo_importer::get_instance();

			if ( ! empty( $class->demo_data_dir ) ) {
				$this->demo_data_dir = $class->demo_data_dir;
				$this->demo_data_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->demo_data_dir ) );
			}

			if ( empty( $this->extension_dir ) ) {
				$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
				$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
			}
		}

		/**
		 * Field Render Function.
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {

			echo '</fieldset></td></tr><tr><td colspan="2"><fieldset class="redux-field kr_demo_importer">';

			$nonce = wp_create_nonce( "redux_{$this->parent->args['opt_name']}_kr_demo_importer" );

			// No errors please
			$defaults = array(
				'id'        => '',
				'url'       => '',
				'width'     => '',
				'height'    => '',
				'thumbnail' => '',
			);

			$this->value = wp_parse_args( $this->value, $defaults );

			$imported = false;

			$this->field['kr_demo_imports'] = apply_filters( "redux/{$this->parent->args['opt_name']}/field/kr_demo_importer_files", array() );

			echo '<div class="theme-browser"><div class="themes">';

			if ( ! empty( $this->field['kr_demo_imports'] ) ) {

				foreach ( $this->field['kr_demo_imports'] as $section => $imports ) {

					if ( empty( $imports ) ) {
						continue;
					}

					if ( ! array_key_exists( 'imported', $imports ) ) {
						$extra_class = 'not-imported';
						$imported = false;
						$import_message = esc_html__( 'Import Demo', 'kr-theme' );
					} else {
						$imported = true;
						$extra_class = 'active imported';
						$import_message = esc_html__( 'Demo Imported', 'kr-theme' );
					}

					echo '<div class="wrap-importer theme ' . esc_attr( $extra_class ) . '" data-demo-id="' . esc_attr( $section ) . '" data-nonce="' . esc_attr( $nonce ) . '" id="' . esc_attr( $this->field['id'] ) . '-custom_imports">';

					echo '<div class="theme-screenshot">';

					if ( isset( $imports['image'] ) ) {
						echo '<img class="kr_image" src="' . esc_attr( esc_url( $this->demo_data_url . $imports['directory'] . '/' . $imports['image'] ) ) . '"/>';
					}

					echo '</div>';

					echo '<span class="more-details">' . esc_html( $import_message ) . '</span>';
					echo '<h3 class="theme-name">' . esc_html( apply_filters( 'kr_demo_importer_directory_title', $imports['directory'] ) ) . '</h3>';

					echo '<div class="theme-actions">';

					if ( false == $imported ) {
						echo '<div class="kr-importer-buttons"><span class="spinner">' . esc_html__( 'Please Wait...', 'kr-theme' ) . '</span><span class="button-primary importer-button import-demo-data">' . esc_html__( 'Import Demo', 'kr-theme' ) . '</span></div>';
					} else {
						echo '<div class="kr-importer-buttons button-secondary importer-button">' . esc_html__( 'Imported', 'kr-theme' ) . '</div>';
						echo '<span class="spinner">' . esc_html__( 'Please Wait...', 'kr-theme' ) . '</span>';
						echo '<div id="kr-importer-reimport" class="kr-importer-buttons button-primary import-demo-data importer-button">' . esc_html__( 'Re-Import', 'kr-theme' ) . '</div>';
					}

					echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<h5>' . esc_html__( 'No Demo Data Provided', 'kr-theme' ) . '</h5>';
			}

			echo '</div></div>';
			echo '</fieldset></td></tr>';
		}
	}
}
