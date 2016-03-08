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

	$versions = columns_versions();
	$plugin_data = get_plugin_data( COLUMNS_PLUGIN_FILE );

	$file = plugin_basename( COLUMNS_PLUGIN_FILE );

	// admin js
	wp_enqueue_script( 'columns-update', COLUMNS_PLUGIN_URL . 'js/update.min.js', array( 'jquery' ), '20160308' );

	$changelog = esc_url( COLUMNS_GIT_CHANGELOG );
	$update_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file, 'upgrade-plugin_' . $file );

	// localize columns-admin script
	$translation_array = array(
		'update_message_git' => sprintf( __( 'There is a <a href="%1$s" target="_blank">new version (%2$s) of %3$s</a> available. To update go to terminal and use:<pre><code>$ cd %4$s</code><br /><code>$ git pull</code></pre> ... or <a href="%5$s" class="update-link">update now</a> (you\'ll lose git functionality to pull changes).', 'columns' ),
			$changelog, $versions['master'], $plugin_data['Name'], COLUMNS_PLUGIN_DIR . '/', $update_link ),
		'update_message_files' => sprintf( __( 'There is a new version of %1$s available. <a href="%2$s" target="_blank">View version %3$s details</a> or <a href="%4$s" class="update-link">update now</a>.', 'columns' ),
			$plugin_data['Name'], $changelog, $versions['master'], $update_link )
	);

	// add translation to script
	wp_localize_script( 'columns-update', 'columnsT9n', $translation_array );

	// data to columns-admin script
	$data_array = array(
		'id' => sanitize_title( $plugin_data['Name'] ),
		'pluginDir' => str_replace( $_SERVER['DOCUMENT_ROOT'], '', plugin_dir_path( COLUMNS_PLUGIN_FILE ) ),
		'pluginFile' => str_replace( WP_PLUGIN_DIR . '/', '', COLUMNS_PLUGIN_FILE ),
		'gitChangelog' => COLUMNS_GIT_CHANGELOG,
		'masterZip' => COLUMNS_MASTER_ZIP,
		'URI' => COLUMNS_GIT_URI,
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
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
	curl_setopt( $ch, CURLOPT_USERAGENT, COLUMNS_GIT_URI );
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
		// for now the current version number
		// will be checked in next step
		'new_version' => $value->checked[ $plugin_file ],
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
 * @param $plugin
 */
add_action( 'after_plugin_row', 'columns__after_plugin_row' );
function columns__after_plugin_row( $plugin ) {
}

/**
 * Change download link to git master zip archive.
 *
 * @param array $options
 *
 * @return array
 */
add_filter( 'upgrader_package_options', 'columns__upgrader_package_options' );
function columns__upgrader_package_options( $options ) {
	if ( WP_PLUGIN_DIR . '/' . $options['hook_extra']['plugin'] === COLUMNS_PLUGIN_FILE ) {
		$options['package'] = COLUMNS_MASTER_ZIP;
	}

	return $options;
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
