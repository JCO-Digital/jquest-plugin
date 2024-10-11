<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing
namespace jQuestPlugin\Options;

use Timber\Timber;

/**
 * Database class, handles bootstrapping the custom database tables.
 *
 * @package jQuestPlugin\Database
 */
class Bootstrap {

	/**
	 * Initializes the Options class.
	 * You can use OptionsPageBuilder to build options pages here or just leave it as it is.
	 *
	 * @see OptionsPageBuilder
	 * @return void
	 */
	public static function init(): void {
		( new OptionsPageBuilder() )
			->set_menu_title( 'JQUEST Settings' )
			->set_page_title( 'JQUEST Settings' )
			->set_menu_slug( 'jquest-settings' )
			->set_capability( 'manage_options' )
			->set_template( 'options-page.twig' )
			->set_parent_slug( 'options-general.php' )
			->add_setting_field( 'organization_id', 'Organization ID' )
			->add_stylesheet(
				array(
					'handle' => 'jquest-settings',
					'src'    => 'https://unpkg.com/@blaze/css@x.x.x/dist/blaze/blaze.css',
				)
			)
			->add_script(
				array(
					'handle'    => 'blaze-nomodule',
					'src'       => 'https://unpkg.com/@blaze/atoms@x.x.x/dist/blaze-atoms/blaze-atoms.js',
					'deps'      => array(),
					'ver'       => '',
					'in_footer' => false,
				)
			)
			->build();
	}

	/**
	 * Initializes Timber.
	 *
	 * @return void
	 */
	private static function timber_init(): void {
		Timber::$locations = array(
			plugin_dir_path( __FILE__ ) . '../../views',
		);
	}
}
