<?php

/**
 * Plugin Name: Columns
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-columns
 * Description: Extends WP Editor with columns.
 * Version: 1.1.3
 * Author: artcom venture GmbH
 * Author URI: http://www.artcom-venture.de/
 */

/**
 * Admin.
 */
add_action( 'admin_head', 'columns__admin_head' );
function columns__admin_head() {
	// check if user has permission to edit posts
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
	     // ... and is allowed to use wysiwyg
	     || ! get_user_option( 'rich_editing' ) == 'true'
	) {
		return;
	}

	// add styles to editor
	add_editor_style( plugins_url( '/css/columns.min.css?' . time(), __FILE__ ) );
	add_editor_style( plugins_url( '/css/columns.admin.min.css?' . time(), __FILE__ ) );

	// enqueue button style
	wp_enqueue_style( 'columns-admin', plugins_url( '/css/editor.min.css', __FILE__ ) );

	// add plugin js
	add_filter( 'mce_external_plugins', 'columns__mce_external_plugins' );
	// add columns button to editor
	add_filter( 'mce_buttons', 'columns__mce_buttons' );
}

/**
 * Enqueue default columns styles.
 */
add_action( 'wp_enqueue_scripts', 'columns_enqueue_scripts' );
function columns_enqueue_scripts() {
	wp_enqueue_style( 'columns', plugins_url( '/css/columns.min.css', __FILE__ ) );
}

/**
 * Add plugin js.
 *
 * @param array $plugin_array
 *
 * @return array
 */
function columns__mce_external_plugins( $plugin_array ) {
	$plugin_array['columns'] = WP_PLUGIN_URL . '/columns/js/columns.admin.min.js';

	return $plugin_array;
}

/**
 * Add columns button to editor.
 *
 * @param array $buttons
 *
 * @return array
 */
function columns__mce_buttons( $buttons ) {
	// to front
	array_unshift( $buttons, 'columns' );

	return $buttons;
}

/**
 * i18n.
 */
add_action( 'after_setup_theme', 'columns__after_setup_theme' );
function columns__after_setup_theme() {
  load_theme_textdomain( 'columns', WP_PLUGIN_DIR . '/columns/languages' );
}

/**
 * MCE translation.
 *
 * @param array $locales
 *
 * @return array
 */
add_filter( 'mce_external_languages', 'columns__mce_external_languages' );
function columns__mce_external_languages( $locales ) {
  $locales['columns'] = plugin_dir_path( __FILE__ ) . 'languages/mce.php';

  return $locales;
}
