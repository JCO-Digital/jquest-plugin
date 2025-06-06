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

		$results = contains_jquest_insterter($block);

		$has_jquest_blocks = $results['has_jquest_blocks'];
		$needs_staging = $results['needs_staging'];

		// Can only add one script at a time. So might as well break.
		if ( $needs_staging ) {
			break;
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

function contains_jquest_insterter($block): array
{

	$return_value = [
		'has_jquest_blocks' => false,
		'needs_staging' 	=> false
	];

	if ( 'jquest-inserter/jquest-inserter' === $block['blockName'] ) {
		$return_value['has_jquest_blocks'] = true;
		if ( false === $return_value['needs_staging'] ) {
			// Unfortunately, this is the only way to get the default attributes from the block...
			// This could be avoided by just assuming the default value, but then we would need to keep the block.json in sync with this.
			$rendered_block = apply_filters( 'the_content', render_block( $block ) );
			if ( strpos( $rendered_block, 'data-staging="true"' ) !== false ) {
				$return_value['needs_staging'] = true;
			}
		}
	}

	if( array_key_exists( 'innerBlocks', $block ) ) {
		foreach ( $block['innerBlocks'] as $inner_block ) {
			$inner_values = contains_jquest_insterter($inner_block);

			$return_value['has_jquest_blocks'] = $return_value['has_jquest_blocks'] || $inner_values['has_jquest_blocks'];
			$return_value['needs_staging'] = $return_value['needs_staging'] || $inner_values['needs_staging'];
		}
	}

	return $return_value;

}

add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_jquest_script' );
