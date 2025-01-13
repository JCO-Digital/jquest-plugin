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
 * Fetch the JQUESTs from Firestore.
 * This function is called when the 'jquest__organization_id' option is updated.
 *
 * @param mixed  $value The new value of the 'jquest__organization_id' option.
 *
 * @return void
 */
function fetch_jquests( $value ) {

	$api_url           = 'https://europe-north1-jquest-e67dc.cloudfunctions.net/organizationGames-getorganizationgames?orgId=' . $value;
	$api_response      = wp_remote_get( $api_url,
		array(
			'timeout' => 30,
		)
	);
	$api_response_body = wp_remote_retrieve_body( $api_response );
	$decoded_response  = json_decode( $api_response_body, false );
	if ( is_null( $decoded_response ) ) {
		update_option('jquest_org_message', 'Failed to fetch JQUESTs from Firestore.');
	}

	$message = $decoded_response->message ?? '';
	update_option('jquest_org_message', $message);

	if ( ! $decoded_response->success === true ) {
		update_option('jquest_org_games', []);
		return;
	}

	update_option('jquest_org_games', $decoded_response->data);
}
