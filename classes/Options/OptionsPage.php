<?php
/**
 * Options page class.
 *
 * @package jQuestPlugin\Options
 */

namespace jQuestPlugin\Options;

use jQuestPlugin\jquest_table;
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
		add_action( 'updated_option', array( $this, 'jquest_plugin_option_updated' ), 10, 3);
		add_action('admin_post_jquest_refresh_games', array( $this, 'jquest_plugin_refresh_games' ));
	}

	function jquest_plugin_refresh_games() {
		if ( isset($_POST['jquest_refresh_games']) ) {
			if( trim(get_option('jquest_org_id', '')) !== '' ) {
				fetch_jquests(get_option('jquest_org_id'));
			}

			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('options-general.php?page=jquest-option');
			wp_redirect($referer);
			exit;
		}
	}

	/**
	 * Handles fetching jQuest games
	 *
	 * @return void
	 */
	function jquest_plugin_option_updated($option_name, $old_value, $new_value) {
		if ( $option_name === 'jquest_org_id' && $old_value !== $new_value ) {
			if( trim($new_value) !== '' ) {
				fetch_jquests(get_option('jquest_org_id'));
			} else {
				update_option('jquest_org_games', []);
			}

		}
	}


	/**
	 * Adds the options page to the admin menu.
	 *
	 * @return void
	 */
	final public function add_page(): void {
		add_options_page(
			'jQuest Options',
			'jQuest Settings',
			'manage_options',
			'jquest-option',
			array( $this, 'render_page' )
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
				'sanitize_callback' => function($input) {
					if (is_array($input)) {
						return array_map(function($item) {
							if (is_object($item)) {
								$item->title = isset($item->title) ? sanitize_text_field($item->title) : '';
								$item->ID = isset($item->ID) ? sanitize_text_field($item->ID) : '';
								return $item;
							}
							return (object) ['title' => '', 'id' => ''];
						}, $input);
					}
					return [];
				},
			)
		);

		// Settings sections.
		add_settings_section(
			'jquest-general',
			__( 'General', 'jquest-plugin' ),
			array( $this, 'render_general_section' ),
			'jquest-options-general',
		);

		add_settings_field(
			'jquest_org_id',
			__( 'Organisation ID', 'jquest-plugin' ),
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
		echo '<p>' . esc_html__( 'General settings required for jQuest integration.', 'jquest-plugin' ) . '</p>';
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
				'placeholder' => __( 'Organisation ID', 'jquest-plugin' ),
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
	 * Handles the rendering of the options page.
	 *
	 * @return void
	 */
	final public function render_page(): void {

		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = array(
			'tabs'       => array(
				'general' => array(
					'label' => __( 'General', 'jquest-plugin' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=jquest-options' ) ),
				),
			),
			'active_tab' => $tab,
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'admin-settings', $data );
	}
}
