<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace jQuestPlugin\RestAPI\APIs;

use jQuestPlugin\RestAPI\RestAPI;
use jQuestPlugin\RestAPI\RestAPIHelpers;
use WP_REST_Request;
use WP_REST_Response;
use function jQuestPlugin\fetch_jquests;

/**
 * Example API class.
 * This class is an example of how to create a new API.
 * You can use this as a template for your own APIs or remove it if you don't need it.
 *
 * @package jQuestPlugin\RestAPI
 */
class JquestAPI extends RestAPI {
	/**
	 * The base string for the rest route.
	 *
	 * @var string
	 */
	public static string $namespace = 'jquest/v1';

	/**
	 * Registers the API endpoints.
	 *
	 * @inheritDoc
	 */
	public static function register_endpoints(): void {
		register_rest_route(
			self::$namespace,
			'/games',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_games' ),
				'permission_callback' => array( RestAPIHelpers::class, 'is_user_logged_in' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/games/refresh',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'refresh_games' ),
				'permission_callback' => array( RestAPIHelpers::class, 'is_user_logged_in' ),
			)
		);
	}

	/**
	 * Example callback function.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_games( WP_REST_Request $request ): WP_REST_Response {
		$organization = get_option( 'jquest_org_id' );
		$games        = get_option( 'jquest_org_games' );

		$data = array(
			'organization' => $organization,
			'games'        => $games,
		);
		return new WP_REST_Response(
			$data
		);
	}

	/**
	 * Refreshes the stored quests and returns the updated collection.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function refresh_games( WP_REST_Request $request ): WP_REST_Response {
		$organization = trim( (string) get_option( 'jquest_org_id', '' ) );
		if ( '' !== $organization ) {
			fetch_jquests( $organization );
		}

		return self::get_games( $request );
	}
}
