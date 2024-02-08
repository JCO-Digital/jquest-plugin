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

namespace JcoreBroiler\Nette\Utils;

use JcoreBroiler\Nette;


/**
 * Provides the base class for a generic list (items can be accessed by index).
 * @template T
 * @implements \IteratorAggregate<int, T>
 * @implements \ArrayAccess<int, T>
 */
class ArrayList implements \ArrayAccess, \Countable, \IteratorAggregate
{
	use JcoreBroiler\Nette\SmartObject;

	private array $list = [];


	/**
	 * Transforms array to ArrayList.
	 * @param  list<T>  $array
	 */
	public static function from(array $array): static
	{
		if (!Arrays::isList($array)) {
			throw new \JcoreBroiler\Nette\InvalidArgumentException('Array is not valid list.');
		}

		$obj = new static;
		$obj->list = $array;
		return $obj;
	}


	/**
	 * Returns an iterator over all items.
	 * @return \Iterator<int, T>
	 */
	public function &getIterator(): \Iterator
	{
		foreach ($this->list as &$item) {
			yield $item;
		}
	}


	/**
	 * Returns items count.
	 */
	public function count(): int
	{
		return count($this->list);
	}


	/**
	 * Replaces or appends a item.
	 * @param  int|null  $index
	 * @param  T  $value
	 * @throws JcoreBroiler\Nette\OutOfRangeException
	 */
	public function offsetSet($index, $value): void
	{
		if ($index === null) {
			$this->list[] = $value;

		} elseif (!is_int($index) || $index < 0 || $index >= count($this->list)) {
			throw new \JcoreBroiler\Nette\OutOfRangeException('Offset invalid or out of range');

		} else {
			$this->list[$index] = $value;
		}
	}


	/**
	 * Returns a item.
	 * @param  int  $index
	 * @return T
	 * @throws JcoreBroiler\Nette\OutOfRangeException
	 */
	public function offsetGet($index): mixed
	{
		if (!is_int($index) || $index < 0 || $index >= count($this->list)) {
			throw new \JcoreBroiler\Nette\OutOfRangeException('Offset invalid or out of range');
		}

		return $this->list[$index];
	}


	/**
	 * Determines whether a item exists.
	 * @param  int  $index
	 */
	public function offsetExists($index): bool
	{
		return is_int($index) && $index >= 0 && $index < count($this->list);
	}


	/**
	 * Removes the element at the specified position in this list.
	 * @param  int  $index
	 * @throws JcoreBroiler\Nette\OutOfRangeException
	 */
	public function offsetUnset($index): void
	{
		if (!is_int($index) || $index < 0 || $index >= count($this->list)) {
			throw new \JcoreBroiler\Nette\OutOfRangeException('Offset invalid or out of range');
		}

		array_splice($this->list, $index, 1);
	}


	/**
	 * Prepends a item.
	 * @param  T  $value
	 */
	public function prepend(mixed $value): void
	{
		$first = array_slice($this->list, 0, 1);
		$this->offsetSet(0, $value);
		array_splice($this->list, 1, 0, $first);
	}
}
