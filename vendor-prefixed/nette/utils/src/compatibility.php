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

if (false) {
	/** @deprecated use JcoreBroiler\Nette\HtmlStringable */
	interface IHtmlString extends JcoreBroiler\Nette\HtmlStringable
	{
	}
} elseif (!interface_exists(IHtmlString::class)) {
	class_alias(JcoreBroiler\Nette\HtmlStringable::class, IHtmlString::class);
}

namespace JcoreBroiler\Nette\Localization;

if (false) {
	/** @deprecated use JcoreBroiler\Nette\Localization\Translator */
	interface ITranslator extends Translator
	{
	}
} elseif (!interface_exists(ITranslator::class)) {
	class_alias(Translator::class, ITranslator::class);
}
