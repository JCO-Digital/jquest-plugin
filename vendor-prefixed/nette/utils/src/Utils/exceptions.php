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
 * The exception that is thrown when an image error occurs.
 */
class ImageException extends \Exception
{
}


/**
 * The exception that indicates invalid image file.
 */
class UnknownImageFileException extends ImageException
{
}


/**
 * The exception that indicates error of JSON encoding/decoding.
 */
class JsonException extends \JsonException
{
}


/**
 * The exception that indicates error of the last Regexp execution.
 */
class RegexpException extends \Exception
{
}


/**
 * The exception that indicates assertion error.
 */
class AssertionException extends \Exception
{
}
