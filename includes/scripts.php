<?php
/**
 * Handles enqueuing of SuperQuest scripts, when the block is present on a page.
 *
 * @package SuperQuestPlugin\Scripts
 */

namespace SuperQuestPlugin\Scripts;

/**
 * URL of the SuperQuest loader. It picks its build from the
 * window.__JQUEST_VERSION global set below.
 */
const LOADER_URL = 'https://files.jquest.fi/jquest/jquest-loader.js';

/**
 * The one build every page loads. Blocks and popups all run on it, so nothing
 * on the page gets a say in which script is fetched.
 */
const SCRIPT_VERSION = 'v2';

/**
 * Base URL of the bundle directory, holding manifest.json and the hashed chunks
 * it lists.
 */
const BUNDLE_BASE_URL = 'https://files.jquest.fi/jquest/' . SCRIPT_VERSION;

/**
 * Option loading the loader on every front-end page, whether or not a
 * SuperQuest block or popup is present. Global, since it is a technical concern.
 */
const ALWAYS_LOAD_OPTION = 'superquest_always_load_loader';

/**
 * Option holding the pages no popup quest is inserted on. Global, since post
 * IDs never collide between languages, and a page that should stay clear of
 * popups should stay clear of them in every language.
 */
const POPUP_V2_EXCLUDE_OPTION = 'superquest_popup_v2_exclude_ids';

/**
 * Flag option marking that the one-time migration from a single popup quest
 * per language to a list of them has run.
 */
const POPUP_V2_MIGRATION_FLAG = 'superquest_popup_v2_quests_migrated';

/**
 * Returns the current language slug, or 'default' when Polylang is inactive or
 * has no language for this request.
 *
 * @return string
 */
function current_language(): string {
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';

	return $lang ? (string) $lang : 'default';
}

/**
 * Option prefix for the popup settings of a language.
 *
 * @param string|null $lang Language slug. Defaults to the current language.
 *
 * @return string
 */
function popup_v2_prefix( ?string $lang = null ): string {
	return 'superquest_popup_v2_' . ( $lang ?? current_language() ) . '_';
}

/**
 * Parses a list of post IDs typed into a settings field: IDs separated by
 * commas or whitespace, in any mix.
 *
 * @param mixed $value The raw field value.
 *
 * @return int[] Unique, positive IDs.
 */
function parse_id_list( $value ): array {
	if ( is_scalar( $value ) ) {
		$value = (string) $value;
	} elseif ( ! is_array( $value ) ) {
		$value = '';
	}

	return array_values( array_filter( wp_parse_id_list( $value ) ) );
}

/**
 * Sanitises a list of post IDs down to a comma-separated string, so the stored
 * option reads back the same way it was typed.
 *
 * @param mixed $value The raw field value.
 *
 * @return string
 */
function sanitize_id_list( $value ): string {
	return implode( ', ', parse_id_list( $value ) );
}

/**
 * Option holding a language's list of popup quests.
 *
 * @param string|null $lang Language slug. Defaults to the current language.
 *
 * @return string
 */
function popup_v2_quests_option( ?string $lang = null ): string {
	return popup_v2_prefix( $lang ) . 'quests';
}

/**
 * Normalises one stored popup quest entry into a predictable shape, so that
 * neither the settings page nor the front end has to guess at what the option
 * holds.
 *
 * @param mixed $entry The stored entry.
 *
 * @return array{enabled: bool, quest_id: string}
 */
function normalize_popup_v2_quest( $entry ): array {
	$entry = is_array( $entry ) ? $entry : array();

	return array(
		'enabled'  => ! empty( $entry['enabled'] ),
		'quest_id' => isset( $entry['quest_id'] ) && is_scalar( $entry['quest_id'] )
			? sanitize_text_field( (string) $entry['quest_id'] )
			: '',
	);
}

/**
 * The popup quests configured for a language, in the order they were added.
 *
 * @param string|null $lang Language slug. Defaults to the current language.
 *
 * @return array<int, array{enabled: bool, quest_id: string}>
 */
function popup_v2_quests( ?string $lang = null ): array {
	$stored = get_option( popup_v2_quests_option( $lang ), null );

	if ( ! is_array( $stored ) ) {
		// Back-compat for the window before migrate_popup_v2_quests() runs on
		// the next admin request: read the one legacy entry as a list of one.
		$prefix = popup_v2_prefix( $lang );
		$stored = array(
			array(
				'enabled'  => get_option( $prefix . 'enabled', 0 ),
				'quest_id' => get_option( $prefix . 'quest_id', '' ),
			),
		);
	}

	// An entry without a quest cannot render anything, so it never reaches a
	// caller — that also drops the empty legacy entry synthesised above.
	return array_values(
		array_filter(
			array_map( __NAMESPACE__ . '\normalize_popup_v2_quest', $stored ),
			function ( array $quest ): bool {
				return '' !== $quest['quest_id'];
			}
		)
	);
}

/**
 * Sanitises the popup quest list posted from the settings page.
 *
 * The form carries no JavaScript, so a row is deleted either by ticking its
 * remove box or by emptying its quest select; both are dropped here, and what
 * is left is reindexed so the stored list stays a clean sequential array.
 *
 * @param mixed $value The raw field value.
 *
 * @return array
 */
function sanitize_popup_v2_quests( $value ): array {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$quests = array();
	foreach ( $value as $entry ) {
		if ( ! is_array( $entry ) || ! empty( $entry['remove'] ) ) {
			continue;
		}

		$quest = normalize_popup_v2_quest( $entry );
		if ( '' === $quest['quest_id'] ) {
			continue;
		}

		$quests[] = array(
			'enabled'  => $quest['enabled'] ? 1 : 0,
			'quest_id' => $quest['quest_id'],
		);
	}

	return $quests;
}

/**
 * The pages no popup quest is inserted on, across every language.
 *
 * @return int[]
 */
function popup_v2_excluded_ids(): array {
	$stored = get_option( POPUP_V2_EXCLUDE_OPTION, null );

	if ( null === $stored ) {
		// Back-compat for the window before migrate_popup_v2_quests() runs on
		// the next admin request: read this language's legacy list, which is
		// what the page was excluded by before the list became global.
		$stored = get_option( popup_v2_prefix() . 'exclude_ids', '' );
	}

	return parse_id_list( $stored );
}

/**
 * Whether the post being rendered is on the popup exclusion list.
 *
 * Excluding a page takes its quests out of both the loader decision and the
 * footer markup, so nothing is inserted above that page's footer.
 *
 * @return bool
 */
function popup_v2_excluded(): bool {
	$excluded = popup_v2_excluded_ids();
	if ( empty( $excluded ) ) {
		return false;
	}

	$current = (int) get_queried_object_id();

	return $current > 0 && in_array( $current, $excluded, true );
}

/**
 * The quests this language shows above the footer on the post being rendered.
 *
 * @return string[] Quest IDs, in configured order.
 */
function popup_v2_active_quest_ids(): array {
	if ( popup_v2_excluded() ) {
		return array();
	}

	$active = array();
	foreach ( popup_v2_quests() as $quest ) {
		if ( $quest['enabled'] ) {
			$active[] = $quest['quest_id'];
		}
	}

	return $active;
}

/**
 * Attributes telling consent managers not to gate the loader. The loader sets
 * no cookies and stores nothing, so blocking it until consent only breaks
 * quests. Covers Cookiebot, OneTrust and CookieYes.
 *
 * @return array<string, string|bool> Attribute map for wp_print_script_tag() et al.
 */
function consent_attributes(): array {
	return array(
		'data-cookieconsent' => 'ignore',
		'data-ot-ignore'     => true,
		'data-cookieyes'     => 'ignore',
	);
}

/**
 * How long a fetched manifest is reused before it is fetched again.
 *
 * The CDN caches the manifest for 60 seconds, so this can lag a deploy by a few
 * minutes. In that window the preload hints point at chunks that were just
 * replaced. That wastes one request per chunk and breaks nothing, because the
 * loader reads the live manifest itself.
 */
const MANIFEST_CACHE_TTL = 5 * MINUTE_IN_SECONDS;

/**
 * Fetches and caches the bundle's manifest.json.
 *
 * Failures are cached too. An unreachable CDN then costs one attempt per TTL
 * instead of one per page view.
 *
 * @return array<string, mixed> Decoded manifest, or an empty array when unavailable.
 */
function bundle_manifest(): array {
	$key = 'superquest_manifest_' . SCRIPT_VERSION;

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$manifest = array();
	$response = wp_remote_get(
		BUNDLE_BASE_URL . '/manifest.json',
		array( 'timeout' => 2 )
	);

	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $decoded ) ) {
			$manifest = $decoded;
		}
	}

	set_transient( $key, $manifest, MANIFEST_CACHE_TTL );

	return $manifest;
}

/**
 * The chunks worth preloading. That is the entry chunk plus every chunk it
 * imports statically, so the browser fetches the whole bundle in one round trip
 * instead of entry first and vendors after.
 *
 * A manifest may name them in a "preload" list. Without one, this guesses:
 * every vendor chunk is treated as a static import of the entry, except Sentry,
 * which the entry imports lazily. This matches the current bundle. Chunks the
 * entry loads on demand, such as rive and masterQuest, are skipped. They are
 * large and only some quests use them.
 *
 * @return string[] Chunk URLs, entry first.
 */
function bundle_preload_urls(): array {
	$manifest = bundle_manifest();
	$app      = $manifest['app'] ?? '';
	if ( ! is_string( $app ) || '' === $app ) {
		return array();
	}

	$files = $manifest['preload'] ?? null;
	if ( ! is_array( $files ) ) {
		$files = array_filter(
			(array) ( $manifest['files'] ?? array() ),
			function ( $file ): bool {
				return is_string( $file )
					&& str_contains( $file, '-vendor-' )
					&& ! str_contains( $file, '-vendor-sentry-' );
			}
		);
	}

	$urls = array( BUNDLE_BASE_URL . '/' . $app );
	foreach ( $files as $file ) {
		if ( is_string( $file ) && '' !== $file && $app !== $file ) {
			$urls[] = BUNDLE_BASE_URL . '/' . $file;
		}
	}

	return array_values( array_unique( $urls ) );
}

/**
 * Prints the resource hints that let the browser fetch the bundle while the
 * host page is still parsing. Without them the loader asks for the manifest,
 * then the entry chunk, then the vendor chunks, one round trip after another.
 *
 * The loader fetches all of these anonymously with CORS: fetch() for the
 * manifest, module scripts for the chunks. Every hint carries crossorigin so the
 * preloaded response lands in the same cache and connection pool.
 *
 * @return void
 */
function print_bundle_preloads(): void {
	printf(
		'<link rel="preload" as="fetch" href="%s" crossorigin>' . "\n",
		esc_url( BUNDLE_BASE_URL . '/manifest.json' )
	);

	foreach ( bundle_preload_urls() as $url ) {
		printf( '<link rel="modulepreload" href="%s" crossorigin>' . "\n", esc_url( $url ) );
	}
}

/**
 * Loads the SuperQuest loader once per page and names the build it should fetch
 * via the window.__JQUEST_VERSION global.
 *
 * The loader is a classic IIFE, so it goes out as a classic async script. It
 * runs as soon as its bytes arrive and never blocks parsing. This plugin used to
 * ship it as a module, which the browser defers until the host page has finished
 * parsing. On heavy pages that was many seconds after the download. Running
 * early is safe because the loader waits for DOMContentLoaded before it looks
 * for widgets.
 *
 * @param bool $preload_bundle Whether the app bundle will be needed without
 *                             user interaction, and so is worth fetching ahead
 *                             of time.
 *
 * @return void
 */
function insert_superquest_script( bool $preload_bundle = false ): void {
	static $inserted = false;
	if ( $inserted ) {
		return;
	}
	$inserted = true;

	// The consent attributes are printed directly below. The filter is kept so
	// consent managers hooked to wp_script_attributes still find the loader tag
	// under its id.
	add_filter(
		'wp_script_attributes',
		function ( array $attributes ): array {
			if ( 'superquest-loader-js' === ( $attributes['id'] ?? '' ) ) {
				$attributes = array_merge( $attributes, consent_attributes() );
			}

			return $attributes;
		}
	);

	// Every request to the CDN after the loader itself is anonymous CORS. Open
	// that connection now.
	echo '<link rel="preconnect" href="https://files.jquest.fi" crossorigin>' . "\n";

	// The version global has to be set before the loader runs. The inline tag
	// comes first and executes during parsing, and the async loader cannot run
	// before the parser has reached its own tag, so the order holds.
	wp_print_inline_script_tag(
		"window.__JQUEST_VERSION = '" . esc_js( SCRIPT_VERSION ) . "';",
		consent_attributes()
	);

	wp_print_script_tag(
		array_merge(
			array(
				'id'          => 'superquest-loader-js',
				'src'         => LOADER_URL,
				'async'       => true,
				'crossorigin' => 'anonymous',
			),
			consent_attributes()
		)
	);

	if ( $preload_bundle ) {
		print_bundle_preloads();
	}
}

/**
 * Whether the current post content holds at least one SuperQuest block.
 *
 * @return bool
 */
function has_superquest_block(): bool {
	return \SuperQuestPlugin\post_has_inserter_block();
}

/**
 * Whether this language has at least one popup quest to render above the footer
 * on the page being rendered.
 *
 * @return bool
 */
function popup_v2_enabled(): bool {
	return ! empty( popup_v2_active_quest_ids() );
}

/**
 * Loads the loader on the pages that need it.
 *
 * Only a single loader can run per page, so every source — blocks, popup quests
 * and the always-load setting — is resolved here in one place instead of racing
 * to be the first to call insert_superquest_script().
 *
 * @return void
 */
function maybe_insert_loader(): void {
	$has_block = has_superquest_block();
	$has_popup = popup_v2_enabled();

	if ( ! $has_block && ! $has_popup && ! get_option( ALWAYS_LOAD_OPTION, 0 ) ) {
		return;
	}

	// Preload the bundle only when the loader will fetch it without the visitor
	// doing anything. That is a block in the content, since the viewport gate
	// fires as soon as it scrolls near, or a popup quest. The always-load setting
	// alone may never need the bundle, and preloading a megabyte of vendor code
	// for nothing would only slow the host page down.
	insert_superquest_script( $has_block || $has_popup );
}

// Priority 2 runs right after wp_enqueue_scripts at 1 and ahead of the theme's
// styles at 8 and head scripts at 9. The loader and the preload hints are then
// among the first tags in the head, with nothing from the host page queued
// ahead of them.
add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_loader', 2 );

/**
 * Outputs the popup quest divs at the top of the footer, i.e. just above
 * everything else hooked to wp_footer.
 *
 * @return void
 */
function maybe_insert_popup_v2_divs(): void {
	$quest_ids = popup_v2_active_quest_ids();
	if ( empty( $quest_ids ) ) {
		return;
	}

	// Prepare every value as a finished, escaped string so the markup below
	// stays a plain template with no inline PHP.
	$lang               = current_language();
	$org_id             = esc_attr( get_option( 'superquest_org_id', '' ) );
	$locale             = 'default' === $lang ? '' : esc_attr( $lang );
	$superquest_version = esc_attr( SCRIPT_VERSION );

	foreach ( $quest_ids as $quest_id ) {
		$quest_id = esc_attr( $quest_id );

		// data-game-id is the attribute the external loader reads, so it keeps
		// that name even though the plugin calls these quests everywhere else.
		//
		// data-jq-load="eager" opts this widget out of the loader's viewport
		// gate. The loader only fetches the app bundle once a .jquest-app
		// approaches the viewport, but these divs sit at the very bottom of the
		// document and render floating popups, so they would only load once the
		// visitor scrolled all the way down — by which point the popup has
		// missed its chance to appear.
		// phpcs:disable WordPress.Security.EscapeOutput -- values are escaped above.
		echo <<<HTML
		<div
			class="jquest-app"
			data-new-styles="true"
			data-locale="{$locale}"
			data-org-id="{$org_id}"
			data-game-id="{$quest_id}"
			data-version="{$superquest_version}"
			data-jq-load="eager"
		></div>
		HTML;
		// phpcs:enable WordPress.Security.EscapeOutput

		// The heredoc has no trailing newline, so several quests would
		// otherwise land on one line in the page source.
		echo "\n";
	}
}

add_action( 'wp_footer', __NAMESPACE__ . '\maybe_insert_popup_v2_divs', 0 );

/**
 * One-time migration from the legacy single popup quest per language to the
 * per-language quest list. Each language's `enabled`/`quest_id`/`exclude_ids`
 * trio becomes a list of one, the legacy options are removed, and a flag is set
 * so it never runs again.
 *
 * @return void
 */
function migrate_popup_v2_quests(): void {
	if ( get_option( POPUP_V2_MIGRATION_FLAG ) ) {
		return;
	}

	global $wpdb;
	// Find every language's legacy option, e.g. superquest_popup_v2_en_quest_id.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$legacy_options = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( 'superquest_popup_v2_' ) . '%' . $wpdb->esc_like( '_quest_id' )
		)
	);

	$excluded = array();
	foreach ( $legacy_options as $legacy_option ) {
		$prefix   = substr( $legacy_option, 0, -strlen( 'quest_id' ) );
		$quest_id = (string) get_option( $legacy_option, '' );

		// Only seed the list when there is a quest to carry over and nothing has
		// been saved through the new settings form yet, so a list a user has
		// already edited is never overwritten.
		if ( '' !== $quest_id && ! is_array( get_option( $prefix . 'quests', null ) ) ) {
			update_option(
				$prefix . 'quests',
				array(
					array(
						'enabled'  => get_option( $prefix . 'enabled', 0 ) ? 1 : 0,
						'quest_id' => $quest_id,
					),
				)
			);
		}

		// The exclusion list is global now, so every language's list is folded
		// into one. A page excluded in any language stays excluded, which is the
		// safe direction: the alternative puts a popup back onto a page somebody
		// had deliberately kept clear of them.
		$excluded = array_merge( $excluded, parse_id_list( get_option( $prefix . 'exclude_ids', '' ) ) );

		delete_option( $legacy_option );
		delete_option( $prefix . 'enabled' );
		delete_option( $prefix . 'exclude_ids' );
	}

	if ( ! empty( $excluded ) && null === get_option( POPUP_V2_EXCLUDE_OPTION, null ) ) {
		update_option( POPUP_V2_EXCLUDE_OPTION, sanitize_id_list( $excluded ) );
	}

	update_option( POPUP_V2_MIGRATION_FLAG, 1 );
}

add_action( 'admin_init', __NAMESPACE__ . '\migrate_popup_v2_quests' );

/**
 * Outputs the trigger button styles a superquest-inserter block's popup needs, once
 * per page. The values are whatever the removed trigger settings page last
 * saved, falling back to the defaults below.
 *
 * @return void
 */
function output_popup_trigger_styles(): void {
	static $output = false;
	if ( $output ) {
		return;
	}
	$output = true;

	$text_color                   = get_option( 'superquest_popup_trigger_text_color', '#1a2e40' );
	$bg_color                     = get_option( 'superquest_popup_trigger_bg_color', '#ffffff' );
	$text_hover_color             = get_option( 'superquest_popup_trigger_text_hover_color', '#1a2e40' );
	$bg_hover_color               = get_option( 'superquest_popup_trigger_bg_hover_color', '#f0f0f0' );
	$icon_bg_color                = get_option( 'superquest_popup_trigger_icon_bg_color', '#ffffff' );
	$icon_bg_hover_color          = get_option( 'superquest_popup_trigger_icon_bg_hover_color', '' );
	$icon_color                   = get_option( 'superquest_popup_trigger_icon_color', '' );
	$icon_hover_color             = get_option( 'superquest_popup_trigger_icon_hover_color', '' );
	$side                         = get_option( 'superquest_popup_trigger_side', 'right' );
	$offset_x                     = (int) get_option( 'superquest_popup_trigger_offset_x', 16 );
	$offset_y                     = (int) get_option( 'superquest_popup_trigger_offset_y', 16 );
	$border_radius                = (int) get_option( 'superquest_popup_trigger_border_radius', 25 );
	$padding_top                  = (int) get_option( 'superquest_popup_trigger_padding_top', 11 );
	$padding_right                = (int) get_option( 'superquest_popup_trigger_padding_right', 23 );
	$padding_bottom               = (int) get_option( 'superquest_popup_trigger_padding_bottom', 11 );
	$padding_left                 = (int) get_option( 'superquest_popup_trigger_padding_left', 23 );
	$icon_container_size          = (int) get_option( 'superquest_popup_trigger_icon_container_size', 29 );
	$icon_container_border_radius = (int) get_option(
		'superquest_popup_trigger_icon_container_border_radius',
		50,
	);
	$items_gap                    = (int) get_option( 'superquest_popup_trigger_items_gap', 8 );
	$icon_size                    = (int) get_option( 'superquest_popup_trigger_icon_size', 20 );
	$font_size                    = (int) get_option( 'superquest_popup_trigger_font_size', 18 );
	$font_weight                  = get_option( 'superquest_popup_trigger_font_weight', '400' );
	$underline_width              = (int) get_option( 'superquest_popup_trigger_underline_width', 1 );
	$underline_color              = get_option( 'superquest_popup_trigger_underline_color', '' );
	$underline_hover_color        = get_option( 'superquest_popup_trigger_underline_hover_color', '' );
	$minimized                    = (bool) get_option( 'superquest_popup_trigger_minimized', 0 );
	$watch_selector               = get_option( 'superquest_popup_trigger_watch_selector', 'footer' );
	$watch_threshold              = (int) get_option( 'superquest_popup_trigger_watch_threshold', 10 );
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
 * Outputs trigger button styles when any superquest-inserter block on the page has the trigger button enabled.
 *
 * @return void
 */
function maybe_insert_block_trigger_styles(): void {
	if ( ! \SuperQuestPlugin\post_has_inserter_block() ) {
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
	 * Recursively checks whether any superquest-inserter block has the trigger button enabled.
	 *
	 * @param array $blocks The blocks to check.
	 *
	 * @return bool
	 */
function block_has_trigger_button( array $blocks ): bool {
	foreach ( $blocks as $block ) {
		if (
			\SuperQuestPlugin\is_inserter_block( $block['blockName'] ) &&
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
	 * Injects the trigger icon (from global settings) into superquest-inserter block output.
	 *
	 * @param string $content The block content.
	 * @param array  $block   The block object.
	 *
	 * @return string
	 */
function inject_block_trigger_icon( string $content, array $block ): string {
	if ( ! \SuperQuestPlugin\is_inserter_block( $block['blockName'] ) ) {
		return $content;
	}

	if (
	empty( $block['attrs']['popup'] ) ||
	! empty( $block['attrs']['popupAuto'] ) ||
	empty( $block['attrs']['popupTriggerButton'] )
	) {
		return $content;
	}

	$icon_mode = get_option( 'superquest_popup_trigger_icon_mode', 'default' );

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
		$icon     = wp_kses( get_option( 'superquest_popup_trigger_icon_custom', '' ), $svg_kses );
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
