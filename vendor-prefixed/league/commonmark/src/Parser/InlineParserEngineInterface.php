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
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JcoreBroiler\League\CommonMark\Parser;

use JcoreBroiler\League\CommonMark\Node\Block\AbstractBlock;

/**
 * Parser for inline content (text, links, emphasized text, etc).
 */
interface InlineParserEngineInterface
{
    /**
     * Parse the given contents as inlines and insert them into the given block
     */
    public function parse(string $contents, AbstractBlock $block): void;
}
