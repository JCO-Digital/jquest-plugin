<?php
/**
 * Helper functions for the plugin.
 *
 * @package SuperQuestPlugin
 */

namespace SuperQuestPlugin;

/**
 * Register script wrapper.
 *
 * @param string $name Script name.
 * @param string $file Filename.
 * @param array  $dependencies Dependencies.
 * @param string $version Optional version number.
 */
function script_register( string $name, string $file, array $dependencies = array(), string $version = '' ): void {
	$info = get_file_info( $file, $version );

	if ( false !== $info ) {
		wp_register_script(
			$name,
			$info['uri'],
			$dependencies,
			$info['version'],
			true
		);
	}
}

/**
 * Register style wrapper.
 *
 * @param string $name Style name.
 * @param string $file Filename.
 * @param array  $dependencies Dependencies.
 * @param string $version Optional version number.
 */
function style_register( string $name, string $file, array $dependencies = array(), string $version = '' ): void {
	$info = get_file_info( $file, $version );

	if ( false !== $info ) {
		wp_register_style(
			$name,
			$info['uri'],
			$dependencies,
			$info['version']
		);
	}
}

/**
 * Get file info for script/style registration.
 *
 * @param string $file Filename.
 * @param string $version Optional version number.
 *
 * @return bool|string[]
 */
function get_file_info( string $file, string $version = '' ): array|bool {
	if ( ! empty( $version ) ) {
		$version .= '-';
	}
	$location = array(
		'path' => join_path( untrailingslashit( SUPERQUEST_PLUGIN_PATH ), $file ),
		'uri'  => join_path( untrailingslashit( SUPERQUEST_PLUGIN_URI ), $file ),
	);
	if ( file_exists( $location['path'] ) ) {
		$version .= filemtime( $location['path'] );

		return array(
			'uri'     => $location['uri'],
			'path'    => $location['path'],
			'version' => $version,
		);
	}
	return false;
}

/**
 * A function that joins together all parts of a path.
 *
 * @param string $path Base path.
 * @param string ...$parts Path parts to be joined.
 *
 * @return string
 */
function join_path( string $path, string ...$parts ): string {
	foreach ( $parts as $part ) {
		$path .= '/' . trim( $part, '/ ' );
	}

	return $path;
}

/**
 * URL of the SuperQuest wordmark.
 *
 * The mark is drawn in white and cyan, so whatever it sits on has to be dark
 * enough for it to read.
 *
 * @return string
 */
function logo_url(): string {
	return SUPERQUEST_PLUGIN_URI . 'assets/img/Super-Quest-Color-Gradient-RGB.svg';
}

/**
 * Can be used to render a template file.
 *
 * @param string $template The template file name. E.g. "my-template".
 * @param array  $data An array of data to be passed to the template. Will be available as $superquest_my_variable.
 *
 * @return string
 */
function render_template( string $template, array $data = array() ): string {
	$final_path = sprintf( '%s/views/%s.php', untrailingslashit( SUPERQUEST_PLUGIN_PATH ), $template );
	if ( ! file_exists( $final_path ) ) {
		return '';
	}
	ob_start();
	extract( $data, EXTR_PREFIX_ALL, 'superquest' ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	require $final_path;
	return ob_get_clean();
}

/**
 * Normalises a quest collection returned by the API into a list of objects,
 * dropping anything that is not one.
 *
 * @param mixed $quests Quest collection returned by the API.
 *
 * @return array
 */
function normalize_quests( $quests ): array {
	if ( ! is_array( $quests ) ) {
		return array();
	}

	$normalized = array();
	foreach ( $quests as $quest ) {
		if ( is_array( $quest ) ) {
			$quest = (object) $quest;
		}
		if ( ! is_object( $quest ) ) {
			continue;
		}

		$normalized[] = $quest;
	}

	return $normalized;
}

/**
 * Fetch the SuperQuests from Firestore.
 * This function is called when the 'superquest__organization_id' option is updated.
 *
 * @param mixed $value The new value of the 'superquest__organization_id' option.
 *
 * @return void
 */
function fetch_quests( $value ) {
	// The `games` in the path is the vendor's endpoint name, not ours, so it
	// stays as it is however the plugin names quests internally.
	$api_url           = 'https://api.jquest.fi/organizationgames-getorganizationgames?v2=true&orgId=' . $value;
	$api_response      = wp_remote_get(
		$api_url,
		array(
			'timeout' => 30,
		)
	);
	$api_response_body = wp_remote_retrieve_body( $api_response );
	$decoded_response  = json_decode( $api_response_body, false );
	if ( is_null( $decoded_response ) ) {
		update_option( 'superquest_org_message', 'Failed to fetch SuperQuests from Firestore.' );
		update_option( 'superquest_org_quests', array() );
		return;
	}

	$message = $decoded_response->message ?? '';
	update_option( 'superquest_org_message', $message );

	if ( ! isset( $decoded_response->success ) || true !== $decoded_response->success ) {
		update_option( 'superquest_org_quests', array() );
		return;
	}

	// The API still returns the retired generation in `data`. Only `quests` is
	// read, so nothing but the current generation is ever offered.
	update_option( 'superquest_org_quests', normalize_quests( $decoded_response->quests ?? array() ) );
}

/**
 * Flag option marking that the one-time move off the `jquest_` option prefix
 * has run.
 */
const OPTION_PREFIX_MIGRATION_FLAG = 'superquest_option_prefix_migrated';

/**
 * One-time move of every stored option from the `jquest_` prefix to
 * `superquest_`.
 *
 * Done over the whole prefix rather than a list of names because the popup
 * settings were keyed per language — `jquest_popup_v2_<lang>_quests` — so the
 * full set is only known at runtime, and because it then also carries the
 * legacy options the later migrations look for.
 *
 * Runs ahead of those migrations on the same hook, so they see the renamed
 * options rather than the ones this has just deleted.
 *
 * @return void
 */
function migrate_option_prefix(): void {
	if ( get_option( OPTION_PREFIX_MIGRATION_FLAG ) ) {
		return;
	}

	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$legacy_options = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( 'jquest_' ) . '%'
		)
	);

	foreach ( $legacy_options as $legacy_option ) {
		$renamed = 'superquest_' . substr( $legacy_option, strlen( 'jquest_' ) );

		// Never overwrite a value already saved under the new name.
		if ( null === get_option( $renamed, null ) ) {
			update_option( $renamed, get_option( $legacy_option ) );
		}

		delete_option( $legacy_option );
	}

	update_option( OPTION_PREFIX_MIGRATION_FLAG, 1 );
}

add_action( 'admin_init', __NAMESPACE__ . '\migrate_option_prefix', 1 );

/**
 * One-time move of the fetched quest list from its old `games` option name.
 *
 * Without this the list would read empty after an update until an admin pressed
 * Refresh, and every popup entry would report its saved quest as missing.
 *
 * @return void
 */
function migrate_org_quests_option(): void {
	// migrate_option_prefix() normally renames the option before this runs, so
	// the prefixed name is checked first; the raw one covers the case where it
	// has not.
	$legacy_name = null !== get_option( 'superquest_org_games', null )
		? 'superquest_org_games'
		: 'jquest_org_games';

	$legacy = get_option( $legacy_name, null );
	if ( null === $legacy ) {
		return;
	}

	// Never overwrite a list the renamed option has already fetched.
	if ( null === get_option( 'superquest_org_quests', null ) ) {
		update_option( 'superquest_org_quests', $legacy );
	}

	delete_option( $legacy_name );
}

add_action( 'admin_init', __NAMESPACE__ . '\migrate_org_quests_option' );
