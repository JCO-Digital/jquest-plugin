<?php
/**
 * @license GPL-2.0
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\StellarWP\DB\QueryBuilder;

use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\Aggregate;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\CRUD;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\FromClause;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\GroupByStatement;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\HavingClause;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\JoinClause;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\LimitStatement;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\MetaQuery;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\OffsetStatement;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\OrderByStatement;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\SelectStatement;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\TablePrefix;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\UnionOperator;
use JcoreBroiler\StellarWP\DB\QueryBuilder\Concerns\WhereClause;

/**
 * @since 1.0.0
 */
class QueryBuilder {
	use Aggregate;
	use CRUD;
	use FromClause;
	use GroupByStatement;
	use HavingClause;
	use JoinClause;
	use LimitStatement;
	use MetaQuery;
	use OffsetStatement;
	use OrderByStatement;
	use SelectStatement;
	use TablePrefix;
	use UnionOperator;
	use WhereClause;

	/**
	 * @return string
	 */
	public function getSQL() {
		$sql = array_merge(
			$this->getSelectSQL(),
			$this->getFromSQL(),
			$this->getJoinSQL(),
			$this->getWhereSQL(),
			$this->getGroupBySQL(),
			$this->getHavingSQL(),
			$this->getOrderBySQL(),
			$this->getLimitSQL(),
			$this->getOffsetSQL(),
			$this->getUnionSQL()
		);

		// Trim double spaces added by DB::prepare
		return str_replace(
			[ '   ', '  ' ],
			' ',
			implode( ' ', $sql )
		);
	}
}
