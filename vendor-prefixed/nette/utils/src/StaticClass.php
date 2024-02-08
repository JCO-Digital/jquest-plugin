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

namespace JcoreBroiler\Nette;


/**
 * Static class.
 */
trait StaticClass
{
	/**
	 * Class is static and cannot be instantiated.
	 */
	private function __construct()
	{
	}


	/**
	 * Call to undefined static method.
	 * @throws MemberAccessException
	 */
	public static function __callStatic(string $name, array $args): mixed
	{
		Utils\ObjectHelpers::strictStaticCall(static::class, $name);
	}
}
