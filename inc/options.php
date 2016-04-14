<?php

/**
 * Register settings options.
 */
add_action( 'admin_init', 'columns__admin_init' );
function columns__admin_init() {
	register_setting( 'columns', 'columns' );
}

/**
 * Register share admin page.
 */
add_action( 'admin_menu', 'columns__admin_menu' );
function columns__admin_menu() {
	add_options_page(
		__( 'Editor Columns', 'columns' ),
		__( 'Editor Columns', 'columns' ),
		'manage_options',
		'columns-options',
		'columns_options_page'
	);
}

/**
 * Settings page markup.
 */
function columns_options_page() {
	wp_enqueue_script( 'columns-options', COLUMNS_PLUGIN_URL . 'js/options.min.js', array(), '20160412' );

	include( COLUMNS_PLUGIN_DIR . 'inc/options.form.php' );
}

/**
 * @param string $option
 * @param bool $default
 *
 * @return mixed
 */
function columns_get_option( $option = '', $default = false ) {
	$options = columns_get_options( $default );

	if ( ! $option || !isset( $options[$option] ) ) {
		return NULL;
	}

	return $options[$option];
}

/**
 * @param bool $defaults
 *
 * @return array
 */
function columns_get_options( $defaults = false ) {
	$options = get_option( 'columns', array() ) + array(
			'columns' => '',
			'responsive' => 0,
			'tablet' => '',
			'mobile' => ''
		);

	// merge defaults
	if ( $defaults ) {
		global $content_width;

		$options = array_filter( $options ) + array(
				'columns' => 9,
				'responsive' => 0,
				'tablet' => floor( $content_width / 3 * 2 ),
				'mobile' => floor( $content_width / 2 )
			);
	}

	return $options;
}
