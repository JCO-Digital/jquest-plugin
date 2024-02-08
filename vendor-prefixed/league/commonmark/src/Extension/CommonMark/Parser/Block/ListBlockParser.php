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

namespace JcoreBroiler\League\CommonMark\Extension\CommonMark\Parser\Block;

use JcoreBroiler\League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use JcoreBroiler\League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use JcoreBroiler\League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use JcoreBroiler\League\CommonMark\Node\Block\AbstractBlock;
use JcoreBroiler\League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinue;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinueParserInterface;
use JcoreBroiler\League\CommonMark\Parser\Cursor;

final class ListBlockParser extends AbstractBlockContinueParser
{
    /** @psalm-readonly */
    private ListBlock $block;

    private bool $hadBlankLine = false;

    private int $linesAfterBlank = 0;

    public function __construct(ListData $listData)
    {
        $this->block = new ListBlock($listData);
    }

    public function getBlock(): ListBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        if (! $childBlock instanceof ListItem) {
            return false;
        }

        // Another list item is being added to this list block.
        // If the previous line was blank, that means this list
        // block is "loose" (not tight).
        if ($this->hadBlankLine && $this->linesAfterBlank === 1) {
            $this->block->setTight(false);
            $this->hadBlankLine = false;
        }

        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->isBlank()) {
            $this->hadBlankLine    = true;
            $this->linesAfterBlank = 0;
        } elseif ($this->hadBlankLine) {
            $this->linesAfterBlank++;
        }

        // List blocks themselves don't have any markers, only list items. So try to stay in the list.
        // If there is a block start other than list item, canContain makes sure that this list is closed.
        return BlockContinue::at($cursor);
    }
}
