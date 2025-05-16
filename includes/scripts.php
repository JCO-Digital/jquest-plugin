<?php
/**
 * Handles enqueuing of JQUEST scripts, when the block is present on a page.
 *
 * @package jQuestPlugin\Scripts
 */

namespace jQuestPlugin\Scripts;

/**
 * Checks the post content for JQUEST blocks and inserts the script if found.
 *
 * @return void
 */
function maybe_insert_jquest_script() {
	if ( ! has_blocks() || ! has_block( 'jquest-inserter/jquest-inserter' ) ) {
		return;
	}
	$has_jquest_blocks = false;
	$needs_staging     = false;

	$blocks = parse_blocks( get_the_content() );
	foreach ( $blocks as $block ) {
		if ( 'jquest-inserter/jquest-inserter' === $block['blockName'] ) {
			$has_jquest_blocks = true;
			if ( false === $needs_staging ) {
				// Unfortunately, this is the only way to get the default attributes from the block...
				$rendered_block = apply_filters( 'the_content', render_block( $block ) );
				if ( strpos( $rendered_block, 'data-staging="true"' ) !== false ) {
					$needs_staging = true;
				}
			}
		}
	}
	if ( $has_jquest_blocks ) {
		$url = $needs_staging
		? 'https://files.jquest.fi/jquest/staging/jquest-staging.js'
		: 'https://files.jquest.fi/jquest/jquest.js';

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<script 
				data-cookieconsent-ignore='true' 
				type='module' 
				src='<?php echo $url; ?>'></script>
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_jquest_script' );
