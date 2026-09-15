<?php // phpcs:ignore Squiz.Commenting.ClassComment.Missing
const SUPERQUEST_COMPOSER_AUTOLOADER          = '/vendor/autoload.php';
const SUPERQUEST_PREFIXED_COMPOSER_AUTOLOADER = '/vendor-prefixed/autoload.php';
const SUPERQUEST_TABLE_PREFIX                 = 'superquest_';
const SUPERQUEST_OPTION_PREFIX                = 'superquest_';

const SUPERQUEST_TEXT_DOMAIN = 'superquest';

// Name the inserter block serialises under in post_content.
const SUPERQUEST_BLOCK_NAME = 'superquest-inserter/superquest-inserter';

// The name it serialised under before the plugin was renamed. Content still
// carries it until migrate_block_name() has rewritten it.
const SUPERQUEST_LEGACY_BLOCK_NAME = 'jquest-inserter/jquest-inserter';

const SUPERQUEST_PLUGIN_PATH = __DIR__;
define( 'SUPERQUEST_PLUGIN_URI', plugin_dir_url( __FILE__ ) );

// Single source of truth for the version: the plugin header in jquest-plugin.php.
define(
	'SUPERQUEST_PLUGIN_VERSION',
	get_file_data( __DIR__ . '/jquest-plugin.php', array( 'Version' => 'Version' ) )['Version']
);
