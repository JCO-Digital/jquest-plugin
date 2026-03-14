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
			'dashicons-games',
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
		}

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
		wp_enqueue_style( 'jquest-admin' );
		wp_enqueue_script( 'jquest-backend' );
	}

	/**
	 * Handles the rendering of the popup settings page.
	 *
	 * @return void
	 */
	final public function render_popup_page(): void {
		$tabs = array();

		if ( function_exists( 'pll_languages_list' ) ) {
			foreach ( pll_languages_list( array( 'fields' => 'slug' ) ) as $slug ) {
				$tabs[ $slug ] = array(
					'label' => strtoupper( $slug ),
					'url'   => add_query_arg( array( 'tab' => $slug ), admin_url( 'admin.php?page=jquest-popup' ) ),
				);
			}
		}

		$first_tab  = ! empty( $tabs ) ? array_key_first( $tabs ) : '';
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : $first_tab; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

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
