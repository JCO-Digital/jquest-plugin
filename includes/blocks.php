<?php
/**
 * This file handles the registration of Gutenberg blocks for the WordPress editor.
 *
 * @package SuperQuestPlugin
 */

namespace SuperQuestPlugin;

add_action( 'init', 'SuperQuestPlugin\init_blocks' );

/**
 * The 'init_blocks' function is used to initialize and register custom Gutenberg blocks for the WordPress editor.
 * It scans the '/blocks/build/' directory for block files and registers them.
 */
function init_blocks() {
	// Define the path to the directory where the built blocks are stored.
	$folder = SUPERQUEST_PLUGIN_PATH . '/blocks/build/';

	if ( is_dir( $folder ) ) {
		$blocks = apply_filters( 'broiler_get_blocks', array_diff( scandir( $folder ), array( '..', '.' ) ) );

		foreach ( $blocks as $block ) {
			if ( is_dir( $folder . $block ) && file_exists( $folder . $block . '/block.json' ) ) {
				register_block_type( $folder . $block );
			}
		}
	}
}

/**
 * Flag option marking that the one-time rewrite of the old block name in
 * post_content has run.
 */
const BLOCK_NAME_MIGRATION_FLAG = 'superquest_block_name_migrated';

/**
 * Whether a parsed block is the inserter, under either of its names.
 *
 * @param string|null $block_name The parsed block's name.
 *
 * @return bool
 */
function is_inserter_block( ?string $block_name ): bool {
	return SUPERQUEST_BLOCK_NAME === $block_name || SUPERQUEST_LEGACY_BLOCK_NAME === $block_name;
}

/**
 * Whether the post being rendered holds an inserter block, under either name.
 *
 * @return bool
 */
function post_has_inserter_block(): bool {
	return has_blocks()
		&& ( has_block( SUPERQUEST_BLOCK_NAME ) || has_block( SUPERQUEST_LEGACY_BLOCK_NAME ) );
}

/**
 * One-time rewrite of the block name in post_content.
 *
 * A block's name is part of the HTML comment it serialises into, and the block
 * editor has no deprecation for a renamed block — content naming the old one
 * would show up as an unrecognised block. So the delimiters are rewritten in
 * place, once, and the flag below keeps it from ever running again.
 *
 * Revisions are rewritten too, so restoring one does not put the old name back.
 *
 * @return void
 */
function migrate_block_name(): void {
	if ( get_option( BLOCK_NAME_MIGRATION_FLAG ) ) {
		return;
	}

	global $wpdb;

	// Matches both the opening `<!-- wp:name ... -->` and the closing
	// `<!-- /wp:name -->`, since the latter contains the former's needle.
	$needle = 'wp:' . SUPERQUEST_LEGACY_BLOCK_NAME;
	$like   = '%' . $wpdb->esc_like( $needle ) . '%';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$ids = $wpdb->get_col(
		$wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE %s", $like )
	);

	if ( ! empty( $ids ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts}
				SET post_content = REPLACE( post_content, %s, %s )
				WHERE post_content LIKE %s",
				$needle,
				'wp:' . SUPERQUEST_BLOCK_NAME,
				$like
			)
		);

		foreach ( $ids as $id ) {
			clean_post_cache( (int) $id );
		}
	}

	update_option( BLOCK_NAME_MIGRATION_FLAG, 1 );
}

add_action( 'admin_init', __NAMESPACE__ . '\migrate_block_name' );
