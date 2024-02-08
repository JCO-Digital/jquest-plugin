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
 * Represents the file or directory returned by the Finder.
 * @internal do not create instances directly
 */
final class FileInfo extends \SplFileInfo
{
	private string $relativePath;


	public function __construct(string $file, string $relativePath = '')
	{
		parent::__construct($file);
		$this->setInfoClass(static::class);
		$this->relativePath = $relativePath;
	}


	/**
	 * Returns the relative directory path.
	 */
	public function getRelativePath(): string
	{
		return $this->relativePath;
	}


	/**
	 * Returns the relative path including file name.
	 */
	public function getRelativePathname(): string
	{
		return ($this->relativePath === '' ? '' : $this->relativePath . DIRECTORY_SEPARATOR)
			. $this->getBasename();
	}


	/**
	 * Returns the contents of the file.
	 * @throws JcoreBroiler\Nette\IOException
	 */
	public function read(): string
	{
		return FileSystem::read($this->getPathname());
	}


	/**
	 * Writes the contents to the file.
	 * @throws JcoreBroiler\Nette\IOException
	 */
	public function write(string $content): void
	{
		FileSystem::write($this->getPathname(), $content);
	}
}
