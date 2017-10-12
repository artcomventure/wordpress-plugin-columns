<?php

/**
 * Plugin Name: Editor Columns
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-columns
 * Description: Extends HTML Editor with WYSIWYG columns.
 * Version: 1.7.4
 * Text Domain: columns
 * Author: artcom venture GmbH
 * Author URI: http://www.artcom-venture.de/
 */

if ( ! defined( 'COLUMNS_PLUGIN_URL' ) ) {
	define( 'COLUMNS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'COLUMNS_PLUGIN_DIR' ) ) {
	define( 'COLUMNS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Check if columns.css exists
 * ... and create it in case it doesn't.
 */
add_action( 'init', 'columns__init' );
function columns__init() {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	$plugin_data = get_plugin_data( __FILE__ );

	if ( ! file_exists( COLUMNS_PLUGIN_DIR . 'css/columns.min.css' ) || version_compare( $plugin_data['Version'], get_option( 'columns_version', 0 ), '>' ) ) {
		update_option_columns( NULL, columns_get_options( TRUE ), 'columns' );
		update_option( 'columns_version', $plugin_data['Version'] );
	}
}

/**
 * Create admin notice.
 *
 * @param string $message
 * @param string $type
 * @param boolean $html
 */
function columns_admin_notice( $message = '', $type = 'info', $html = false ) {
	if ( $message ) {
		if ( !$html ) $message = sprintf( '<p>%1$s</p>', esc_html( $message ) );

		add_action( 'admin_notices', function() use ($message, $type) {
			printf( '<div class="notice notice-%1$s is-dismissible">%2$s</div>', esc_attr( $type ), $message );
		} );
	}
}

/**
 * Enqueue admin and editor styles and scripts.
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
	if ( file_exists( COLUMNS_PLUGIN_DIR . 'css/columns.min.css' ) ) {
		add_editor_style( plugins_url( '/css/columns.min.css?', __FILE__ ) );
	}
	add_editor_style( plugins_url( '/css/editor.css?', __FILE__ ) );

	// enqueue button style
	wp_enqueue_style( 'columns-admin', COLUMNS_PLUGIN_URL . 'css/editor.css', array(), '20170321' );

	// add plugin js
	wp_register_script( 'columns-variables', COLUMNS_PLUGIN_URL . 'js/variables.js' );
	wp_localize_script( 'columns-variables', 'columns_options', columns_get_options( TRUE ) );
	wp_enqueue_script( 'columns-variables' );
	add_filter( 'mce_external_plugins', 'columns__mce_external_plugins' );
	// add columns button to editor
	add_filter( 'mce_buttons', 'columns__mce_buttons' );
}

/**
 * Enqueue columns styles.
 */
add_action( 'wp_enqueue_scripts', 'columns_enqueue_scripts' );
function columns_enqueue_scripts() {
	if ( file_exists( COLUMNS_PLUGIN_DIR . 'css/columns.min.css' ) ) {
		wp_enqueue_style( 'columns', COLUMNS_PLUGIN_URL . 'css/columns.min.css' );
	}
}

/**
 * Add plugin js.
 *
 * @param array $plugin_array
 *
 * @return array
 */
function columns__mce_external_plugins( $plugin_array ) {
	$plugin_array['columns'] = COLUMNS_PLUGIN_URL . 'js/editor.js';

	return $plugin_array;
}

/**
 * Add columns button to editor's first button row.
 *
 * @param array $buttons
 *
 * @return array
 */
function columns__mce_buttons( $buttons ) {
	// add to the very left
	array_unshift( $buttons, 'columns' );

	return $buttons;
}

/**
 * t9n.
 */
add_action( 'after_setup_theme', 'columns__after_setup_theme' );
function columns__after_setup_theme() {
	load_theme_textdomain( 'columns', COLUMNS_PLUGIN_DIR . 'languages' );
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
	$locales['columns'] = COLUMNS_PLUGIN_DIR . 'languages/mce.php';

	return $locales;
}

/**
 * Remove update notification (since this plugin isn't listed on https://wordpress.org/plugins/).
 */
add_filter( 'site_transient_update_plugins', 'columns__site_transient_update_plugins' );
function columns__site_transient_update_plugins( $value ) {
	$plugin_file = plugin_basename( __FILE__ );

	if ( isset( $value->response[ $plugin_file ] ) ) {
		unset( $value->response[ $plugin_file ] );
	}

	return $value;
}

/**
 * Change details link to GitHub repository.
 */
add_filter( 'plugin_row_meta', 'columns__plugin_row_meta', 10, 2 );
function columns__plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) == $file ) {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file );

		$links[2] = '<a href="' . $plugin_data['PluginURI'] . '">' . __( 'Visit plugin site' ) . '</a>';

		$links[] = '<a href="' . admin_url( 'options-general.php?page=columns-options' ) . '">' . __( 'Settings' ) . '</a>';
	}

	return $links;
}

// options
include( COLUMNS_PLUGIN_DIR . 'inc/options.php' );

/**
 * Delete traces on deactivation.
 */
register_deactivation_hook( __FILE__, 'columns_deactivate' );
function columns_deactivate() {
	delete_option( 'columns' );
}
