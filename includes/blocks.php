<?php
/**
 * This file handles the registration of Gutenberg blocks for the WordPress editor.
 *
 * @package jQuestPlugin
 */

namespace jQuestPlugin;

add_action( 'init', 'jQuestPlugin\init_blocks' );

/**
 * The 'init_blocks' function is used to initialize and register custom Gutenberg blocks for the WordPress editor.
 * It scans the '/blocks/build/' directory for block files and registers them.
 */
function init_blocks() {
	// Define the path to the directory where the built blocks are stored.
	$folder = JQUEST_PLUGIN_PATH . '/blocks/build/';

	if ( is_dir( $folder ) ) {
		$blocks = apply_filters( 'broiler_get_blocks', array_diff( scandir( $folder ), array( '..', '.' ) ) );

		foreach ( $blocks as $block ) {
			if ( is_dir( $folder . $block ) && file_exists( $folder . $block . '/block.json' ) ) {
				register_block_type( $folder . $block );
			}
		}
	}
}
