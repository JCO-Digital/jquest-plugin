<?php // phpcs:ignore Squiz.Commenting.ClassComment.Missing
const JQUEST_COMPOSER_AUTOLOADER          = '/vendor/autoload.php';
const JQUEST_PREFIXED_COMPOSER_AUTOLOADER = '/vendor-prefixed/autoload.php';
const JQUEST_TABLE_PREFIX                 = 'jquest_';
const JQUEST_OPTION_PREFIX                = 'jquest_';

const JQUEST_TEXT_DOMAIN = 'jquest';

const JQUEST_PLUGIN_PATH = __DIR__;
define( 'JQUEST_PLUGIN_URI', plugin_dir_url( __FILE__ ) );

// Single source of truth for the version: the plugin header in jquest-plugin.php.
define(
	'JQUEST_PLUGIN_VERSION',
	get_file_data( __DIR__ . '/jquest-plugin.php', array( 'Version' => 'Version' ) )['Version']
);
