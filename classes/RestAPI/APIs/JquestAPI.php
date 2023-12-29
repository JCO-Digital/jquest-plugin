<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\RestAPI\APIs;

use JcoreBroiler\Options\Option;
use JcoreBroiler\RestAPI\RestAPI;
use JcoreBroiler\RestAPI\RestAPIHelpers;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Example API class.
 * This class is an example of how to create a new API.
 * You can use this as a template for your own APIs or remove it if you don't need it.
 *
 * @package JcoreBroiler\RestAPI
 */
class JquestAPI extends RestAPI {
	/**
	 * The base string for the rest route.
	 *
	 * @var string
	 */
	public static string $namespace = 'jquest/v1';

	/**
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
	}

	/**
	 * Example callback function.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_games( WP_REST_Request $request ): WP_REST_Response {
		$organization = Option::get( 'organization_id' );
		$games        = Option::get( 'organization_games' );

		$data = array(
			'organization' => $organization,
			'games'        => $games,
		);
		return new WP_REST_Response(
			$data
		);
	}
}
