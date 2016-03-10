<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

function columns_translation() {
	$strings = array(
		'Columns' => __( 'Columns', 'columns' ),
		'Column' => __( 'Column', 'columns' ),
		'AddParagraph' => __( 'Add paragraph', 'columns' ),
	);

	$locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.columns", ' . json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = columns_translation();
