<?php

/**
 * Plugin Name: Columns
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-columns
 * Description: Extends WP Editor with columns.
 * Version: 1.0.0
 * Author: artcom venture GmbH
 * Author URI: http://www.artcom-venture.de/
 */

// ...
add_action( 'admin_head', 'columns_wpe_button' );
function columns_wpe_button() {
	// check if user has edit permission
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
	     // allow to use wysiwyg
	     || ! get_user_option( 'rich_editing' ) == 'true'
	) {
		return;
	}

	add_editor_style( plugins_url( '/css/columns.css?' . time(), __FILE__ ) );
	add_editor_style( plugins_url( '/css/columns.admin.css?' . time(), __FILE__ ) );
	wp_enqueue_style( 'columns-admin', plugins_url( '/css/editor.css', __FILE__ ) );

	add_filter( 'mce_external_plugins', 'columns__mce_external_plugins' );
	add_filter( 'mce_buttons', 'columns__mce_buttons' );
}

/**
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', 'columns_enqueue_scripts' );
function columns_enqueue_scripts() {
	if ( is_admin() ) {
		return;
	}

	wp_enqueue_style( 'columns', plugins_url( '/css/columns.css', __FILE__ ) );
}

/**
 * ...
 *
 * @param $plugin_array
 *
 * @return mixed
 */
function columns__mce_external_plugins( $plugin_array ) {
	$plugin_array['columns'] = WP_PLUGIN_URL . '/columns/js/columns.admin.min.js';

	return $plugin_array;
}

/**
 * Register column button.
 *
 * @param array $buttons
 *
 * @return array
 */
function columns__mce_buttons( $buttons ) {
	// add columns button
	array_unshift( $buttons, 'columns' );

	// ...
	foreach ( array( 'wp_more', 'wp_adv' ) as $button ) {
		if ( $index = array_search( $button, $buttons ) ) {
			unset( $buttons[ $index ] );
			array_push( $buttons, $button );
		}
	}

	return $buttons;
}
