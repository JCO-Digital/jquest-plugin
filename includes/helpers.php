<?php
/**
 * Helper functions for the plugin.
 *
 * @package jQuestPlugin
 */

namespace jQuestPlugin;

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
		'path' => join_path( untrailingslashit( JQUEST_PLUGIN_PATH ), $file ),
		'uri'  => join_path( untrailingslashit( JQUEST_PLUGIN_URI ), $file ),
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
 * Can be used to render a template file.
 *
 * @param string $template The template file name. E.g. "my-template".
 * @param array  $data An array of data to be passed to the template. Will be available as $jquest_my_variable.
 *
 * @return string
 */
function render_template( string $template, array $data = array() ): string {
	$final_path = sprintf( '%s/views/%s.php', untrailingslashit( JQUEST_PLUGIN_PATH ), $template );
	if ( ! file_exists( $final_path ) ) {
		return '';
	}
	ob_start();
	extract( $data, EXTR_PREFIX_ALL, 'jquest' ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	require $final_path;
	return ob_get_clean();
}

/**
 * Adds the API generation to every valid quest in a collection.
 *
 * @param mixed  $quests  Quest collection returned by the API.
 * @param string $version Quest generation.
 *
 * @return array
 */
function add_jquest_versions( $quests, string $version ): array {
	if ( ! is_array( $quests ) ) {
		return array();
	}

	$versioned_quests = array();
	foreach ( $quests as $quest ) {
		if ( is_array( $quest ) ) {
			$quest = (object) $quest;
		}
		if ( ! is_object( $quest ) ) {
			continue;
		}

		$quest->version     = $version;
		$versioned_quests[] = $quest;
	}

	return $versioned_quests;
}

/**
 * Gets the generation of a stored quest.
 *
 * @param string     $quest_id Quest ID.
 * @param array|null $quests   Optional quest collection. Uses the stored quests by default.
 *
 * @return string
 */
function get_jquest_version( string $quest_id, ?array $quests = null ): string {
	$quests = $quests ?? get_option( 'jquest_org_games', array() );

	foreach ( $quests as $quest ) {
		if ( is_object( $quest ) && isset( $quest->id ) && $quest_id === $quest->id ) {
			return isset( $quest->version ) && 'v2' === $quest->version ? 'v2' : 'v1';
		}
	}

	return 'v1';
}

/**
 * Fetch the JQUESTs from Firestore.
 * This function is called when the 'jquest__organization_id' option is updated.
 *
 * @param mixed $value The new value of the 'jquest__organization_id' option.
 *
 * @return void
 */
function fetch_jquests( $value ) {
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
		update_option( 'jquest_org_message', 'Failed to fetch JQUESTs from Firestore.' );
		update_option( 'jquest_org_games', array() );
		return;
	}

	$message = $decoded_response->message ?? '';
	update_option( 'jquest_org_message', $message );

	if ( ! isset( $decoded_response->success ) || true !== $decoded_response->success ) {
		update_option( 'jquest_org_games', array() );
		return;
	}

	$v1_quests = add_jquest_versions( $decoded_response->data ?? array(), 'v1' );
	$v2_quests = add_jquest_versions( $decoded_response->quests ?? array(), 'v2' );

	update_option( 'jquest_org_games', array_merge( $v1_quests, $v2_quests ) );
}
