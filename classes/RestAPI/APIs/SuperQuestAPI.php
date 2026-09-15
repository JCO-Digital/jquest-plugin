<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace SuperQuestPlugin\RestAPI\APIs;

use SuperQuestPlugin\RestAPI\RestAPI;
use SuperQuestPlugin\RestAPI\RestAPIHelpers;
use WP_REST_Request;
use WP_REST_Response;
use function SuperQuestPlugin\fetch_quests;

/**
 * Example API class.
 * This class is an example of how to create a new API.
 * You can use this as a template for your own APIs or remove it if you don't need it.
 *
 * @package SuperQuestPlugin\RestAPI
 */
class SuperQuestAPI extends RestAPI {
	/**
	 * The base string for the rest route.
	 *
	 * @var string
	 */
	public static string $namespace = 'superquest/v1';

	/**
	 * Registers the API endpoints.
	 *
	 * @inheritDoc
	 */
	public static function register_endpoints(): void {
		register_rest_route(
			self::$namespace,
			'/quests',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_quests' ),
				'permission_callback' => array( RestAPIHelpers::class, 'is_user_logged_in' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/quests/refresh',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'refresh_quests' ),
				'permission_callback' => array( RestAPIHelpers::class, 'is_user_logged_in' ),
			)
		);
	}

	/**
	 * Returns the organisation and its stored quests.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_quests( WP_REST_Request $request ): WP_REST_Response {
		$organization = get_option( 'superquest_org_id' );
		$quests       = get_option( 'superquest_org_quests' );

		$data = array(
			'organization' => $organization,
			'quests'       => $quests,
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
	public static function refresh_quests( WP_REST_Request $request ): WP_REST_Response {
		$organization = trim( (string) get_option( 'superquest_org_id', '' ) );
		if ( '' !== $organization ) {
			fetch_quests( $organization );
		}

		return self::get_quests( $request );
	}
}
