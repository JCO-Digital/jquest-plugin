<?php
/**
 * Plugin Name:     JQUEST Plugin
 * Plugin URI:      https://jco.fi
 * Description:     A plugin for inserting JQUESTs into your site.
 * Author:          J&Co Digital Oy
 * Author URI:      https://jco.fi
 * Text Domain:     jcore-broiler
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         JcoreBroiler
 */

namespace JcoreBroiler;

use JcoreBroiler\Options\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/consts.php';

// Load regular composer autoloader.
if ( is_readable( __DIR__ . JQUEST_COMPOSER_AUTOLOADER ) ) {
	require_once __DIR__ . JQUEST_COMPOSER_AUTOLOADER;
} elseif ( is_readable( ABSPATH . JQUEST_COMPOSER_AUTOLOADER ) ) {
	require_once ABSPATH . JQUEST_COMPOSER_AUTOLOADER;
}

// Load prefixed composer autoloader.
if ( is_readable( __DIR__ . JQUEST_PREFIXED_COMPOSER_AUTOLOADER ) ) {
	require_once __DIR__ . JQUEST_PREFIXED_COMPOSER_AUTOLOADER;
} elseif ( is_readable( ABSPATH . JQUEST_PREFIXED_COMPOSER_AUTOLOADER ) ) {
	require_once ABSPATH . JQUEST_PREFIXED_COMPOSER_AUTOLOADER;
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/includes/acf.php';
require_once __DIR__ . '/includes/blocks.php';

/**
 * Checks the prerequisites for the plugin.
 *
 * @return bool
 */
function check_prerequisites(): bool {
	return true;
	$pass = ( is_readable( __DIR__ . JQUEST_COMPOSER_AUTOLOADER ) ||
	          is_readable( ABSPATH . JQUEST_COMPOSER_AUTOLOADER ) ) &&
	        ( is_readable( __DIR__ . JQUEST_PREFIXED_COMPOSER_AUTOLOADER ) ||
	          is_readable( ABSPATH . JQUEST_PREFIXED_COMPOSER_AUTOLOADER ) );

	if ( $pass ) {
		return true;
	}
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	\deactivate_plugins( \plugin_basename( __FILE__ ) );
	// Ignore this as we are not doing anything else with the get variable than checking/unsetting it.
	// phpcs:ignore
	if ( isset( $_GET['activate'] ) ) {
		// phpcs:ignore
		unset( $_GET['activate'] );
	}
	add_action( 'admin_notices', __NAMESPACE__ . '\dependencies_errors' );
	return false;
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\check_prerequisites' );

/**
 * Initializes the plugins parts.
 *
 * @return void
 */
function initialize_plugin(): void {
	// Bootstrap the plugins parts, comment out the ones you don't need.
	Database\Bootstrap::init();
	Docs\Bootstrap::init();
	RestAPI\Bootstrap::init();
	Options\Bootstrap::init();
}

/**
 * The registration function for the plugin.
 *
 * @return void
 */
function register_plugin_activation_hook(): void {
	$pass = check_prerequisites();
	if ( ! $pass ) {
		return;
	}
	// Create the database tables on plugin activation.
	Database\Bootstrap::create_tables();
}


/**
 * Loads the translations.
 *
 * @return void
 */
function load_translations(): void {
	load_plugin_textdomain( JQUEST_TEXT_DOMAIN, false, basename( __DIR__ ) . '/languages' );
}

/**
 * Fetch the JQUESTs from Firestore.
 * This function is called when the 'jquest__organization_id' option is updated.
 *
 * @param mixed  $old_value The old value of the 'jquest__organization_id' option.
 * @param mixed  $value The new value of the 'jquest__organization_id' option.
 * @param string $option The name of the option.
 *
 * @return void
 */
function fetch_jquests( $old_value, $value, $option ) {
	$api_url           = 'https://europe-north1-jquest-e67dc.cloudfunctions.net/organizationGames-getorganizationgames?orgId=' . $value;
	$api_response      = wp_remote_get( $api_url,
		array(
			'timeout' => 30,
		)
	);
	$api_response_body = wp_remote_retrieve_body( $api_response );
	$decoded_response  = json_decode( $api_response_body, false );
	if ( is_null( $decoded_response ) ) {
		Option::set( 'organization_message', 'Failed to fetch JQUESTs from Firestore.' );
	}

	$message = $decoded_response->message ?? '';
	Option::set( 'organization_message', $message );

	if ( ! $decoded_response->success === true ) {
		Option::remove( 'organization_games' );
		return;
	}
	Option::set( 'organization_games', $decoded_response->data );
}
add_action( 'update_option_jquest__organization_id', __NAMESPACE__ . '\fetch_jquests', 10, 3 );

/**
 * Wrapper function for fetch_jquests() to be called when the 'jquest__organization_id' option is added.
 *
 * @param mixed $option The name of the option.
 * @param mixed $value The new value of the 'jquest__organization_id' option.
 * @return void
 */
function fetch_jquests_wrapper( $option, $value ) {
	fetch_jquests( null, $value, $option );
}
add_action( 'add_option_jquest__organization_id', __NAMESPACE__ . '\fetch_jquests_wrapper', 10, 2 );

/**
 * Adds the organization message and games to the settings page context.
 *
 * @param array $context The context array.
 *
 * @return mixed
 */
function add_context_to_settings_page( $context ) {
	$context['organization_message'] = Option::get( 'organization_message' );
	$context['organization_games']   = Option::get( 'organization_games' );
	return $context;
}
add_filter( 'timber_context_jquest-settings', __NAMESPACE__ . '\add_context_to_settings_page' );

register_activation_hook( __FILE__, __NAMESPACE__ . '\register_plugin_activation_hook' );
add_action( 'admin_init', __NAMESPACE__ . '\check_prerequisites' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_translations' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\initialize_plugin' );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\my_plugin_settings' );

/**
 * Adds a link to the settings page to the plugins page.
 *
 * @param array $settings The settings array.
 *
 * @return array
 */
function my_plugin_settings( $settings ) {
	$settings[] = '<a href="' . get_admin_url( null, 'options-general.php?page=jquest-settings' ) . '">Settings</a>';
	return $settings;
}
