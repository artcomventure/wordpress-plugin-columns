<?php

/**
 * Plugin Name: Columns
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-columns
 * Description: Extends WP Editor with columns.
 * Version: 1.2.0
 * Author: artcom venture GmbH
 * Author URI: http://www.artcom-venture.de/
 */

/**
 * On activation.
 */
register_deactivation_hook( __FILE__, 'columns_versions' );

/**
 * @param bool $cache
 */
function columns_versions( $cache = TRUE ) {
	// check versions: force, init or after one hour at the earliest
	if ( $cache && ( $versions = get_transient( 'columns_versions' ) ) && HOUR_IN_SECONDS > ( time() - $versions['last_checked'] ) ) {
		return $versions;
	}

	$plugin_data = get_plugin_data( __FILE__ );

	$versions = array(
		'local' => $plugin_data['Version'],
		'master' => '',
	);

	// call git to check for master version
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'https://github.com/artcomventure/wordpress-plugin-columns' );
	curl_setopt( $ch, CURLOPT_URL, 'https://raw.githubusercontent.com/artcomventure/wordpress-plugin-columns/master/columns.php' );
	$versions['master'] = curl_exec( $ch );
	curl_close( $ch );

	// grep masters version number
	if ( preg_match( '/\* Version: (\d+\.\d+\.\d+)/', $versions['master'], $versions['master'] ) ) {
		$versions['master'] = $versions['master'][1];
	} else {
		$versions['master'] = NULL;
	}

	// got two version numbers
	if ( count( array_filter( $versions ) ) == 2 ) {
		$versions['last_checked'] = time();

		// cache data
		set_transient( 'columns_versions', $versions );
	} else {
		$versions = NULL;
	}

	return $versions;
}

/**
 * Admin.
 */
add_action( 'admin_head', 'columns__admin_head' );
function columns__admin_head() {
	// admin js
	wp_enqueue_script( 'columns-admin', plugins_url( '/js/admin.min.js?' . time(), __FILE__ ) );

	// check if user has permission to edit posts
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
	     // ... and is allowed to use wysiwyg
	     || ! get_user_option( 'rich_editing' ) == 'true'
	) {
		return;
	}

	// add styles to editor
	add_editor_style( plugins_url( '/css/columns.min.css?' . time(), __FILE__ ) );
	add_editor_style( plugins_url( '/css/editor.min.css?' . time(), __FILE__ ) );

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
	$plugin_array['columns'] = WP_PLUGIN_URL . '/columns/js/editor.min.js';

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

/**
 * Git update notification.
 */
add_filter( 'site_transient_update_plugins', 'columns__site_transient_update_plugins' );
function columns__site_transient_update_plugins( $value ) {
	$plugin_file = plugin_basename( __FILE__ );

	// get plugin update value
	if ( isset( $value->response[ $plugin_file ] ) ) {
		$plugin = $value->response[ $plugin_file ];
		unset( $value->response[ $plugin_file ] );
	} elseif ( isset( $value->no_update[ $plugin_file ] ) ) {
		$plugin = $value->no_update[ $plugin_file ];
		unset( $value->no_update[ $plugin_file ] );
	}

	if ( !empty( $plugin ) ) {
		$plugin_data = get_plugin_data( __FILE__ );

//		$plugin->url = $plugin_data['PluginURI'];
//		$plugin->package = 'https://github.com/artcomventure/wordpress-plugin-columns/archive/master.zip';

		if ( $versions = columns_versions() ) {
			$plugin->new_version = $versions['master'];

			if ( version_compare( $versions['master'], $versions['local'], '>' ) ) {
				$value->response[ $plugin_file ] = $plugin;
			} else {
				$value->no_update[ $plugin_file ] = $plugin;
			}
		}
	}

	return $value;
}

/**
 * Delete traces on deactivation.
 */
register_deactivation_hook( __FILE__, 'columns_deactivate' );
function columns_deactivate() {
	delete_transient( 'columns_versions' );
}
