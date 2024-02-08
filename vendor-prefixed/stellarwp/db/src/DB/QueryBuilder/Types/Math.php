<?php
/**
 * @license GPL-2.0
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\StellarWP\DB\QueryBuilder\Types;

/**
 * @since 1.0.0
 */
class Math extends Type {
	const SUM = 'SUM';
	const MIN = 'MIN';
	const MAX = 'MAX';
	const COUNT = 'COUNT';
	const AVG = 'AVG';
}
