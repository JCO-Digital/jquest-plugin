<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 *
 * @license BSD-3-Clause,GPL-2.0-only,GPL-3.0-only
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace JcoreBroiler\Nette\Schema;


interface Schema
{
	/**
	 * Normalization.
	 * @return mixed
	 */
	function normalize(mixed $value, Context $context);

	/**
	 * Merging.
	 * @return mixed
	 */
	function merge(mixed $value, mixed $base);

	/**
	 * Validation and finalization.
	 * @return mixed
	 */
	function complete(mixed $value, Context $context);

	/**
	 * @return mixed
	 */
	function completeDefault(Context $context);
}
