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


/**
 * Type of image file.
 */
/*enum*/ final class ImageType
{
	public const
		JPEG = IMAGETYPE_JPEG,
		PNG = IMAGETYPE_PNG,
		GIF = IMAGETYPE_GIF,
		WEBP = IMAGETYPE_WEBP,
		AVIF = 19, // IMAGETYPE_AVIF,
		BMP = IMAGETYPE_BMP;
}
