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

use JcoreBroiler\Nette;


/**
 * Validation error.
 */
class ValidationException extends JcoreBroiler\Nette\InvalidStateException
{
	/** @var Message[] */
	private array $messages;


	/**
	 * @param  Message[]  $messages
	 */
	public function __construct(?string $message, array $messages = [])
	{
		parent::__construct($message ?: $messages[0]->toString());
		$this->messages = $messages;
	}


	/**
	 * @return string[]
	 */
	public function getMessages(): array
	{
		$res = [];
		foreach ($this->messages as $message) {
			$res[] = $message->toString();
		}

		return $res;
	}


	/**
	 * @return Message[]
	 */
	public function getMessageObjects(): array
	{
		return $this->messages;
	}
}
