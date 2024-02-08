<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JcoreBroiler\League\CommonMark\Extension\SmartPunct;

use JcoreBroiler\League\CommonMark\Node\Inline\AbstractStringContainer;

final class Quote extends AbstractStringContainer
{
    public const DOUBLE_QUOTE        = '"';
    public const DOUBLE_QUOTE_OPENER = '“';
    public const DOUBLE_QUOTE_CLOSER = '”';

    public const SINGLE_QUOTE        = "'";
    public const SINGLE_QUOTE_OPENER = '‘';
    public const SINGLE_QUOTE_CLOSER = '’';
}
