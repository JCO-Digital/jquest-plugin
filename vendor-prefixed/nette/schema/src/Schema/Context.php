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


final class Context
{
	public bool $skipDefaults = false;

	/** @var string[] */
	public array $path = [];

	public bool $isKey = false;

	/** @var Message[] */
	public array $errors = [];

	/** @var Message[] */
	public array $warnings = [];

	/** @var array[] */
	public array $dynamics = [];


	public function addError(string $message, string $code, array $variables = []): Message
	{
		$variables['isKey'] = $this->isKey;
		return $this->errors[] = new Message($message, $code, $this->path, $variables);
	}


	public function addWarning(string $message, string $code, array $variables = []): Message
	{
		return $this->warnings[] = new Message($message, $code, $this->path, $variables);
	}


	/** @return \Closure(): bool */
	public function createChecker(): \Closure
	{
		$count = count($this->errors);
		return fn(): bool => $count === count($this->errors);
	}
}
