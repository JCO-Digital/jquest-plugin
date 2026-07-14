<?php
/**
 * Handles enqueuing of JQUEST scripts, when the block is present on a page.
 *
 * @package jQuestPlugin\Scripts
 */

namespace jQuestPlugin\Scripts;

/**
 * URL of the jQuest loader. The loader decides which build to fetch based on
 * the window.__JQUEST_VERSION global set below.
 */
const LOADER_URL = 'https://files.jquest.fi/jquest/jquest-loader.js';

/**
 * Version channel used when none is selected or an unknown one is requested.
 */
const DEFAULT_VERSION = 'stable';

/**
 * Version channels the loader understands.
 */
const VERSIONS = array( 'stable', 'latest', 'v2' );

/**
 * Option holding the popup version channel. Global (shared across languages),
 * since the version is a technical concern rather than per-language content.
 */
const POPUP_VERSION_OPTION = 'jquest_popup_version';

/**
 * Flag option marking that the one-time popup version migration has run.
 */
const VERSION_MIGRATION_FLAG = 'jquest_popup_version_migrated';

/**
 * Normalises a version channel to one the loader understands.
 *
 * @param mixed $version The requested version.
 *
 * @return string A value from VERSIONS, or DEFAULT_VERSION when unrecognised.
 */
function sanitize_version( $version ): string {
	return in_array( $version, VERSIONS, true ) ? (string) $version : DEFAULT_VERSION;
}

/**
 * Loads the jQuest loader once per page and tells it which version to fetch
 * via the window.__JQUEST_VERSION global.
 *
 * @param string $version The version channel to load (see VERSIONS).
 *
 * @return void
 */
function insert_jquest_script( string $version = DEFAULT_VERSION ): void {
	static $inserted = false;
	if ( $inserted ) {
		return;
	}
	$inserted = true;

	// Fall back to the default channel for anything unrecognised.
	$version = sanitize_version( $version );

	// The loader is a module, which always executes deferred, so set the
	// version global from a classic inline script first — it runs during
	// parsing, well before the loader module executes.
	echo "<script>window.__JQUEST_VERSION = '" . esc_js( $version ) . "';</script>\n";

	// Register and enqueue the loader using WordPress's native Module API.
	wp_register_script_module( 'jquest-loader', LOADER_URL, array(), \JQUEST_PLUGIN_VERSION );
	wp_enqueue_script_module( 'jquest-loader' );
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
		if ( (bool) $version ) {
			break;
		}
	}
	if ( $has_jquest_blocks ) {
		insert_jquest_script( $version ? $version : DEFAULT_VERSION );
	}
}

/**
 * Recursively checks if a block or its inner blocks contain the jquest-inserter.
 *
 * @param array $block The block object to check.
 *
 * @return array
 */
function contains_jquest_insterter( $block ): array {
	$return_value = array(
		'has_jquest_blocks' => false,
		'version'           => false,
	);

	if ( 'jquest-inserter/jquest-inserter' === $block['blockName'] ) {
		$return_value['has_jquest_blocks'] = true;
		if ( false === $return_value['version'] ) {
			// Read the attribute directly. WordPress omits attributes equal to
			// their default from the block comment, so fall back to the block's
			// registered default (kept in sync with block.json automatically).
			$block_type              = \WP_Block_Type_Registry::get_instance()
				->get_registered( 'jquest-inserter/jquest-inserter' );
			$default_version         = $block_type->attributes['version']['default'] ?? 'stable';
			$return_value['version'] = $block['attrs']['version'] ?? $default_version;
		}
	}

	if ( array_key_exists( 'innerBlocks', $block ) ) {
		foreach ( $block['innerBlocks'] as $inner_block ) {
			$inner_values = contains_jquest_insterter( $inner_block );

			$return_value['has_jquest_blocks'] =
				$return_value['has_jquest_blocks'] || $inner_values['has_jquest_blocks'];
			if ( false === $return_value['version'] ) {
				$return_value['version'] = $inner_values['version'];
			}
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

	$version = get_option( POPUP_VERSION_OPTION, '' );
	if ( '' === $version ) {
		// Back-compat for the window before migrate_popup_version() runs: fall
		// back to this language's legacy boolean "use latest script" option.
		$version = get_option( $prefix . 'latest_script', 0 ) ? 'latest' : DEFAULT_VERSION;
	}

	insert_jquest_script( $version );
}

add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_popup_script' );

/**
 * One-time migration from the legacy per-language boolean `latest_script`
 * options to the single global version option. It consolidates every
 * language's value (any "latest" wins), then removes the legacy options and
 * sets a flag so it never runs again.
 *
 * @return void
 */
function migrate_popup_version(): void {
	if ( get_option( VERSION_MIGRATION_FLAG ) ) {
		return;
	}

	global $wpdb;
	// Find every language's legacy option, e.g. jquest_popup_en_latest_script.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$legacy_options = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( 'jquest_popup_' ) . '%' . $wpdb->esc_like( '_latest_script' )
		)
	);

	// Only seed the global version if it has not been set explicitly already.
	if ( ! empty( $legacy_options ) && '' === (string) get_option( POPUP_VERSION_OPTION, '' ) ) {
		$use_latest = false;
		foreach ( $legacy_options as $legacy_option ) {
			if ( get_option( $legacy_option, 0 ) ) {
				$use_latest = true;
				break;
			}
		}
		update_option( POPUP_VERSION_OPTION, $use_latest ? 'latest' : DEFAULT_VERSION );
	}

	foreach ( $legacy_options as $legacy_option ) {
		delete_option( $legacy_option );
	}

	update_option( VERSION_MIGRATION_FLAG, 1 );
}

add_action( 'admin_init', __NAMESPACE__ . '\migrate_popup_version' );

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

	// Prepare every value as a finished, escaped string so the markup below
	// stays a plain template with no inline PHP.
	$org_id           = esc_attr( get_option( 'jquest_org_id', '' ) );
	$quest_id         = get_option( $prefix . 'quest_id', '' );
	$quest_version    = \jQuestPlugin\get_jquest_version( $quest_id );
	$quest_id         = esc_attr( $quest_id );
	$auto             = get_option( $prefix . 'auto', 0 ) ? 'true' : 'false';
	$limit            = (int) get_option( $prefix . 'limit', 0 );
	$attach           = esc_attr( get_option( $prefix . 'attach', 'body' ) );
	$disable_dismiss  = get_option( $prefix . 'disable_dismiss', 1 ) ? 'true' : 'false';
	$disable_noscroll = get_option( $prefix . 'disable_noscroll', 1 ) ? 'true' : 'false';
	$locale           = 'default' === $lang ? '' : esc_attr( $lang );
	$version_attr     = 'v2' === $quest_version ? "\n\t\tdata-jq-version=\"v2\"" : '';

	// phpcs:disable WordPress.Security.EscapeOutput -- values are escaped/int-cast above.
	echo <<<HTML
	<div
		class="jquest-app"
		data-new-styles="true"
		data-locale="{$locale}"
		data-org-id="{$org_id}"
		data-game-id="{$quest_id}"{$version_attr}
		data-popup="true"
		data-popup-auto="{$auto}"
		data-popup-limit="{$limit}"
		data-popup-attach="{$attach}"
		data-popup-disable-dismiss="{$disable_dismiss}"
		data-popup-disable-noscroll="{$disable_noscroll}"
	></div>
	HTML;
	// phpcs:enable WordPress.Security.EscapeOutput
}

add_action( 'wp_footer', __NAMESPACE__ . '\maybe_insert_popup_div' );

/**
 * Outputs the popup trigger button styles from global settings, once per page.
 *
 * @return void
 */
function output_popup_trigger_styles(): void {
	static $output = false;
	if ( $output ) {
		return;
	}
	$output = true;

	$text_color                   = get_option( 'jquest_popup_trigger_text_color', '#1a2e40' );
	$bg_color                     = get_option( 'jquest_popup_trigger_bg_color', '#ffffff' );
	$text_hover_color             = get_option( 'jquest_popup_trigger_text_hover_color', '#1a2e40' );
	$bg_hover_color               = get_option( 'jquest_popup_trigger_bg_hover_color', '#f0f0f0' );
	$icon_bg_color                = get_option( 'jquest_popup_trigger_icon_bg_color', '#ffffff' );
	$icon_bg_hover_color          = get_option( 'jquest_popup_trigger_icon_bg_hover_color', '' );
	$icon_color                   = get_option( 'jquest_popup_trigger_icon_color', '' );
	$icon_hover_color             = get_option( 'jquest_popup_trigger_icon_hover_color', '' );
	$side                         = get_option( 'jquest_popup_trigger_side', 'right' );
	$offset_x                     = (int) get_option( 'jquest_popup_trigger_offset_x', 16 );
	$offset_y                     = (int) get_option( 'jquest_popup_trigger_offset_y', 16 );
	$border_radius                = (int) get_option( 'jquest_popup_trigger_border_radius', 25 );
	$padding_top                  = (int) get_option( 'jquest_popup_trigger_padding_top', 11 );
	$padding_right                = (int) get_option( 'jquest_popup_trigger_padding_right', 23 );
	$padding_bottom               = (int) get_option( 'jquest_popup_trigger_padding_bottom', 11 );
	$padding_left                 = (int) get_option( 'jquest_popup_trigger_padding_left', 23 );
	$icon_container_size          = (int) get_option( 'jquest_popup_trigger_icon_container_size', 29 );
	$icon_container_border_radius = (int) get_option(
		'jquest_popup_trigger_icon_container_border_radius',
		50,
	);
	$items_gap                    = (int) get_option( 'jquest_popup_trigger_items_gap', 8 );
	$icon_size                    = (int) get_option( 'jquest_popup_trigger_icon_size', 20 );
	$font_size                    = (int) get_option( 'jquest_popup_trigger_font_size', 18 );
	$font_weight                  = get_option( 'jquest_popup_trigger_font_weight', '400' );
	$underline_width              = (int) get_option( 'jquest_popup_trigger_underline_width', 1 );
	$underline_color              = get_option( 'jquest_popup_trigger_underline_color', '' );
	$underline_hover_color        = get_option( 'jquest_popup_trigger_underline_hover_color', '' );
	$minimized                    = (bool) get_option( 'jquest_popup_trigger_minimized', 0 );
	$watch_selector               = get_option( 'jquest_popup_trigger_watch_selector', 'footer' );
	$watch_threshold              = (int) get_option( 'jquest_popup_trigger_watch_threshold', 10 );
	// Build every dynamic value as a finished string first. Keeping the CSS
	// template free of inline PHP makes it readable and stops the formatter
	// from splitting a value across lines, which previously produced broken
	// CSS such as "23 px" in the padding shorthand.
	$side                  = esc_attr( $side );
	$text_color            = esc_attr( $text_color );
	$bg_color              = esc_attr( $bg_color );
	$text_hover_color      = esc_attr( $text_hover_color );
	$bg_hover_color        = esc_attr( $bg_hover_color );
	$icon_bg_color         = esc_attr( $icon_bg_color );
	$font_weight           = esc_attr( $font_weight );
	$padding               = sprintf( '%dpx %dpx %dpx %dpx', $padding_top, $padding_right, $padding_bottom, $padding_left );
	$underline_color       = '' !== $underline_color ? esc_attr( $underline_color ) : 'transparent';
	$underline_hover_color = '' !== $underline_hover_color ? esc_attr( $underline_hover_color ) : 'transparent';

	// Optional rules, emitted only when their source value is set.
	$font_size_rule  = $font_size > 0 ? "font-size: {$font_size}px;" : '';
	$icon_color_rule = '' !== $icon_color ? 'color: ' . esc_attr( $icon_color ) . ';' : '';

	$icon_bg_hover_rule = '' !== $icon_bg_hover_color
		? '.jquest-popup-toggle a:hover .icon-container { background-color: ' . esc_attr( $icon_bg_hover_color ) . '; }'
		: '';
	$icon_hover_rule    = '' !== $icon_hover_color
		? '.jquest-popup-toggle a:hover .icon-container svg { color: ' . esc_attr( $icon_hover_color ) . '; }'
		: '';

	$minimized_rule = $minimized ? sprintf(
		'.jquest-popup-toggle.is-minimized a { border-radius: 50%%; padding: %dpx; } .jquest-popup-toggle.is-minimized .label { display: none; }',
		$padding_top
	) : '';

	// phpcs:disable WordPress.Security.EscapeOutput -- values are escaped/int-cast above.
	echo <<<CSS
	<style>
		.jquest-app[data-popup='true'] { display: none; }

		.jquest-popup-toggle {
			position: fixed;
			bottom: {$offset_y}px;
			{$side}: {$offset_x}px;
			z-index: 99;
			max-width: 100%;
			transition: all .3s;
		}
		.jquest-popup-toggle * { transition: all .3s; }

		.jquest-popup-toggle a {
			display: flex;
			align-items: center;
			position: relative;
			height: 100%;
			gap: {$items_gap}px;
			padding: {$padding};
			background-color: {$bg_color};
			color: {$text_color};
			border-radius: {$border_radius}px;
			border-bottom: {$underline_width}px solid {$underline_color};
			text-decoration: none;
			cursor: pointer;
			font-weight: {$font_weight};
			{$font_size_rule}
		}
		.jquest-popup-toggle a .label {
			margin-top: auto;
			margin-bottom: auto;
			line-height: 18px;
		}

		.jquest-popup-toggle a:hover {
			background-color: {$bg_hover_color};
			color: {$text_hover_color};
			border-bottom-color: {$underline_hover_color};
		}

		.jquest-popup-toggle a .icon-container {
			display: flex;
			justify-content: center;
			align-items: center;
			width: {$icon_container_size}px;
			height: {$icon_container_size}px;
			border-radius: {$icon_container_border_radius}px;
			background-color: {$icon_bg_color};
		}
		.jquest-popup-toggle a .icon-container svg {
			width: {$icon_size}px;
			height: {$icon_size}px;
			{$icon_color_rule}
		}

		{$icon_bg_hover_rule}
		{$icon_hover_rule}

		@media (width >= 1024px) {
			.jquest-popup-toggle .mobile-only { display: none !important; }
		}
		@media (width < 1024px) {
			.jquest-popup-toggle .desktop-only { display: none !important; }
		}

		{$minimized_rule}
	</style>
	CSS;

	if ( $minimized ) :
		?>
	<script>
	(function () {
		var selector  = <?php echo wp_json_encode( $watch_selector ); ?>;
		var threshold = <?php echo esc_js( $watch_threshold / 100 ); ?>;
		function init() {
			var toggles = document.querySelectorAll('.jquest-popup-toggle');
			if (!toggles.length) return;
			var target = document.querySelector(selector);
			if (!target) return;
			new IntersectionObserver(function (entries) {
				var visible = entries[0].isIntersecting;
				toggles.forEach(function (el) { el.classList.toggle('is-minimized', visible); });
			}, { threshold: threshold }).observe(target);
		}
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init);
		} else {
			init();
		}
	})();
	</script>
	<?php endif; ?>
	<?php
	// phpcs:enable WordPress.Security.EscapeOutput
}

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

	$label        = get_option( $prefix . 'desktop_label', '' );
	$label_mobile = get_option( $prefix . 'mobile_label', '' );
	if ( '' === $label ) {
		$label = get_option( $prefix . 'mobile_label', '' );
	}
	$quest_id = get_option( $prefix . 'quest_id', '' );

	$icon_mode = get_option( 'jquest_popup_trigger_icon_mode', 'default' );

	$svg_kses = array(
		'svg'    => array(
			'xmlns'   => true,
			'viewBox' => true,
			'width'   => true,
			'height'  => true,
			'fill'    => true,
		),
		'path'   => array(
			'd'            => true,
			'fill'         => true,
			'fill-rule'    => true,
			'clip-rule'    => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'g'      => array( 'fill' => true ),
		'circle' => array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'rect'   => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'fill'   => true,
			'rx'     => true,
		),
	);

	if ( 'default' === $icon_mode ) {
		$icon =
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2.046 21.8a.5.5 0 0 0 .62.635l4.87-1.515A9.96 9.96 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2Z"/></svg>';
	} elseif ( 'custom' === $icon_mode ) {
		$icon = wp_kses( get_option( 'jquest_popup_trigger_icon_custom', '' ), $svg_kses );
	} else {
		$icon = '';
	}
	output_popup_trigger_styles();

	// Build the markup fragments up front so the template stays a plain
	// heredoc with no inline PHP or conditionals.
	$toggle_class = '' !== $icon ? 'has-icon' : 'no-icon';
	$quest_id     = esc_attr( $quest_id );

	$label_html = '';
	if ( '' !== $label || '' !== $label_mobile ) {
		$label_parts = '';
		if ( '' !== $label ) {
			$label_parts .= '<span class="desktop-only">' . esc_html( $label ) . '</span>';
		}
		if ( '' !== $label_mobile ) {
			$label_parts .= '<span class="mobile-only">' . esc_html( $label_mobile ) . '</span>';
		}
		$label_html = '<span class="label">' . $label_parts . '</span>';
	}

	// $icon is either a trusted literal or already run through wp_kses().
	$icon_html = '' !== $icon ? '<span class="icon-container">' . $icon . '</span>' : '';

	// phpcs:disable WordPress.Security.EscapeOutput -- values are escaped / wp_kses'd above.
	echo <<<HTML
	<div class="jquest-popup-toggle {$toggle_class}" data-jq-load="hover">
		<a href="#jquest-popup-{$quest_id}">
			{$label_html}
			{$icon_html}
		</a>
	</div>
	HTML;
	// phpcs:enable WordPress.Security.EscapeOutput
}

add_action( 'wp_footer', __NAMESPACE__ . '\maybe_insert_popup_trigger' );

/**
 * Outputs trigger button styles when any jquest-inserter block on the page has the trigger button enabled.
 *
 * @return void
 */
function maybe_insert_block_trigger_styles(): void {
	if ( ! has_blocks() || ! has_block( 'jquest-inserter/jquest-inserter' ) ) {
		return;
	}

	$post = get_post();
	if ( ! $post ) {
		return;
	}

	$blocks = parse_blocks( $post->post_content );
	if ( block_has_trigger_button( $blocks ) ) {
		output_popup_trigger_styles();
	}
}

	/**
	 * Recursively checks whether any jquest-inserter block has the trigger button enabled.
	 *
	 * @param array $blocks The blocks to check.
	 *
	 * @return bool
	 */
function block_has_trigger_button( array $blocks ): bool {
	foreach ( $blocks as $block ) {
		if (
			'jquest-inserter/jquest-inserter' === $block['blockName'] &&
			! empty( $block['attrs']['popup'] ) &&
			empty( $block['attrs']['popupAuto'] ) &&
			! empty( $block['attrs']['popupTriggerButton'] )
		) {
			return true;
		}
		if ( ! empty( $block['innerBlocks'] ) && block_has_trigger_button( $block['innerBlocks'] ) ) {
			return true;
		}
	}
	return false;
}

add_action( 'wp_footer', __NAMESPACE__ . '\maybe_insert_block_trigger_styles' );

	/**
	 * Injects the trigger icon (from global settings) into jquest-inserter block output.
	 *
	 * @param string $content The block content.
	 * @param array  $block   The block object.
	 *
	 * @return string
	 */
function inject_block_trigger_icon( string $content, array $block ): string {
	if ( 'jquest-inserter/jquest-inserter' !== $block['blockName'] ) {
		return $content;
	}

	if (
	empty( $block['attrs']['popup'] ) ||
	! empty( $block['attrs']['popupAuto'] ) ||
	empty( $block['attrs']['popupTriggerButton'] )
	) {
		return $content;
	}

	$icon_mode = get_option( 'jquest_popup_trigger_icon_mode', 'default' );

	if ( 'default' === $icon_mode ) {
		$icon =
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2.046 21.8a.5.5 0 0 0 .62.635l4.87-1.515A9.96 9.96 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2Z"/></svg>';
	} elseif ( 'custom' === $icon_mode ) {
		$svg_kses = array(
			'svg'    => array(
				'xmlns'   => true,
				'viewBox' => true,
				'width'   => true,
				'height'  => true,
				'fill'    => true,
			),
			'path'   => array(
				'd'            => true,
				'fill'         => true,
				'fill-rule'    => true,
				'clip-rule'    => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'g'      => array( 'fill' => true ),
			'circle' => array(
				'cx'           => true,
				'cy'           => true,
				'r'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'rect'   => array(
				'x'      => true,
				'y'      => true,
				'width'  => true,
				'height' => true,
				'fill'   => true,
				'rx'     => true,
			),
		);
		$icon     = wp_kses( get_option( 'jquest_popup_trigger_icon_custom', '' ), $svg_kses );
	} else {
		$icon = '';
	}

	$class     = '' !== $icon ? 'has-icon' : 'no-icon';
	$icon_html = '' !== $icon ? '<span class="icon-container">' . $icon . '</span>' : '';

	return preg_replace_callback(
		'/<div class="jquest-popup-toggle">(.*?)<\/div>/s',
		function ( $matches ) use ( $class, $icon_html ) {
			$inner = preg_replace( '/<\/a>/', $icon_html . '</a>', $matches[1], 1 );
			return '<div class="jquest-popup-toggle ' . $class . '">' . $inner . '</div>';
		},
		$content,
	);
}

add_filter( 'render_block', __NAMESPACE__ . '\inject_block_trigger_icon', 10, 2 );
