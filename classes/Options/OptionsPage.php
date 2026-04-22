<?php
/**
 * Options page class.
 *
 * @package jQuestPlugin\Options
 */

namespace jQuestPlugin\Options;

use jQuestPlugin\Singleton;
use function jQuestPlugin\fetch_jquests;
use function jQuestPlugin\render_template;
use function jQuestPlugin\render_text_field;
use function jQuestPlugin\style_register;

/**
 * Options page class.
 *
 * @since 0.1.0
 */
class OptionsPage extends Singleton {

	/**
	 * Initializes the options page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'enqueue_assets' ) );
		add_action( 'updated_option', array( $this, 'jquest_plugin_option_updated' ), 10, 3 );
		add_action( 'admin_post_jquest_refresh_games', array( $this, 'jquest_plugin_refresh_games' ) );
	}

	/**
	 * Handles refreshing the jQuest games.
	 *
	 * @return void
	 */
	public function jquest_plugin_refresh_games(): void {
		check_admin_referer( 'my_plugin_button_action_nonce', 'my_plugin_button_action_nonce_field' );

		if ( isset( $_POST['jquest_refresh_games'] ) ) {
			if ( trim( get_option( 'jquest_org_id', '' ) ) !== '' ) {
				fetch_jquests( get_option( 'jquest_org_id' ) );
			}

			$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : admin_url( 'admin.php?page=jquest-options' );
			wp_safe_redirect( $referer );
			exit;
		}
	}

	/**
	 * Handles fetching jQuest games
	 *
	 * @return void
	 */
	public function jquest_plugin_option_updated( $option_name, $old_value, $new_value ): void {
		if ( $option_name === 'jquest_org_id' && $old_value !== $new_value ) {
			if ( trim( $new_value ) !== '' ) {
				fetch_jquests( $new_value );
			} else {
				update_option( 'jquest_org_games', array() );
			}
		}
	}


	/**
	 * Adds the options page to the admin menu.
	 *
	 * @return void
	 */
	final public function add_page(): void {
		add_menu_page(
			'jQuest',
			'jQuest',
			'manage_options',
			'jquest-options',
			array( $this, 'render_page' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118 137"><path fill="black" d="M19.62,45.96v19.05c0,1.2-1.3,1.95-2.34,1.35L.78,56.84c-.48-.28-.78-.79-.78-1.35v-20.85c0-.56.3-1.07.78-1.35L58.07.21c.48-.28,1.08-.28,1.56,0l16.5,9.53c1.04.6,1.04,2.1,0,2.7L20.4,44.61c-.48.28-.78.79-.78,1.35ZM58.15,114.8L2.41,82.62c-1.04-.6-2.34.15-2.34,1.35v19.05c0,.56.29,1.07.78,1.35l57.29,33.08c.48.28,1.08.28,1.56,0l18.06-10.43c.48-.28.78-.79.78-1.35v-19.05c0-1.2-1.3-1.95-2.34-1.35l-16.5,9.53c-.48.28-1.08.28-1.56,0ZM98.08,45.72v64.35c0,1.2,1.3,1.95,2.34,1.35l16.5-9.52c.48-.28.78-.79.78-1.35V34.39c0-.56-.3-1.07-.78-1.35l-18.06-10.43c-.48-.28-1.08-.28-1.56,0l-16.5,9.53c-1.04.6-1.04,2.1,0,2.7l16.5,9.53c.48.28.78.79.78,1.35ZM77.94,80.54c.38-.3.61-.75.61-1.24v-20.94c0-.49-.23-.94-.61-1.24l-18.31-10.58c-.49-.28-1.08-.28-1.56,0l-18.31,10.58c-.38.3-.61.75-.61,1.24v20.94c0,.49.23.94.61,1.24l18.31,10.58c.47.28,1.07.28,1.56,0l18.31-10.58Z"/></svg>' ),
			80
		);

		add_submenu_page(
			'jquest-options',
			__( 'General', 'jquest-' ),
			__( 'General', 'jquest-' ),
			'manage_options',
			'jquest-options',
			array( $this, 'render_page' )
		);

		add_submenu_page(
			'jquest-options',
			__( 'Popup', 'jquest-' ),
			__( 'Popup', 'jquest-' ),
			'manage_options',
			'jquest-popup',
			array( $this, 'render_popup_page' )
		);
	}

	/**
	 * Handles registering the settings.
	 *
	 * @return void
	 */
	final public function register_settings(): void {

		register_setting(
			'jquest-options-general',
			'jquest_org_id',
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'jquest-general',
			'jquest_org_message',
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_setting(
			'jquest-general',
			'jquest_org_games',
			array(
				'sanitize_callback' => function ( $input ) {
					if ( is_array( $input ) ) {
						return array_map(
							function ( $item ) {
								if ( is_object( $item ) ) {
									$item->title = isset( $item->title ) ? sanitize_text_field( $item->title ) : '';
									$item->ID = isset( $item->ID ) ? sanitize_text_field( $item->ID ) : '';
									return $item;
								}
								return (object) array(
									'title' => '',
									'id'    => '',
								);
							},
							$input
						);
					}
					return array();
				},
			)
		);

		// Popup settings — one group per language (or 'default' when Polylang is inactive).
		$popup_langs = function_exists( 'pll_languages_list' )
			? pll_languages_list( array( 'fields' => 'slug' ) )
			: array( 'default' );

		foreach ( $popup_langs as $lang ) {
			$prefix = 'jquest_popup_' . $lang . '_';
			$group  = 'jquest-popup-' . $lang;

			register_setting( $group, $prefix . 'enabled', array( 'sanitize_callback' => 'absint' ) );
			register_setting( $group, $prefix . 'quest_id', array( 'sanitize_callback' => 'sanitize_text_field' ) );
			register_setting( $group, $prefix . 'desktop_label', array( 'sanitize_callback' => 'sanitize_text_field' ) );
			register_setting( $group, $prefix . 'mobile_label', array( 'sanitize_callback' => 'sanitize_text_field' ) );
			register_setting( $group, $prefix . 'attach', array( 'sanitize_callback' => 'sanitize_text_field' ) );
			register_setting( $group, $prefix . 'auto', array( 'sanitize_callback' => 'absint' ) );
			register_setting( $group, $prefix . 'limit', array( 'sanitize_callback' => 'absint' ) );
			register_setting( $group, $prefix . 'disable_dismiss', array( 'sanitize_callback' => 'absint' ) );
			register_setting( $group, $prefix . 'disable_noscroll', array( 'sanitize_callback' => 'absint' ) );
			register_setting( $group, $prefix . 'latest_script', array( 'sanitize_callback' => 'absint' ) );
		}

		// Trigger settings — global (not per-language).
		$svg_kses = array(
			'svg'    => array( 'xmlns' => true, 'viewBox' => true, 'width' => true, 'height' => true, 'fill' => true ),
			'path'   => array( 'd' => true, 'fill' => true, 'fill-rule' => true, 'clip-rule' => true, 'stroke' => true, 'stroke-width' => true ),
			'g'      => array( 'fill' => true ),
			'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
			'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true, 'rx' => true ),
		);

		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_enabled', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_minimized', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_watch_selector', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_watch_threshold', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_font_size', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_font_weight', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_underline_width', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_underline_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_underline_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_text_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_bg_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_text_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_bg_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_bg_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_bg_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_mode', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_container_size', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_container_border_radius', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_items_gap', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_icon_size', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_side', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_offset_x', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_offset_y', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_border_radius', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_padding_top', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_padding_right', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_padding_bottom', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'jquest-popup-trigger', 'jquest_popup_trigger_padding_left', array( 'sanitize_callback' => 'absint' ) );
		register_setting(
			'jquest-popup-trigger',
			'jquest_popup_trigger_icon_custom',
			array(
				'sanitize_callback' => function( $input ) use ( $svg_kses ) {
					return wp_kses( $input, $svg_kses );
				},
			)
		);

		// Settings sections.
		add_settings_section(
			'jquest-general',
			__( 'General', 'jquest-' ),
			array( $this, 'render_general_section' ),
			'jquest-options-general',
		);

		add_settings_field(
			'jquest_org_id',
			__( 'Organisation ID', 'jquest-' ),
			array( $this, 'render_id_field' ),
			'jquest-options-general',
			'jquest-general',
			array(
				'label_for' => 'jquest_org_id',
			)
		);
	}

	/**
	 * Renders the General section.
	 *
	 * @return void
	 */
	final public function render_general_section(): void {
		echo '<p>' . esc_html__( 'General settings required for jQuest integration.', 'jquest-' ) . '</p>';
	}


	/**
	 * Handles rendering the ID field.
	 *
	 * @return void
	 */
	final public function render_id_field(): void {
		render_text_field(
			array(
				'id'          => 'jquest_org_id',
				'value'       => get_option( 'jquest_org_id', '' ),
				'placeholder' => __( 'Organisation ID', 'jquest-' ),
			)
		);
	}

	/**
	 * Enqueues the assets for the options page.
	 *
	 * @return void
	 */
	final public function enqueue_assets(): void {
		style_register( 'jquest-admin', 'assets/css/admin.css' );
		wp_enqueue_style( 'jquest-admin' );
		wp_enqueue_script( 'jquest-backend' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		$palette = wp_get_global_settings( array( 'color', 'palette' ) );
		$colors  = array_column(
			array_merge( $palette['theme'] ?? array(), $palette['default'] ?? array() ),
			'color'
		);
		wp_localize_script( 'wp-color-picker', 'jquestColorPalette', $colors );
		wp_add_inline_script(
			'wp-color-picker',
			'jQuery(function($){ $(".jquest-color-picker").wpColorPicker({ palettes: window.jquestColorPalette }); });',
			'after'
		);
	}

	/**
	 * Handles the rendering of the popup settings page.
	 *
	 * @return void
	 */
	final public function render_popup_page(): void {
		$tabs = array(
			'trigger' => array(
				'label' => __( 'Trigger', 'jquest-' ),
				'url'   => add_query_arg( array( 'tab' => 'trigger' ), admin_url( 'admin.php?page=jquest-popup' ) ),
			),
		);

		if ( function_exists( 'pll_languages_list' ) ) {
			foreach ( pll_languages_list( array( 'fields' => 'slug' ) ) as $slug ) {
				$tabs[ $slug ] = array(
					'label' => strtoupper( $slug ),
					'url'   => add_query_arg( array( 'tab' => $slug ), admin_url( 'admin.php?page=jquest-popup' ) ),
				);
			}
		} else {
			$tabs['default'] = array(
				'label' => __( 'Popup', 'jquest-' ),
				'url'   => add_query_arg( array( 'tab' => 'default' ), admin_url( 'admin.php?page=jquest-popup' ) ),
			);
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'trigger'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$lang_key = $active_tab ?: 'default';

		$data = array(
			'tabs'       => $tabs,
			'active_tab' => $active_tab,
			'lang_key'   => $lang_key,
			'games'      => get_option( 'jquest_org_games', array() ),
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'popup-settings', $data );
	}

	/**
	 * Handles the rendering of the options page.
	 *
	 * @return void
	 */
	final public function render_page(): void {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = array(
			'tabs'       => array(
				'general' => array(
					'label' => __( 'General', 'jquest-' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=jquest-options' ) ),
				),
			),
			'active_tab' => $tab,
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'admin-settings', $data );
	}
}
