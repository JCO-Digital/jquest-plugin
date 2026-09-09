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
 * Option loading the loader on every front-end page, whether or not a jQuest
 * block or popup is present. Global, since it is a technical concern.
 */
const ALWAYS_LOAD_OPTION = 'jquest_always_load_loader';

/**
 * Option holding the pages every Popup v2 quest is left off. Global, since a
 * page's script channel is a technical concern rather than per-language
 * content, and post IDs never collide between languages.
 */
const POPUP_V2_EXCLUDE_OPTION = 'jquest_popup_v2_exclude_ids';

/**
 * Flag option marking that the one-time migration from a single Popup v2 quest
 * per language to a list of them has run.
 */
const POPUP_V2_MIGRATION_FLAG = 'jquest_popup_v2_quests_migrated';

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
 * Option prefix for the Popup v2 settings of a language.
 *
 * @param string|null $lang Language slug. Defaults to the current language.
 *
 * @return string
 */
function popup_v2_prefix( ?string $lang = null ): string {
	return 'jquest_popup_v2_' . ( $lang ?? current_language() ) . '_';
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
 * Option holding a language's list of Popup v2 quests.
 *
 * @param string|null $lang Language slug. Defaults to the current language.
 *
 * @return string
 */
function popup_v2_quests_option( ?string $lang = null ): string {
	return popup_v2_prefix( $lang ) . 'quests';
}

/**
 * Normalises one stored Popup v2 entry into a predictable shape, so neither the
 * settings page nor the front end has to guess at what the option holds.
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
 * The Popup v2 quests configured for a language, in the order they were added.
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
 * Sanitises the Popup v2 quest list posted from the settings page.
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
 * The pages Popup v2 is left off, across every language.
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
 * Whether the post being rendered is on the Popup v2 exclusion list.
 *
 * Only one loader can run per page and a v2 quest anywhere on it pins the whole
 * page to the v2 bundle, so a page carrying a stable/latest block cannot also
 * carry a v2 popup. Excluding the page takes every quest out of both the loader
 * decision and the footer markup, leaving the block's own channel to win.
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
 * Base URL of a version channel's bundle directory, holding manifest.json and
 * the hashed chunks it lists. Mirrors the channel table inside the loader.
 *
 * @param string $version The version channel (see VERSIONS).
 *
 * @return string
 */
function channel_base_url( string $version ): string {
	return 'https://files.jquest.fi/jquest/' . sanitize_version( $version );
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
 * Fetches and caches a channel's manifest.json.
 *
 * Failures are cached too. An unreachable CDN then costs one attempt per TTL
 * instead of one per page view.
 *
 * @param string $version The version channel (see VERSIONS).
 *
 * @return array<string, mixed> Decoded manifest, or an empty array when unavailable.
 */
function channel_manifest( string $version ): array {
	$version = sanitize_version( $version );
	$key     = 'jquest_manifest_' . $version;

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$manifest = array();
	$response = wp_remote_get(
		channel_base_url( $version ) . '/manifest.json',
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
 * The chunks worth preloading for a channel. That is the entry chunk plus every
 * chunk it imports statically, so the browser fetches the whole bundle in one
 * round trip instead of entry first and vendors after.
 *
 * A manifest may name them in a "preload" list. Without one, this guesses:
 * every vendor chunk is treated as a static import of the entry, except Sentry,
 * which the entry imports lazily. This matches every current channel. Chunks
 * the entry loads on demand, such as rive and masterQuest, are skipped. They
 * are large and only some quests use them.
 *
 * @param string $version The version channel (see VERSIONS).
 *
 * @return string[] Chunk URLs, entry first.
 */
function channel_preload_urls( string $version ): array {
	$manifest = channel_manifest( $version );
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

	$base = channel_base_url( $version );
	$urls = array( $base . '/' . $app );
	foreach ( $files as $file ) {
		if ( is_string( $file ) && '' !== $file && $app !== $file ) {
			$urls[] = $base . '/' . $file;
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
 * @param string $version The version channel (see VERSIONS).
 *
 * @return void
 */
function print_bundle_preloads( string $version ): void {
	$base = channel_base_url( $version );

	printf(
		'<link rel="preload" as="fetch" href="%s" crossorigin>' . "\n",
		esc_url( $base . '/manifest.json' )
	);

	foreach ( channel_preload_urls( $version ) as $url ) {
		printf( '<link rel="modulepreload" href="%s" crossorigin>' . "\n", esc_url( $url ) );
	}
}

/**
 * Loads the jQuest loader once per page and tells it which version to fetch
 * via the window.__JQUEST_VERSION global.
 *
 * The loader is a classic IIFE, so it goes out as a classic async script. It
 * runs as soon as its bytes arrive and never blocks parsing. This plugin used to
 * ship it as a module, which the browser defers until the host page has finished
 * parsing. On heavy pages that was many seconds after the download. Running
 * early is safe because the loader waits for DOMContentLoaded before it looks
 * for widgets.
 *
 * @param string $version        The version channel to load (see VERSIONS).
 * @param bool   $preload_bundle Whether the app bundle will be needed without
 *                               user interaction, and so is worth fetching
 *                               ahead of time.
 *
 * @return void
 */
function insert_jquest_script( string $version = DEFAULT_VERSION, bool $preload_bundle = false ): void {
	static $inserted = false;
	if ( $inserted ) {
		return;
	}
	$inserted = true;

	// Fall back to the default channel for anything unrecognised.
	$version = sanitize_version( $version );

	// The consent attributes are printed directly below. The filter is kept so
	// consent managers hooked to wp_script_attributes still find the loader tag
	// under its id.
	add_filter(
		'wp_script_attributes',
		function ( array $attributes ): array {
			if ( 'jquest-loader-js' === ( $attributes['id'] ?? '' ) ) {
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
		"window.__JQUEST_VERSION = '" . esc_js( $version ) . "';",
		consent_attributes()
	);

	wp_print_script_tag(
		array_merge(
			array(
				'id'          => 'jquest-loader-js',
				'src'         => LOADER_URL,
				'async'       => true,
				'crossorigin' => 'anonymous',
			),
			consent_attributes()
		)
	);

	if ( $preload_bundle ) {
		print_bundle_preloads( $version );
	}
}

/**
 * An empty loader request, i.e. a source that needs no loader at all.
 *
 * @return array{present: bool, version: string|null, has_v2_quest: bool}
 */
function empty_loader_request(): array {
	return array(
		'present'      => false,
		'version'      => null,
		'has_v2_quest' => false,
	);
}

/**
 * Recursively collects what the jquest-inserter blocks in a block tree need:
 * whether any is present, the first requested channel, and whether any of them
 * renders a v2 quest.
 *
 * @param array $blocks The blocks to scan.
 *
 * @return array{present: bool, version: string|null, has_v2_quest: bool}
 */
function scan_jquest_blocks( array $blocks ): array {
	$request = empty_loader_request();

	foreach ( $blocks as $block ) {
		if ( 'jquest-inserter/jquest-inserter' === $block['blockName'] ) {
			$request['present'] = true;

			// Read the attribute directly. WordPress omits attributes equal to
			// their default from the block comment, so fall back to the block's
			// registered default (kept in sync with block.json automatically).
			$block_type      = \WP_Block_Type_Registry::get_instance()
				->get_registered( 'jquest-inserter/jquest-inserter' );
			$default_version = $block_type->attributes['version']['default'] ?? DEFAULT_VERSION;
			$version         = (string) ( $block['attrs']['version'] ?? $default_version );

			if ( null === $request['version'] && '' !== $version ) {
				$request['version'] = $version;
			}

			// The editor pins the channel to v2 when a v2 quest is picked, but
			// check the quest generation too so content saved before that
			// behaviour landed is still recognised.
			if ( 'v2' === ( $block['attrs']['questVersion'] ?? '' ) || 'v2' === $version ) {
				$request['has_v2_quest'] = true;
			}
		}

		if ( empty( $block['innerBlocks'] ) ) {
			continue;
		}

		$inner                   = scan_jquest_blocks( $block['innerBlocks'] );
		$request['present']      = $request['present'] || $inner['present'];
		$request['has_v2_quest'] = $request['has_v2_quest'] || $inner['has_v2_quest'];
		$request['version']    ??= $inner['version'];
	}

	return $request;
}

/**
 * What the jQuest blocks in the current post content need from the loader.
 *
 * @return array{present: bool, version: string|null, has_v2_quest: bool}
 */
function block_loader_request(): array {
	if ( ! has_blocks() || ! has_block( 'jquest-inserter/jquest-inserter' ) ) {
		return empty_loader_request();
	}

	$post = get_post();
	if ( ! $post ) {
		return empty_loader_request();
	}

	return scan_jquest_blocks( parse_blocks( $post->post_content ) );
}

/**
 * What the popup configured for the current language needs from the loader.
 *
 * @return array{present: bool, version: string|null, has_v2_quest: bool, eager?: bool}
 */
function popup_loader_request(): array {
	$prefix = 'jquest_popup_' . current_language() . '_';

	if ( ! get_option( $prefix . 'enabled', 0 ) ) {
		return empty_loader_request();
	}

	$version = (string) get_option( POPUP_VERSION_OPTION, '' );
	if ( '' === $version ) {
		// Back-compat for the window before migrate_popup_version() runs: fall
		// back to this language's legacy boolean "use latest script" option.
		$version = get_option( $prefix . 'latest_script', 0 ) ? 'latest' : DEFAULT_VERSION;
	}

	$quest_id = (string) get_option( $prefix . 'quest_id', '' );

	return array(
		'present'      => true,
		'version'      => $version,
		'has_v2_quest' => 'v2' === \jQuestPlugin\get_jquest_version( $quest_id ),
		// An auto popup declares itself eager, so its bundle is fetched without
		// any interaction. A popup the visitor opens only fetches on hover.
		'eager'        => (bool) get_option( $prefix . 'auto', 0 ),
	);
}

/**
 * Whether this language has at least one Popup v2 quest to render above the
 * footer on the page being rendered.
 *
 * @return bool
 */
function popup_v2_enabled(): bool {
	return ! empty( popup_v2_active_quest_ids() );
}

/**
 * Loads the one loader this page needs.
 *
 * Only a single loader can run per page, so every source — blocks, the popup,
 * Popup v2 and the always-load setting — is resolved here in one place instead
 * of racing to be the first to call insert_jquest_script(). A v2 quest anywhere
 * on the page pins the channel to v2, since v2 quests cannot run on the other
 * channels; otherwise a block on the page decides, then the global setting.
 *
 * @return void
 */
function maybe_insert_loader(): void {
	$block_request = block_loader_request();
	$popup_request = popup_loader_request();
	$popup_v2      = popup_v2_enabled();

	$needed = $block_request['present']
		|| $popup_request['present']
		|| $popup_v2
		|| (bool) get_option( ALWAYS_LOAD_OPTION, 0 );

	if ( ! $needed ) {
		return;
	}

	// Preload the bundle only when the loader will fetch it without the visitor
	// doing anything. That is a block in the content, since the viewport gate
	// fires as soon as it scrolls near, an auto popup, or a Popup v2 quest. A
	// hover-opened popup or the always-load setting alone may never need the
	// bundle. Preloading a megabyte of vendor code for nothing would only slow
	// the host page down.
	$preload_bundle = $block_request['present']
		|| ( $popup_request['eager'] ?? false )
		|| $popup_v2;

	if ( $block_request['has_v2_quest'] || $popup_request['has_v2_quest'] || $popup_v2 ) {
		insert_jquest_script( 'v2', $preload_bundle );
		return;
	}

	insert_jquest_script(
		$block_request['version']
			?? $popup_request['version']
			?? (string) get_option( POPUP_VERSION_OPTION, DEFAULT_VERSION ),
		$preload_bundle
	);
}

// Priority 2 runs right after wp_enqueue_scripts at 1 and ahead of the theme's
// styles at 8 and head scripts at 9. The loader and the preload hints are then
// among the first tags in the head, with nothing from the host page queued
// ahead of them.
add_action( 'wp_head', __NAMESPACE__ . '\maybe_insert_loader', 2 );

/**
 * Outputs the Popup v2 quest div at the top of the footer, i.e. just above
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
	// stays a plain template with no inline PHP. The quests are always v2 ones —
	// the settings page only offers v2 quests — so the version is hard-coded
	// rather than looked up, which keeps it right even when the stored quest
	// list is stale or empty.
	$lang   = current_language();
	$org_id = esc_attr( get_option( 'jquest_org_id', '' ) );
	$locale = 'default' === $lang ? '' : esc_attr( $lang );

	foreach ( $quest_ids as $quest_id ) {
		$quest_id = esc_attr( $quest_id );

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
			data-version="v2"
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
 * One-time migration from the legacy single Popup v2 quest per language to the
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
	// Find every language's legacy option, e.g. jquest_popup_v2_en_quest_id.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$legacy_options = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( 'jquest_popup_v2_' ) . '%' . $wpdb->esc_like( '_quest_id' )
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
		// safe direction: the alternative puts a v2 popup back onto a page whose
		// block needs another script channel.
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
 * Outputs the jQuest popup div into the footer when the popup is enabled for the current language.
 *
 * @return void
 */
function maybe_insert_popup_div(): void {
	$lang   = current_language();
	$prefix = 'jquest_popup_' . $lang . '_';

	if ( ! get_option( $prefix . 'enabled', 0 ) ) {
		return;
	}

	// Prepare every value as a finished, escaped string so the markup below
	// stays a plain template with no inline PHP. The version goes out as
	// data-version because that is what the loader reads (dataset.version) —
	// a widget declaring v2 forces the v2 bundle for the whole page.
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
	$version_attr     = 'v2' === $quest_version ? "\n\tdata-version=\"v2\"" : '';

	// The popup div is hidden by CSS, so the loader's viewport observer can never
	// fire for it — something has to tell the loader when to fetch the bundle. A
	// popup the visitor opens gets that from the trigger button's
	// data-jq-load="hover", but an auto popup opens with no interaction at all,
	// so it has to declare itself eager.
	$load_attr = get_option( $prefix . 'auto', 0 ) ? "\n\tdata-jq-load=\"eager\"" : '';

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
		data-popup-disable-noscroll="{$disable_noscroll}"{$load_attr}
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

	$prefix = 'jquest_popup_' . current_language() . '_';

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
