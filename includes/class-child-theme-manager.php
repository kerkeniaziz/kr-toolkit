<?php
/**
 * Child Theme Manager Class
 *
 * @package KR_Toolkit
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KR_Child_Theme_Manager Class
 */
class KR_Child_Theme_Manager {

	/**
	 * Parent theme slug
	 *
	 * @var string
	 */
	private $parent_theme = 'kr-theme';

	/**
	 * Check if a child theme exists and is active
	 *
	 * @return bool
	 */
	public function has_child_theme() {
		$current_theme = wp_get_theme();
		return ( $current_theme->get_template() === $this->parent_theme && $current_theme->get_stylesheet() !== $this->parent_theme );
	}

	/**
	 * Get active child theme information
	 *
	 * @return array|null
	 */
	public function get_child_theme_info() {
		if ( ! $this->has_child_theme() ) {
			return null;
		}

		$theme = wp_get_theme();
		return array(
			'name'        => $theme->get( 'Name' ),
			'folder'      => $theme->get_stylesheet(),
			'version'     => $theme->get( 'Version' ),
			'description' => $theme->get( 'Description' ),
			'author'      => $theme->get( 'Author' ),
			'template'    => $theme->get_template(),
			'path'        => $theme->get_stylesheet_directory(),
		);
	}

	/**
	 * Create child theme
	 *
	 * @param string $theme_name Child theme name.
	 * @return bool|WP_Error
	 */
	public function create_child_theme( $theme_name = '' ) {
		// Validate name
		if ( empty( $theme_name ) ) {
			$theme_name = 'KR Child';
		}

		// Sanitize slug
		$theme_slug = sanitize_title( $theme_name );
		$theme_dir = get_theme_root() . '/' . $theme_slug;

		// Check if already exists
		if ( file_exists( $theme_dir ) ) {
			return new WP_Error( 'theme_exists', __( 'A theme with this name already exists.', 'kr-toolkit' ) );
		}

		// Create directory
		if ( ! wp_mkdir_p( $theme_dir ) ) {
			return new WP_Error( 'dir_creation_failed', __( 'Failed to create theme directory.', 'kr-toolkit' ) );
		}

		// Create style.css
		$this->create_style_file( $theme_dir, $theme_name, $theme_slug );

		// Create functions.php
		$this->create_functions_file( $theme_dir );

		// Create screenshot
		$this->copy_screenshot( $theme_dir );

		// Create readme
		$this->create_readme_file( $theme_dir, $theme_name );

		return true;
	}

	/**
	 * Create style.css file
	 *
	 * @param string $theme_dir Theme directory path.
	 * @param string $theme_name Theme name.
	 * @param string $theme_slug Theme slug.
	 */
	private function create_style_file( $theme_dir, $theme_name, $theme_slug ) {
		$content = "/*
Theme Name:   {$theme_name}
Theme URI:    https://krtheme.com
Description:  Child theme for KR Theme
Author:       KR Theme
Author URI:   https://www.krtheme.com/
Template:     kr-theme
Version:      1.0.0
License:      GNU General Public License v2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  {$theme_slug}
*/

/* ==========================================================================
   Add your custom styles below
   ========================================================================== */
";

		file_put_contents( $theme_dir . '/style.css', $content );
	}

	/**
	 * Create functions.php file
	 *
	 * @param string $theme_dir Theme directory path.
	 */
	private function create_functions_file( $theme_dir ) {
		$content = "<?php
/**
 * Child Theme Functions
 *
 * @package KR_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent and child theme styles
 */
function kr_child_enqueue_styles() {
	// Parent theme stylesheet
	wp_enqueue_style( 'kr-theme-style', get_template_directory_uri() . '/style.css', array(), '4.2.1' );
	
	// Child theme stylesheet
	wp_enqueue_style( 'kr-child-style', get_stylesheet_uri(), array( 'kr-theme-style' ), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'kr_child_enqueue_styles', 15 );

/**
 * Add your custom functions below
 */
";

		file_put_contents( $theme_dir . '/functions.php', $content );
	}

	/**
	 * Copy screenshot from parent theme
	 *
	 * @param string $theme_dir Theme directory path.
	 */
	private function copy_screenshot( $theme_dir ) {
		$parent_screenshot = get_template_directory() . '/screenshot.png';
		
		if ( file_exists( $parent_screenshot ) ) {
			copy( $parent_screenshot, $theme_dir . '/screenshot.png' );
		}
	}

	/**
	 * Create readme file
	 *
	 * @param string $theme_dir Theme directory path.
	 * @param string $theme_name Theme name.
	 */
	private function create_readme_file( $theme_dir, $theme_name ) {
		$content = "# {$theme_name}

Child theme for KR Theme

## Description

This is a child theme for KR Theme. Add your customizations here to preserve them through parent theme updates.

## Installation

1. Upload the theme folder to /wp-content/themes/
2. Activate the theme through the WordPress admin
3. Customize as needed

## Customization

Add your custom CSS in style.css
Add your custom PHP in functions.php

## Support

For support, visit https://krtheme.com/support

## Copyright

Copyright © " . date('Y') . " KR Theme. All rights reserved.
";

		file_put_contents( $theme_dir . '/readme.md', $content );
	}

	/**
	 * Download child theme as zip
	 *
	 * @param string $theme_slug Child theme slug.
	 * @return bool|WP_Error
	 */
	public function download_child_theme( $theme_slug ) {
		$theme_dir = get_theme_root() . '/' . $theme_slug;

		if ( ! file_exists( $theme_dir ) ) {
			return new WP_Error( 'theme_not_found', __( 'Child theme not found.', 'kr-toolkit' ) );
		}

		// Create zip
		$zip_file = wp_tempnam( $theme_slug . '.zip' );
		$zip = new ZipArchive();

		if ( $zip->open( $zip_file, ZipArchive::CREATE ) !== true ) {
			return new WP_Error( 'zip_failed', __( 'Failed to create zip file.', 'kr-toolkit' ) );
		}

		// Add files to zip
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $theme_dir ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ( $files as $file ) {
			if ( ! $file->isDir() ) {
				$file_path = $file->getRealPath();
				$relative_path = substr( $file_path, strlen( $theme_dir ) + 1 );
				$zip->addFile( $file_path, $theme_slug . '/' . $relative_path );
			}
		}

		$zip->close();

		return $zip_file;
	}

	/**
	 * Get installed child themes
	 *
	 * @return array
	 */
	public function get_child_themes() {
		$themes = wp_get_themes();
		$child_themes = array();

		foreach ( $themes as $theme ) {
			if ( $theme->get_template() === $this->parent_theme ) {
				$child_themes[] = array(
					'name'        => $theme->get( 'Name' ),
					'slug'        => $theme->get_stylesheet(),
					'version'     => $theme->get( 'Version' ),
					'description' => $theme->get( 'Description' ),
					'author'      => $theme->get( 'Author' ),
					'is_active'   => ( get_stylesheet() === $theme->get_stylesheet() ),
				);
			}
		}

		return $child_themes;
	}

	/**
	 * Delete child theme
	 *
	 * @param string $theme_slug Child theme slug.
	 * @return bool|WP_Error
	 */
	public function delete_child_theme( $theme_slug ) {
		// Don't delete if active
		if ( get_stylesheet() === $theme_slug ) {
			return new WP_Error( 'theme_active', __( 'Cannot delete active theme.', 'kr-toolkit' ) );
		}

		$theme_dir = get_theme_root() . '/' . $theme_slug;

		if ( ! file_exists( $theme_dir ) ) {
			return new WP_Error( 'theme_not_found', __( 'Child theme not found.', 'kr-toolkit' ) );
		}

		// Delete directory
		$deleted = $this->delete_directory( $theme_dir );

		if ( ! $deleted ) {
			return new WP_Error( 'delete_failed', __( 'Failed to delete child theme.', 'kr-toolkit' ) );
		}

		return true;
	}

	/**
	 * Recursively delete directory
	 *
	 * @param string $dir Directory path.
	 * @return bool
	 */
	private function delete_directory( $dir ) {
		if ( ! file_exists( $dir ) ) {
			return true;
		}

		if ( ! is_dir( $dir ) ) {
			return unlink( $dir );
		}

		foreach ( scandir( $dir ) as $item ) {
			if ( $item == '.' || $item == '..' ) {
				continue;
			}

			if ( ! $this->delete_directory( $dir . DIRECTORY_SEPARATOR . $item ) ) {
				return false;
			}
		}

		return rmdir( $dir );
	}
}
