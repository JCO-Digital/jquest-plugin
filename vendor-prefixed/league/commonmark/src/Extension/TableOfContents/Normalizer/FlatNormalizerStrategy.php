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

namespace JcoreBroiler\League\CommonMark\Extension\TableOfContents\Normalizer;

use JcoreBroiler\League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use JcoreBroiler\League\CommonMark\Extension\TableOfContents\Node\TableOfContents;

final class FlatNormalizerStrategy implements NormalizerStrategyInterface
{
    /** @psalm-readonly */
    private TableOfContents $toc;

    public function __construct(TableOfContents $toc)
    {
        $this->toc = $toc;
    }

    public function addItem(int $level, ListItem $listItemToAdd): void
    {
        $this->toc->appendChild($listItemToAdd);
    }
}
