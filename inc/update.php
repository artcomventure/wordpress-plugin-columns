<?php

/**
 * Add styles and scripts to plugin.php.
 */
add_action( 'admin_enqueue_scripts', 'columns_update__admin_enqueue_scripts' );
function columns_update__admin_enqueue_scripts( $hook ) {
	// only on 'Plugins' page
	if ( $hook != 'plugins.php' ) {
		return;
	}

	// admin js
	wp_enqueue_script( 'columns-update', COLUMNS_PLUGIN_URL . 'js/update.min.js', array( 'jquery' ), '20160309' );

	// data to columns-admin script
	$data_array = array(
		'pluginFile' => str_replace( WP_PLUGIN_DIR . '/', '', COLUMNS_PLUGIN_FILE ),
		'gitURI' => COLUMNS_GIT_URI,
	);

	// add data to script
	wp_localize_script( 'columns-update', 'columnsData', $data_array );
}

/**
 * Get local and master (git) version number.
 *
 * @param bool $cache
 *
 * @return array|null
 */
function columns_versions( $cache = TRUE ) {
	$plugin_data = get_plugin_data( COLUMNS_PLUGIN_FILE );

	// use cached data
	if ( $cache && ( $versions = get_transient( 'columns_versions' ) ) && HOUR_IN_SECONDS > ( time() - $versions['last_checked'] ) ) {
		// replace 'local' with current one (in case of update)
		$versions['local'] = $plugin_data['Version'];

		return $versions;
	}

	$versions = array(
		'local' => $plugin_data['Version'],
		'master' => '',
	);

	// call git to check for master version
	$versions['master'] = wp_remote_get( 'https://raw.githubusercontent.com/artcomventure/wordpress-plugin-columns/master/columns.php' );

	if ( ! is_wp_error( $versions['master'] ) ) {
		// grep masters version number
		if ( preg_match( '/\* Version: (\d+\.\d+\.\d+)/', $versions['master']['body'], $versions['master'] ) ) {
			$versions['master'] = $versions['master'][1];
		} else {
			$versions['master'] = NULL;
		}
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
 * Git update notification.
 */
add_filter( 'site_transient_update_plugins', 'columns__site_transient_update_plugins' );
function columns__site_transient_update_plugins( $value ) {
	$plugin_file = plugin_basename( COLUMNS_PLUGIN_FILE );

	// remove plugin entry (in case of name conflict with existing https://wordpress.org/plugins/ plugin)
	foreach ( array( 'response', 'no_update' ) as $group ) {
		if ( isset( $value->{$group}[ $plugin_file ] ) ) {
			unset( $value->{$group}[ $plugin_file ] );
			break;
		}
	}

	// create plugin object
	$plugin = (object) array(
        'plugin' => $plugin_file,
		// for now the current version number
		// will be checked in next step
		'new_version' => $value->checked[ $plugin_file ],
        'url' => COLUMNS_GIT_URI,
        'package' => COLUMNS_MASTER_ZIP,
	);

	// check versions
	if ( $versions = columns_versions() ) {
		// mark plugin for update
		if ( version_compare( $versions['master'], $versions['local'], '>' ) ) {
			$plugin->new_version = $versions['master'];
			$value->response[ $plugin_file ] = $plugin;
		} else {
			$value->no_update[ $plugin_file ] = $plugin;
		}
	}

	return $value;
}

/**
 * Rename plugin folder (wordpress-plugin-columns-master) to current one.
 *
 * @param $source
 * @param $remote_source
 * @param $this
 * @param $hook_extra
 *
 * @return string
 */
add_filter( 'upgrader_source_selection', 'columns__upgrader_source_selection', 10, 4 );
function columns__upgrader_source_selection( $source, $remote_source, $this, $hook_extra ) {
	if ( WP_PLUGIN_DIR . '/' . $hook_extra['plugin'] === COLUMNS_PLUGIN_FILE ) {
		// current plugin folder and source file
		list( $folder, $file ) = explode( '/', $hook_extra['plugin'] );

		// rename git folder name (wordpress-plugin-columns-master)
		if ( $source != ( $new_source = $remote_source . '/' . $folder . '/' ) ) {
			rename( $source, $new_source );

			// set source to rename folder
			$source = $new_source;
		}
	}

	return $source;
}
