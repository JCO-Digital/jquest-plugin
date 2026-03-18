<?php
/**
 * Handles enqueuing of JQUEST scripts, when the block is present on a page.
 *
 * @package jQuestPlugin\Scripts
 */

namespace jQuestPlugin\Scripts;

/**
 * Outputs a jQuest script tag, ensuring each URL is only output once per page load.
 *
 * @param string $url The script URL to insert.
 *
 * @return void
 */
function insert_jquest_script( string $url ): void {
	static $inserted = false;

	if ( $inserted ) {
		return;
	}

	$inserted = true;

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<script
		data-cookieconsent="ignore"
		data-ot-ignore
		data-cookieyes="ignore"
		type="module"
		src="<?php echo esc_url( $url ); ?>"></script>
	<?php
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

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
	$version           = false;

	$post = get_post();
	if ( ! $post ) {
		return;
	}

	$blocks = parse_blocks( $post->post_content );
	foreach ( $blocks as $block ) {

		$results = contains_jquest_insterter( $block );

		$has_jquest_blocks = $results['has_jquest_blocks'];
		$version           = $results['version'];
		if ( !!$version ) {
			break;
		}
	}
	if ( $has_jquest_blocks ) {
		if ( $version == 'stable' ) {
			$url = 'https://files.jquest.fi/jquest/stable/jquest-stable.js';
		} else if ( $version == 'latest' ) {
			$url = 'https://files.jquest.fi/jquest/latest/jquest-latest.js';
		}
		if ( isset( $url ) ) {
			insert_jquest_script( $url );
		}
	}
}

function contains_jquest_insterter( $block ): array {

	$return_value = array(
		'has_jquest_blocks' => false,
		'version'           => false
	);

	if ( 'jquest-inserter/jquest-inserter' === $block['blockName'] ) {
		$return_value['has_jquest_blocks'] = true;
		if ( false === $return_value['version'] ) {
			// Unfortunately, this is the only way to get the default attributes from the block...
			// This could be avoided by just assuming the default value, but then we would need to keep the block.json in sync with this.
			$rendered_block = apply_filters( 'the_content', render_block( $block ) );
			if ( strpos( $rendered_block, 'data-version="stable"' ) !== false ) {
				$return_value['version'] = "stable";
			} if ( strpos( $rendered_block, 'data-version="latest"' ) !== false ) {
				$return_value['version'] = "latest";
			}
		}
	}

	if ( array_key_exists( 'innerBlocks', $block ) ) {
		foreach ( $block['innerBlocks'] as $inner_block ) {
			$inner_values = contains_jquest_insterter( $inner_block );

			$return_value['has_jquest_blocks'] = $return_value['has_jquest_blocks'] || $inner_values['has_jquest_blocks'];
			$return_value['version']     = $return_value['version'];
		}
	}

	return $return_value;
}

add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_jquest_script' );

/**
 * Inserts the jQuest popup script when the popup is enabled for the current language.
 *
 * @return void
 */
function maybe_insert_popup_script(): void {
	$lang   = function_exists( 'pll_current_language' ) ? pll_current_language() : 'default';
	$prefix = 'jquest_popup_' . $lang . '_';

	if ( ! get_option( $prefix . 'enabled', 0 ) ) {
		return;
	}

	$url = get_option( $prefix . 'latest_script', 0 )
		? 'https://files.jquest.fi/jquest/latest/jquest-latest.js'
		: 'https://files.jquest.fi/jquest/stable/jquest-stable.js';

	insert_jquest_script( $url );
}

add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_popup_script' );

/**
 * Outputs the jQuest popup div into the footer when the popup is enabled for the current language.
 *
 * @return void
 */
function maybe_insert_popup_div(): void {
	$lang   = function_exists( 'pll_current_language' ) ? pll_current_language() : 'default';
	$prefix = 'jquest_popup_' . $lang . '_';

	if ( ! get_option( $prefix . 'enabled', 0 ) ) {
		return;
	}

	$org_id          = get_option( 'jquest_org_id', '' );
	$quest_id        = get_option( $prefix . 'quest_id', '' );
	$auto            = get_option( $prefix . 'auto', 0 ) ? 'true' : 'false';
	$limit           = (int) get_option( $prefix . 'limit', 0 );
	$attach          = get_option( $prefix . 'attach', 'body' );
	$disable_dismiss  = get_option( $prefix . 'disable_dismiss', 1 ) ? 'true' : 'false';
	$disable_noscroll = get_option( $prefix . 'disable_noscroll', 1 ) ? 'true' : 'false';
	$locale          = $lang === 'default' ? '' : $lang;

	?>
	<div
		class="jquest-app"
		data-new-styles="true"
		data-locale="<?php echo esc_attr( $locale ); ?>"
		data-org-id="<?php echo esc_attr( $org_id ); ?>"
		data-game-id="<?php echo esc_attr( $quest_id ); ?>"
		data-popup="true"
		data-popup-auto="<?php echo esc_attr( $auto ); ?>"
		data-popup-limit="<?php echo esc_attr( $limit ); ?>"
		data-popup-attach="<?php echo esc_attr( $attach ); ?>"
		data-popup-disable-dismiss="<?php echo esc_attr( $disable_dismiss ); ?>"
		data-popup-disable-noscroll="<?php echo esc_attr( $disable_noscroll ); ?>"
	></div>
	<?php
}

add_action( 'wp_footer', __NAMESPACE__ . '\maybe_insert_popup_div' );

/**
 * Outputs the jQuest popup trigger button when enabled.
 *
 * @return void
 */
function maybe_insert_popup_trigger(): void {
	if ( ! get_option( 'jquest_popup_trigger_enabled', 0 ) ) {
		return;
	}

	$lang   = function_exists( 'pll_current_language' ) ? pll_current_language() : 'default';
	$prefix = 'jquest_popup_' . $lang . '_';

	if ( ! get_option( $prefix . 'enabled', 0 ) ) {
		return;
	}

	$label = get_option( $prefix . 'desktop_label', '' );
	$label_mobile = get_option( $prefix . 'mobile_label', '' );
	if ( '' === $label ) {
		$label = get_option( $prefix . 'mobile_label', '' );
	}
	$quest_id = get_option( $prefix . 'quest_id', '' );

	$text_color       = get_option( 'jquest_popup_trigger_text_color', '#1a2e40' );
	$bg_color         = get_option( 'jquest_popup_trigger_bg_color', '#ffffff' );
	$text_hover_color = get_option( 'jquest_popup_trigger_text_hover_color', '#1a2e40' );
	$bg_hover_color        = get_option( 'jquest_popup_trigger_bg_hover_color', '#f0f0f0' );
	$icon_bg_color         = get_option( 'jquest_popup_trigger_icon_bg_color', '#ffffff' );
	$icon_bg_hover_color   = get_option( 'jquest_popup_trigger_icon_bg_hover_color', '' );
	$icon_mode             = get_option( 'jquest_popup_trigger_icon_mode', 'default' );

	$svg_kses = array(
		'svg'    => array( 'xmlns' => true, 'viewBox' => true, 'width' => true, 'height' => true, 'fill' => true ),
		'path'   => array( 'd' => true, 'fill' => true, 'fill-rule' => true, 'clip-rule' => true, 'stroke' => true, 'stroke-width' => true ),
		'g'      => array( 'fill' => true ),
		'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
		'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true, 'rx' => true ),
	);

	if ( 'default' === $icon_mode ) {
		$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2.046 21.8a.5.5 0 0 0 .62.635l4.87-1.515A9.96 9.96 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2Z"/></svg>';
	} elseif ( 'custom' === $icon_mode ) {
		$icon = wp_kses( get_option( 'jquest_popup_trigger_icon_custom', '' ), $svg_kses );
	} else {
		$icon = '';
	}

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<style>
		.jquest-app[data-popup='true'] { display: none; }
		.jquest-popup-toggle {
			position: fixed;
			bottom: 1rem;
			right: 1rem;
			z-index: 99;
			height: 42px;
			max-width: 100%;
			transition: all .3s;
			> * {
				transition: all .3s;
			}

			a {
				position: relative;
				background-color: <?php echo esc_attr( $bg_color ); ?>;
				cursor: pointer;
				border-radius: 25px;
				text-decoration: none;
				color: <?php echo esc_attr( $text_color ); ?>;
				padding: 11px 53px 11px 23px;
				font-weight: 400;
				display: flex;
    		height: 100%;
			      .label{
			      line-height: 18px;
			      }
			}

			&.no-icon a{
				padding: 11px 23px;
			}
		}
		.jquest-popup-toggle a:hover {
			background-color: <?php echo esc_attr( $bg_hover_color ); ?>;
			color: <?php echo esc_attr( $text_hover_color ); ?>;
		}
		.jquest-popup-toggle a .icon-container {
			position: absolute;
			border-radius: 50%;
			background-color: <?php echo esc_attr( $icon_bg_color ); ?>;
			right: 8px;
			top: 50%;
			transform: translateY(-50%);
			height: 29px;
			width: 29px;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		<?php if ( '' !== $icon_bg_hover_color ) : ?>
		.jquest-popup-toggle a:hover .icon-container { background-color: <?php echo esc_attr( $icon_bg_hover_color ); ?>; }
		<?php endif; ?>
		@media (width >= 1024px) {
			.jquest-popup-toggle .mobile-only {
				display: none !important;
			}
		}
		@media (width < 1024px) {
			.jquest-popup-toggle .desktop-only {
				display: none !important;
			}
		}
	</style>
	<div class="jquest-popup-toggle <?php echo isset( $icon ) && '' !== $icon ? 'has-icon' : 'no-icon'; ?>">
		<a href="#jquest-popup-<?php echo esc_attr( $quest_id ); ?>">
			<?php if ( '' !== $label || '' !== $label_mobile ) : ?>
				<span class="label">
					<?php if ( '' !== $label ) : ?>
					<span class="desktop-only"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $label_mobile ) : ?>
					<span class="mobile-only"><?php echo esc_html( $label_mobile ); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<?php if ( '' !== $icon ) : ?>
			<span class="icon-container"><?php echo $icon; ?></span>
			<?php endif; ?>
		</a>
	</div>
	<?php
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_action( 'wp_footer', __NAMESPACE__ . '\maybe_insert_popup_trigger' );
