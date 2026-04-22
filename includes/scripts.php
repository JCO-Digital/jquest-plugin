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
function insert_jquest_script(string $url): void
{
	static $inserted = false;

	if ($inserted) {
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
		src="<?php echo esc_url($url); ?>"></script>
	<?php
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped


}

/**
 * Checks the post content for JQUEST blocks and inserts the script if found.
 *
 * @return void
 */
function maybe_insert_jquest_script()
{
	if (!has_blocks() || !has_block('jquest-inserter/jquest-inserter')) {
		return;
	}

	$has_jquest_blocks = false;
	$version = false;

	$post = get_post();
	if (!$post) {
		return;
	}

	$blocks = parse_blocks($post->post_content);
	foreach ($blocks as $block) {
		$results = contains_jquest_insterter($block);

		$has_jquest_blocks = $results['has_jquest_blocks'];
		$version = $results['version'];
		if (!!$version) {
			break;
		}
	}
	if ($has_jquest_blocks) {
		if ($version == 'stable') {
			$url = 'https://files.jquest.fi/jquest/stable/jquest-stable.js';
		} elseif ($version == 'latest') {
			$url = 'https://files.jquest.fi/jquest/latest/jquest-latest.js';
		}
		if (isset($url)) {
			insert_jquest_script($url);
		}
	}
}

function contains_jquest_insterter($block): array
{
	$return_value = [
		'has_jquest_blocks' => false,
		'version' => false,
	];

	if ('jquest-inserter/jquest-inserter' === $block['blockName']) {
		$return_value['has_jquest_blocks'] = true;
		if (false === $return_value['version']) {
			// Unfortunately, this is the only way to get the default attributes from the block...
			// This could be avoided by just assuming the default value, but then we would need to keep the block.json in sync with this.
			$rendered_block = apply_filters('the_content', render_block($block));
			if (strpos($rendered_block, 'data-version="stable"') !== false) {
				$return_value['version'] = 'stable';
			}
			if (strpos($rendered_block, 'data-version="latest"') !== false) {
				$return_value['version'] = 'latest';
			}
		}
	}

	if (array_key_exists('innerBlocks', $block)) {
		foreach ($block['innerBlocks'] as $inner_block) {
			$inner_values = contains_jquest_insterter($inner_block);

			$return_value['has_jquest_blocks'] =
				$return_value['has_jquest_blocks'] || $inner_values['has_jquest_blocks'];
			$return_value['version'] = $return_value['version'];
		}
	}

	return $return_value;
}

add_action('wp_head', __NAMESPACE__ . '\maybe_insert_jquest_script');

/**
 * Inserts the jQuest popup script when the popup is enabled for the current language.
 *
 * @return void
 */
function maybe_insert_popup_script(): void
{
	$lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
	$prefix = 'jquest_popup_' . $lang . '_';

	if (!get_option($prefix . 'enabled', 0)) {
		return;
	}

	$url = get_option($prefix . 'latest_script', 0)
		? 'https://files.jquest.fi/jquest/latest/jquest-latest.js'
		: 'https://files.jquest.fi/jquest/stable/jquest-stable.js';

	insert_jquest_script($url);
}

add_action('wp_head', __NAMESPACE__ . '\maybe_insert_popup_script');

/**
 * Outputs the jQuest popup div into the footer when the popup is enabled for the current language.
 *
 * @return void
 */
function maybe_insert_popup_div(): void
{
	$lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
	$prefix = 'jquest_popup_' . $lang . '_';

	if (!get_option($prefix . 'enabled', 0)) {
		return;
	}

	$org_id = get_option('jquest_org_id', '');
	$quest_id = get_option($prefix . 'quest_id', '');
	$auto = get_option($prefix . 'auto', 0) ? 'true' : 'false';
	$limit = (int) get_option($prefix . 'limit', 0);
	$attach = get_option($prefix . 'attach', 'body');
	$disable_dismiss = get_option($prefix . 'disable_dismiss', 1) ? 'true' : 'false';
	$disable_noscroll = get_option($prefix . 'disable_noscroll', 1) ? 'true' : 'false';
	$locale = $lang === 'default' ? '' : $lang;
	?>
	<div
		class="jquest-app"
		data-new-styles="true"
		data-locale="<?php echo esc_attr($locale); ?>"
		data-org-id="<?php echo esc_attr($org_id); ?>"
		data-game-id="<?php echo esc_attr($quest_id); ?>"
		data-popup="true"
		data-popup-auto="<?php echo esc_attr($auto); ?>"
		data-popup-limit="<?php echo esc_attr($limit); ?>"
		data-popup-attach="<?php echo esc_attr($attach); ?>"
		data-popup-disable-dismiss="<?php echo esc_attr($disable_dismiss); ?>"
		data-popup-disable-noscroll="<?php echo esc_attr($disable_noscroll); ?>"
	></div>
	<?php
}

add_action('wp_footer', __NAMESPACE__ . '\maybe_insert_popup_div');

/**
 * Outputs the popup trigger button styles from global settings, once per page.
 *
 * @return void
 */
function output_popup_trigger_styles(): void
{
	static $output = false;
	if ($output) {
		return;
	}
	$output = true;

	$text_color = get_option('jquest_popup_trigger_text_color', '#1a2e40');
	$bg_color = get_option('jquest_popup_trigger_bg_color', '#ffffff');
	$text_hover_color = get_option('jquest_popup_trigger_text_hover_color', '#1a2e40');
	$bg_hover_color = get_option('jquest_popup_trigger_bg_hover_color', '#f0f0f0');
	$icon_bg_color = get_option('jquest_popup_trigger_icon_bg_color', '#ffffff');
	$icon_bg_hover_color = get_option('jquest_popup_trigger_icon_bg_hover_color', '');
	$icon_color = get_option('jquest_popup_trigger_icon_color', '');
	$icon_hover_color = get_option('jquest_popup_trigger_icon_hover_color', '');
	$side = get_option('jquest_popup_trigger_side', 'right');
	$offset_x = (int) get_option('jquest_popup_trigger_offset_x', 16);
	$offset_y = (int) get_option('jquest_popup_trigger_offset_y', 16);
	$border_radius = (int) get_option('jquest_popup_trigger_border_radius', 25);
	$padding_top = (int) get_option('jquest_popup_trigger_padding_top', 11);
	$padding_right = (int) get_option('jquest_popup_trigger_padding_right', 23);
	$padding_bottom = (int) get_option('jquest_popup_trigger_padding_bottom', 11);
	$padding_left = (int) get_option('jquest_popup_trigger_padding_left', 23);
	$icon_container_size = (int) get_option('jquest_popup_trigger_icon_container_size', 29);
	$icon_container_border_radius = (int) get_option(
		'jquest_popup_trigger_icon_container_border_radius',
		50,
	);
	$items_gap = (int) get_option('jquest_popup_trigger_items_gap', 8);
	$icon_size = (int) get_option('jquest_popup_trigger_icon_size', 20);
	$font_size = (int) get_option('jquest_popup_trigger_font_size', 18);
	$font_weight = get_option('jquest_popup_trigger_font_weight', '400');
	$underline_width = (int) get_option('jquest_popup_trigger_underline_width', 1);
	$underline_color = get_option('jquest_popup_trigger_underline_color', '');
	$underline_hover_color = get_option('jquest_popup_trigger_underline_hover_color', '');
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<style>
		.jquest-app[data-popup='true'] { display: none; }
		.jquest-popup-toggle {
			position: fixed;
			bottom: <?php echo esc_attr($offset_y); ?>px;
			<?php echo esc_attr($side); ?>: <?php echo esc_attr($offset_x); ?>px;
			z-index: 99;
			max-width: 100%;
			transition: all .3s;
			* {
				transition: all .3s;
			}

			a {
			    align-items: center;
				position: relative;
				background-color: <?php echo esc_attr($bg_color); ?>;
				cursor: pointer;
				border-radius: <?php echo esc_attr($border_radius); ?>px;
				text-decoration: none;
				color: <?php echo esc_attr($text_color); ?>;
				padding: <?php echo esc_attr($padding_top); ?>px <?php echo esc_attr(
	$padding_right,
); ?>px <?php echo esc_attr($padding_bottom); ?>px <?php echo esc_attr($padding_left); ?>px;
				<?php if ($font_size > 0): ?>font-size: <?php echo esc_attr($font_size); ?>px;<?php endif; ?>
				font-weight: <?php echo esc_attr($font_weight); ?>;
				border-bottom: <?php echo esc_attr($underline_width); ?>px solid <?php echo '' !== $underline_color
	? esc_attr($underline_color)
	: 'transparent'; ?>;
				display: flex;
				gap: <?php echo esc_attr($items_gap); ?>px;
    			height: 100%;
			      .label{
					    margin-top: auto;
						margin-bottom: auto;
			     		line-height: 18px;
			      }
			}
		}
		.jquest-popup-toggle a:hover {
			background-color: <?php echo esc_attr($bg_hover_color); ?>;
			color: <?php echo esc_attr($text_hover_color); ?>;
			border-bottom-color: <?php echo '' !== $underline_hover_color
   	? esc_attr($underline_hover_color)
   	: 'transparent'; ?>;
		}
		.jquest-popup-toggle a .icon-container {
			border-radius: <?php echo esc_attr($icon_container_border_radius); ?>px;
			background-color: <?php echo esc_attr($icon_bg_color); ?>;
			height: <?php echo esc_attr($icon_container_size); ?>px;
			width: <?php echo esc_attr($icon_container_size); ?>px;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.jquest-popup-toggle a .icon-container svg {
			width: <?php echo esc_attr($icon_size); ?>px;
			height: <?php echo esc_attr($icon_size); ?>px;
			<?php if ('' !== $icon_color): ?>color: <?php echo esc_attr($icon_color); ?>;<?php endif; ?>
		}
		<?php if ('' !== $icon_bg_hover_color): ?>
		.jquest-popup-toggle a:hover .icon-container { background-color: <?php echo esc_attr(
  	$icon_bg_hover_color,
  ); ?>; }
		<?php endif; ?>
		<?php if ('' !== $icon_hover_color): ?>
		.jquest-popup-toggle a:hover .icon-container svg { color: <?php echo esc_attr(
  	$icon_hover_color,
  ); ?>; }
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
	<?php
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Outputs the jQuest popup trigger button when enabled.
 *
 * @return void
 */
function maybe_insert_popup_trigger(): void
{
	if (!get_option('jquest_popup_trigger_enabled', 0)) {
		return;
	}

	$lang = function_exists('pll_current_language') ? pll_current_language() : 'default';
	$prefix = 'jquest_popup_' . $lang . '_';

	if (!get_option($prefix . 'enabled', 0)) {
		return;
	}

	$label = get_option($prefix . 'desktop_label', '');
	$label_mobile = get_option($prefix . 'mobile_label', '');
	if ('' === $label) {
		$label = get_option($prefix . 'mobile_label', '');
	}
	$quest_id = get_option($prefix . 'quest_id', '');

	$icon_mode = get_option('jquest_popup_trigger_icon_mode', 'default');

	$svg_kses = [
		'svg' => [
			'xmlns' => true,
			'viewBox' => true,
			'width' => true,
			'height' => true,
			'fill' => true,
		],
		'path' => [
			'd' => true,
			'fill' => true,
			'fill-rule' => true,
			'clip-rule' => true,
			'stroke' => true,
			'stroke-width' => true,
		],
		'g' => ['fill' => true],
		'circle' => [
			'cx' => true,
			'cy' => true,
			'r' => true,
			'fill' => true,
			'stroke' => true,
			'stroke-width' => true,
		],
		'rect' => [
			'x' => true,
			'y' => true,
			'width' => true,
			'height' => true,
			'fill' => true,
			'rx' => true,
		],
	];

	if ('default' === $icon_mode) {
		$icon =
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2.046 21.8a.5.5 0 0 0 .62.635l4.87-1.515A9.96 9.96 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2Z"/></svg>';
	} elseif ('custom' === $icon_mode) {
		$icon = wp_kses(get_option('jquest_popup_trigger_icon_custom', ''), $svg_kses);
	} else {
		$icon = '';
	}
	output_popup_trigger_styles();
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	<div class="jquest-popup-toggle <?php echo isset($icon) && '' !== $icon
 	? 'has-icon'
 	: 'no-icon'; ?>">
		<a href="#jquest-popup-<?php echo esc_attr($quest_id); ?>">
			<?php if ('' !== $label || '' !== $label_mobile): ?>
				<span class="label">
					<?php if ('' !== $label): ?>
					<span class="desktop-only"><?php echo esc_html($label); ?></span>
					<?php endif; ?>
					<?php if ('' !== $label_mobile): ?>
					<span class="mobile-only"><?php echo esc_html($label_mobile); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<?php if ('' !== $icon): ?>
			<span class="icon-container"><?php echo $icon; ?></span>
			<?php endif; ?>
		</a>
	</div>
	<?php
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped


}

add_action('wp_footer', __NAMESPACE__ . '\maybe_insert_popup_trigger');

/**
 * Outputs trigger button styles when any jquest-inserter block on the page has the trigger button enabled.
 *
 * @return void
 */
function maybe_insert_block_trigger_styles(): void
{
	if (!has_blocks() || !has_block('jquest-inserter/jquest-inserter')) {
		return;
	}

	$post = get_post();
	if (!$post) {
		return;
	}

	$blocks = parse_blocks($post->post_content);
	if (block_has_trigger_button($blocks)) {
		output_popup_trigger_styles();
	}
}

/**
 * Recursively checks whether any jquest-inserter block has the trigger button enabled.
 *
 * @param array $blocks
 * @return bool
 */
function block_has_trigger_button(array $blocks): bool
{
	foreach ($blocks as $block) {
		if (
			'jquest-inserter/jquest-inserter' === $block['blockName'] &&
			!empty($block['attrs']['popup']) &&
			empty($block['attrs']['popupAuto']) &&
			!empty($block['attrs']['popupTriggerButton'])
		) {
			return true;
		}
		if (!empty($block['innerBlocks']) && block_has_trigger_button($block['innerBlocks'])) {
			return true;
		}
	}
	return false;
}

add_action('wp_footer', __NAMESPACE__ . '\maybe_insert_block_trigger_styles');

/**
 * Injects the trigger icon (from global settings) into jquest-inserter block output.
 *
 * @param string $content
 * @param array  $block
 * @return string
 */
function inject_block_trigger_icon(string $content, array $block): string
{
	if ('jquest-inserter/jquest-inserter' !== $block['blockName']) {
		return $content;
	}

	if (
		empty($block['attrs']['popup']) ||
		!empty($block['attrs']['popupAuto']) ||
		empty($block['attrs']['popupTriggerButton'])
	) {
		return $content;
	}

	$icon_mode = get_option('jquest_popup_trigger_icon_mode', 'default');

	if ('default' === $icon_mode) {
		$icon =
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2.046 21.8a.5.5 0 0 0 .62.635l4.87-1.515A9.96 9.96 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2Z"/></svg>';
	} elseif ('custom' === $icon_mode) {
		$svg_kses = [
			'svg' => ['xmlns' => true, 'viewBox' => true, 'width' => true, 'height' => true, 'fill' => true],
			'path' => ['d' => true, 'fill' => true, 'fill-rule' => true, 'clip-rule' => true, 'stroke' => true, 'stroke-width' => true],
			'g' => ['fill' => true],
			'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true],
			'rect' => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true, 'rx' => true],
		];
		$icon = wp_kses(get_option('jquest_popup_trigger_icon_custom', ''), $svg_kses);
	} else {
		$icon = '';
	}

	$class     = '' !== $icon ? 'has-icon' : 'no-icon';
	$icon_html = '' !== $icon ? '<span class="icon-container">' . $icon . '</span>' : '';

	return preg_replace_callback(
		'/<div class="jquest-popup-toggle">(.*?)<\/div>/s',
		function ($matches) use ($class, $icon_html) {
			$inner = preg_replace('/<\/a>/', $icon_html . '</a>', $matches[1], 1);
			return '<div class="jquest-popup-toggle ' . $class . '">' . $inner . '</div>';
		},
		$content,
	);
}

add_filter('render_block', __NAMESPACE__ . '\inject_block_trigger_icon', 10, 2);
