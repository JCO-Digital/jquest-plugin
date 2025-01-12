<?php
/**
 * Options page class.
 *
 * @package jQuestPlugin\Options
 */

namespace jQuestPlugin\Options;

use jQuestPlugin\Singleton;
use function jQuestPlugin\render_checkbox_field;
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
			'jquest-options',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Handles registering the settings.
	 *
	 * @return void
	 */
	final public function register_settings(): void {
		// Settings registration.
		register_setting(
			'jquest-options-general',
			'jquest_is_enabled',
			array(
				'sanitize_callback' => 'boolval',
			)
		);
		register_setting(
			'jquest-options-general',
			'jquest_org_id',
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Settings sections.
		add_settings_section(
			'jquest-general',
			__( 'General', 'jquest-plugin' ),
			array( $this, 'render_general_section' ),
			'jquest-options-general',
		);

		// Settings fields.
		add_settings_field(
			'jquest_is_enabled',
			__( 'Enabled', 'jquest-plugin' ),
			array( $this, 'render_enabled_field' ),
			'jquest-options-general',
			'jquest-general'
		);
		add_settings_field(
			'jquest_org_id',
			__( 'Project ID', 'jquest-plugin' ),
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
	 * Renders the Enabled field.
	 *
	 * @return void
	 */
	final public function render_enabled_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'jquest_is_enabled',
				'value'       => get_option( 'jquest_is_enabled', '' ),
				'placeholder' => __( 'Enable jQuest', 'jquest-plugin' ),
			)
		);
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
