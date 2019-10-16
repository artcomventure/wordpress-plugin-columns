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
	wp_enqueue_script( 'columns-options', COLUMNS_PLUGIN_URL . 'js/options.js', array(), '20160412' );

	include( COLUMNS_PLUGIN_DIR . 'inc/options.form.php' );
}

/**
 * @param string $option
 * @param bool $default
 *
 * @return mixed
 */
function columns_get_option( $option = '', $default = FALSE ) {
	$options = columns_get_options( $default );

	if ( ! $option || ! isset( $options[ $option ] ) ) {
		return NULL;
	}

	return $options[ $option ];
}

/**
 * @param bool $defaults
 *
 * @return array
 */
function columns_get_options( $defaults = FALSE ) {
	$options = get_option( 'columns', array() ) + array(
			'columns'    => '',
			'gap'        => '',
			'gallery'    => 0,
			'responsive' => 0,
			'tablet'     => '',
			'mobile'     => ''
		);

	// merge defaults
	if ( $defaults ) {
		global $content_width;

		$options = array_filter( $options, function ( $option ) {
				return $option !== '';
			} ) + array(
			           'columns'    => 9,
			           'gap'        => '1.5em',
			           'gallery'    => 0,
			           'responsive' => 0,
			           'tablet'     => floor( $content_width / 3 * 2 ),
			           'mobile'     => floor( $content_width / 2 )
		           );
	}

	return $options;
}

/**
 * Create/update CSS file.
 */
add_action( 'update_option_columns', 'update_option_columns', 10, 3 );
function update_option_columns( $old_value, $value, $option ) {
	$options = columns_get_options( TRUE );

	if ( $options['gap'] === '0' ) {
		$options['gap'] = '0px';
	} elseif ( ! $options['gap'] || ! preg_match( '/\d+(\.\d+)?(px|em|rem|%)/', $options['gap'] ) ) {
		$options['gap'] = '1.5em';
	}

	$css = '.columns
' . ( $options['gallery'] ? ', .gallery' : '' ) . ' {
    display: -ms-flexbox;
    display: flex;
    width: 100%;
    max-width: 100%;
    -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    -ms-flex-pack: justify;
    justify-content: space-between;
}

ul.columns {
	list-style: none;
	margin-left: 0;
	padding-left: 0;
}

.columns > br
' . ( $options['gallery'] ? ', .gallery > br' : '' ) . ' {
	display: none;
}

.columns > *
' . ( $options['gallery'] ? ', .gallery .gallery-item' : '' ) . ' {
    margin-bottom: ' . $options['gap'] . ';
    margin-left: ' . $options['gap'] . ';
    padding: 0;
    box-sizing: border-box;
}

.columns > * > *:first-child
' . ( $options['gallery'] ? ', .gallery .gallery-item > *:first-child' : '' ) . ' {
    margin-top: 0;
}

.columns > * > *:last-child
' . ( $options['gallery'] ? ', .gallery .gallery-item > *:last-child' : '' ) . ' {
    margin-bottom: 0;
}';

	for ( $i = 1; $i <= 9; $i ++ ) {
		$css .= '

.columns-' . $i . ' > *
' . ( $options['gallery'] ? ', .gallery-columns-' . $i . ' .gallery-item' : '' ) . ' {
    width: calc((100% - ' . $options['gap'] . ' * ' . ( $i - 1 ) . ') / ' . $i . ');
}

.columns-' . $i . ' > *:nth-child(' . $i . 'n+1)
' . ( $options['gallery'] ? ', .gallery-columns-' . $i . ' .gallery-item:nth-child(' . $i . 'n+1)' : '' ) . ' {
    margin-left: 0;
}';

		if ( $i == 2 ) {
			$css .= '

.columns-2 > *.column-narrow {
    width: calc(33.33333% - ' . $options['gap'] . ' / 2);
}

.columns-2 > *.column-wide {
    width: calc(66.66666% - ' . $options['gap'] . ' / 2);
}';
		}
	};

	if ( $options['responsive'] ) {
		// tablet
		$css .= '

@media ( max-width: ' . $options['tablet'] . 'px ) {
	.columns.columns-5 > *
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-5 .gallery-item' : '' ) . ',
	.columns.columns-6 > *
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-6 .gallery-item' : '' ) . ',
	.columns.columns-7 > *:nth-child(7n+5)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-7 .gallery-item:nth-child(7n+5)' : '' ) . ',
	.columns.columns-7 > *:nth-child(7n+6)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-7 .gallery-item:nth-child(7n+6)' : '' ) . ',
	.columns.columns-7 > *:nth-child(7n+7)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-7 .gallery-item:nth-child(7n+7)' : '' ) . ',
	.columns.columns-9 > *
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-9 .gallery-item' : '' ) . ' {
		width: calc(33.33333% - ' . $options['gap'] . ' * 2 / 3);
	}

	.columns.columns-2 > .column-narrow,
	.columns.columns-2 > .column-wide,
	.columns.columns-4 > *,
	.columns.columns-5 > *:nth-child(5n+4),
	.columns.columns-5 > *:nth-child(5n+5)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-5 .gallery-item:nth-child(n+4)' : '' ) . ' {
		width: calc(50% - ' . $options['gap'] . ' / 2);
	}

	.columns.columns-7 > *
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-7 .gallery-item' : '' ) . ',
	.columns.columns-8 > *
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-8 .gallery-item' : '' ) . ' {
		width: calc(25% - ' . $options['gap'] . ' * 3 / 4);
	}

	.columns.columns-4 > *:nth-child(2n+1)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-4 .gallery-item:nth-child(2n+1)' : '' ) . ',
	.columns.columns-5 > *:nth-child(5n+4)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-5 .gallery-item:nth-child(5n+4)' : '' ) . ',
	.columns.columns-6 > *:nth-child(3n+1)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-6 .gallery-item:nth-child(3n+1)' : '' ) . ',
	.columns.columns-7 > *:nth-child(7n+5)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-7 .gallery-item:nth-child(7n+5)' : '' ) . ',
	.columns.columns-8 > *:nth-child(4n+1)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-8 .gallery-item:nth-child(4n+1)' : '' ) . ',
	.columns.columns-9 > *:nth-child(3n+1)
	' . ( $options['gallery'] ? ', .gallery.gallery-columns-9 .gallery-item:nth-child(3n+1)' : '' ) . ' {
	    margin-left: 0;
	}
}';

		// mobile
		$css .= '

@media ( max-width: ' . $options['mobile'] . 'px ) {
	.columns
	' . ( $options['gallery'] ? ', .gallery' : '' ) . ' {
		    flex-direction: column;
        -ms-flex-direction: column;
	}

	.columns[class*="columns-"] > *
	' . ( $options['gallery'] ? ', .gallery[class*="gallery-columns-"] .gallery-item' : '' ) . ' {
		width: 100% !important;
		margin-left: 0 !important;
		max-width: none;
	}
}';
	}

	// eventually create/update css
	if ( $fp = @fopen( COLUMNS_PLUGIN_DIR . 'css/columns.css', 'wb' ) ) {
		fwrite( $fp, $css );
		fclose( $fp );

		if ( $fp = @fopen( COLUMNS_PLUGIN_DIR . 'css/columns.min.css', 'wb' ) ) {
			// minimize
			// thx to https://github.com/GaryJones/Simple-PHP-CSS-Minification/blob/master/minify.php

			// normalize whitespace
			$css = preg_replace( '/\s+/', ' ', $css );
			// remove spaces before and after comment
			$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
			// remove comment blocks, everything between /* and */, unless
			// preserved with /*! ... */ or /** ... */
			$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );
			// remove ; before }
			$css = preg_replace( '/;(?=\s*})/', '', $css );
			// remove space after , : ; { } */ >
			$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
			// remove space before , ; { } ( ) >
			$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
			// strips leading 0 on decimal values (converts 0.5px into .5px)
			$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
			// strips units if value is 0 (converts 0px to 0)
//			$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
			// converts all zeros value into short-hand
			$css = preg_replace( '/0 0 0 0/', '0', $css );
			// shortern 6-character hex color codes to 3-character where possible
			$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );

			fwrite( $fp, trim( $css ) );
			fclose( $fp );
		}
	// error :/
	} else columns_admin_notice( '<p>' . sprintf( __( 'Unable to create css file for editor columns. Please copy and paste the following css to your styles:<br /><a href="#" onclick="%1$s">Show css</a>', 'columns' ), 'jQuery(this).parent().next().show(); jQuery(this).parent().remove(); return false;' ) . '</p>' . '<pre style="display: none;">' . $css . '</pre>', 'error', true );
}
