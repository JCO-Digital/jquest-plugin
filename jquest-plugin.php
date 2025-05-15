<?php
/**
 * Plugin Name:     JQUEST Plugin
 * Plugin URI:      https://jco.fi
 * Description:     A plugin for inserting JQUESTs into your site.
 * Author:          J&Co Digital Oy
 * Author URI:      https://jco.fi
 * Text Domain:     jquest-
 * Domain Path:     /languages
 * Version: 0.4.7
 *
 * @package         jQuestPlugin
 */

namespace jQuestPlugin;

use jQuestPlugin\Options\OptionsPage;

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
require_once __DIR__ . '/includes/blocks.php';
require_once __DIR__ . '/includes/fields.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/table.php';

/**
 * Checks the prerequisites for the plugin.
 *
 * @return bool
 */
function check_prerequisites(): bool {

	$pass = ( is_readable( __DIR__ . JQUEST_COMPOSER_AUTOLOADER ) ||
				is_readable( ABSPATH . JQUEST_COMPOSER_AUTOLOADER ) );

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
	RestAPI\Bootstrap::init();
	OptionsPage::get_instance();
}

/**
 * Loads the translations.
 *
 * @return void
 */
function load_translations(): void {
	load_plugin_textdomain( JQUEST_TEXT_DOMAIN, false, basename( __DIR__ ) . '/languages' );
}

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
function my_plugin_settings( $settings ): array {
	$settings[] = '<a href="' . get_admin_url( null, 'options-general.php?page=jquest-options' ) . '">Settings</a>';
	return $settings;
}
