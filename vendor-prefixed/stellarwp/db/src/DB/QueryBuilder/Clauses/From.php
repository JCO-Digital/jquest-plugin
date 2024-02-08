<?php
/**
 * @license GPL-2.0
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\StellarWP\DB\QueryBuilder\Clauses;

use JcoreBroiler\StellarWP\DB\QueryBuilder\QueryBuilder;

/**
 * @since 1.0.0
 */
class From {
	/**
	 * @var string|RawSQL
	 */
	public $table;

	/**
	 * @var string
	 */
	public $alias;

	/**
	 * @param  string|RawSQL  $table
	 * @param  string|null  $alias
	 */
	public function __construct( $table, $alias = '' ) {
		$this->table = QueryBuilder::prefixTable( $table );
		$this->alias = is_scalar( $alias ) ? trim( (string) $alias ) : '';
	}
}
