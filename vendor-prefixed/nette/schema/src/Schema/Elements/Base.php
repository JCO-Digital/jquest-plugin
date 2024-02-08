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

namespace JcoreBroiler\Nette\Schema\Elements;

use JcoreBroiler\Nette;
use JcoreBroiler\Nette\Schema\Context;
use JcoreBroiler\Nette\Schema\Helpers;


/**
 * @internal
 */
trait Base
{
	private bool $required = false;
	private mixed $default = null;

	/** @var ?callable */
	private $before;

	/** @var callable[] */
	private array $transforms = [];
	private ?string $deprecated = null;


	public function default(mixed $value): self
	{
		$this->default = $value;
		return $this;
	}


	public function required(bool $state = true): self
	{
		$this->required = $state;
		return $this;
	}


	public function before(callable $handler): self
	{
		$this->before = $handler;
		return $this;
	}


	public function castTo(string $type): self
	{
		return $this->transform(Helpers::getCastStrategy($type));
	}


	public function transform(callable $handler): self
	{
		$this->transforms[] = $handler;
		return $this;
	}


	public function assert(callable $handler, ?string $description = null): self
	{
		$expected = $description ?: (is_string($handler) ? "$handler()" : '#' . count($this->transforms));
		return $this->transform(function ($value, Context $context) use ($handler, $description, $expected) {
			if ($handler($value)) {
				return $value;
			}
			$context->addError(
				'Failed assertion ' . ($description ? "'%assertion%'" : '%assertion%') . ' for %label% %path% with value %value%.',
				JcoreBroiler\Nette\Schema\Message::FailedAssertion,
				['value' => $value, 'assertion' => $expected],
			);
		});
	}


	/** Marks as deprecated */
	public function deprecated(string $message = 'The item %path% is deprecated.'): self
	{
		$this->deprecated = $message;
		return $this;
	}


	public function completeDefault(Context $context): mixed
	{
		if ($this->required) {
			$context->addError(
				'The mandatory item %path% is missing.',
				JcoreBroiler\Nette\Schema\Message::MissingItem,
			);
			return null;
		}

		return $this->default;
	}


	public function doNormalize(mixed $value, Context $context): mixed
	{
		if ($this->before) {
			$value = ($this->before)($value);
		}

		return $value;
	}


	private function doDeprecation(Context $context): void
	{
		if ($this->deprecated !== null) {
			$context->addWarning(
				$this->deprecated,
				JcoreBroiler\Nette\Schema\Message::Deprecated,
			);
		}
	}


	private function doTransform(mixed $value, Context $context): mixed
	{
		$isOk = $context->createChecker();
		foreach ($this->transforms as $handler) {
			$value = $handler($value, $context);
			if (!$isOk()) {
				return null;
			}
		}
		return $value;
	}


	/** @deprecated use JcoreBroiler\Nette\Schema\Validators::validateType() */
	private function doValidate(mixed $value, string $expected, Context $context): bool
	{
		$isOk = $context->createChecker();
		Helpers::validateType($value, $expected, $context);
		return $isOk();
	}


	/** @deprecated use JcoreBroiler\Nette\Schema\Validators::validateRange() */
	private static function doValidateRange(mixed $value, array $range, Context $context, string $types = ''): bool
	{
		$isOk = $context->createChecker();
		Helpers::validateRange($value, $range, $context, $types);
		return $isOk();
	}


	/** @deprecated use doTransform() */
	private function doFinalize(mixed $value, Context $context): mixed
	{
		return $this->doTransform($value, $context);
	}
}
