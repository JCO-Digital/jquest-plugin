<?php

namespace jQuestPlugin\Options;

/**
 * Class Option, handles the options.
 *
 * @package jQuestPlugin\Options
 */
class Option {

	/**
	 * The namespace for the options.
	 *
	 * @var string
	 */
	private static string $namespace = JQUEST_OPTION_PREFIX;

	/**
	 * Returns the namespace.
	 *
	 * @param string $key The key to get the namespaced option for.
	 *
	 * @return string
	 */
	public static function get_namespaced_option( string $key ): string {
		return self::$namespace . '_' . $key;
	}

	/**
	 * Handles getting the option.
	 *
	 * @param string $key The key to get the option for.
	 * @param mixed  $default_value The default value to return if the option is not found.
	 *
	 * @return false|mixed|null
	 */
	public static function get( string $key, mixed $default_value = false ): mixed {
		return get_option( self::get_namespaced_option( $key ), $default_value );
	}

	/**
	 * Handles setting the option.
	 *
	 * @param string $key The key to set the option for.
	 * @param mixed  $value The value to set the option to.
	 *
	 * @return bool
	 */
	public static function set( string $key, mixed $value ): bool {
		return update_option( self::get_namespaced_option( $key ), $value );
	}

	/**
	 * Handels removing the option.
	 *
	 * @param string $key - The key to remove the option for.
	 *
	 * @return bool
	 */
	public static function remove( string $key ): bool {
		return delete_option( self::get_namespaced_option( $key ) );
	}
}
