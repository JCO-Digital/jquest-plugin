<?php
/**
 * Finds every place the SuperQuest block is used across the site.
 *
 * @package SuperQuestPlugin\Usage
 */

namespace SuperQuestPlugin\Usage;

/**
 * The posts whose content holds at least one SuperQuest block.
 *
 * The block serialises into post_content as an HTML comment naming it, so one
 * LIKE finds every use. A leading wildcard cannot use an index, so this is a
 * full scan of wp_posts — acceptable because it only runs when an admin opens
 * the usage screen, never on the front end.
 *
 * Revisions and auto-drafts are skipped; everything else is kept, including
 * synced patterns (wp_block) and template parts, so nothing that can render a
 * quest is missed. Trashed posts are kept too, and shown with their status, so
 * a quest sitting in the trash is not mistaken for one that is gone.
 *
 * @return array<int, object> Post rows, grouped by type.
 */
function find_posts_with_block(): array {
	global $wpdb;

	// Content the block-name migration has not reached yet still carries the
	// old name, so both are matched.
	$like        = '%' . $wpdb->esc_like( '<!-- wp:' . SUPERQUEST_BLOCK_NAME ) . '%';
	$legacy_like = '%' . $wpdb->esc_like( '<!-- wp:' . SUPERQUEST_LEGACY_BLOCK_NAME ) . '%';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_type, post_status, post_content
			FROM {$wpdb->posts}
			WHERE ( post_content LIKE %s OR post_content LIKE %s )
			AND post_type != 'revision'
			AND post_status != 'auto-draft'
			ORDER BY post_type ASC, post_title ASC",
			$like,
			$legacy_like
		)
	);
}

/**
 * The posts that embed a given synced pattern.
 *
 * A pattern is stored as its own wp_block post and referenced by ID, so a page
 * using one holds no SuperQuest block of its own and never turns up in the scan
 * above. Without this the report would name the pattern and none of the pages
 * that actually render it.
 *
 * @param int $ref The wp_block post ID.
 *
 * @return array<int, object> Post rows.
 */
function find_pattern_hosts( int $ref ): array {
	global $wpdb;

	$like = '%' . $wpdb->esc_like( '"ref":' . $ref ) . '%';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_type, post_status, post_content
			FROM {$wpdb->posts}
			WHERE post_content LIKE %s
			AND post_type != 'revision'
			AND post_status != 'auto-draft'
			ORDER BY post_title ASC",
			$like
		)
	);

	// The LIKE also matches longer IDs that merely start with this one, e.g.
	// "ref":12 inside "ref":1234, so confirm each hit on a digit boundary.
	return array_values(
		array_filter(
			$rows,
			function ( $row ) use ( $ref ): bool {
				return 1 === preg_match( '/"ref"\s*:\s*' . $ref . '(?!\d)/', $row->post_content );
			}
		)
	);
}

/**
 * Pulls every SuperQuest block out of a parsed block tree, however deeply it is
 * nested inside groups, columns and the like.
 *
 * @param array $blocks The blocks to walk.
 *
 * @return array<int, array{quest_id: string, popup: bool, auto: bool}>
 */
function collect_blocks( array $blocks ): array {
	$found = array();

	foreach ( $blocks as $block ) {
		if ( \SuperQuestPlugin\is_inserter_block( $block['blockName'] ) ) {
			$attrs = $block['attrs'] ?? array();

			// Content saved before the attribute was renamed still holds
			// `selectedGame`. The block's deprecation rewrites it, but only once
			// an editor opens and re-saves the post, so the scan reads both.
			$quest_id = $attrs['selectedQuest'] ?? $attrs['selectedGame'] ?? '';

			$found[] = array(
				'quest_id' => (string) $quest_id,
				'popup'    => ! empty( $attrs['popup'] ),
				'auto'     => ! empty( $attrs['popupAuto'] ),
			);
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = array_merge( $found, collect_blocks( $block['innerBlocks'] ) );
		}
	}

	return $found;
}

/**
 * Quest titles by ID, taken from the last fetched quest list.
 *
 * @return array<string, string>
 */
function quest_titles(): array {
	$titles = array();

	foreach ( (array) get_option( 'superquest_org_quests', array() ) as $quest ) {
		if ( is_object( $quest ) && isset( $quest->id ) ) {
			$titles[ (string) $quest->id ] = isset( $quest->title ) ? (string) $quest->title : '';
		}
	}

	return $titles;
}

/**
 * Every use of the SuperQuest block on the site, ready for the usage table.
 *
 * @return array<int, array{
 *     id: int,
 *     title: string,
 *     type: string,
 *     status: string,
 *     quests: array,
 *     hosts: array
 * }>
 */
function scan(): array {
	$titles  = quest_titles();
	$entries = array();

	foreach ( find_posts_with_block() as $row ) {
		$quests = collect_blocks( parse_blocks( $row->post_content ) );

		// The LIKE can match content that only mentions the block name, such as
		// this very comment quoted inside a code block. Parsing settles it.
		if ( empty( $quests ) ) {
			continue;
		}

		foreach ( $quests as $index => $quest ) {
			$quests[ $index ]['title'] = $titles[ $quest['quest_id'] ] ?? '';
		}

		$entries[] = array(
			'id'     => (int) $row->ID,
			'title'  => (string) $row->post_title,
			'type'   => (string) $row->post_type,
			'status' => (string) $row->post_status,
			'quests' => $quests,
			'hosts'  => 'wp_block' === $row->post_type ? find_pattern_hosts( (int) $row->ID ) : array(),
		);
	}

	return $entries;
}
