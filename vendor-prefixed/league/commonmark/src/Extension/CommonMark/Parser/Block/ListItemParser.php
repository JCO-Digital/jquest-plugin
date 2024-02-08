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
use JcoreBroiler\League\CommonMark\Node\Block\Paragraph;
use JcoreBroiler\League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinue;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinueParserInterface;
use JcoreBroiler\League\CommonMark\Parser\Cursor;

final class ListItemParser extends AbstractBlockContinueParser
{
    /** @psalm-readonly */
    private ListItem $block;

    private bool $hadBlankLine = false;

    public function __construct(ListData $listData)
    {
        $this->block = new ListItem($listData);
    }

    public function getBlock(): ListItem
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        if ($this->hadBlankLine) {
            // We saw a blank line in this list item, that means the list block is loose.
            //
            // spec: if any of its constituent list items directly contain two block-level elements with a blank line
            // between them
            $parent = $this->block->parent();
            if ($parent instanceof ListBlock) {
                $parent->setTight(false);
            }
        }

        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->isBlank()) {
            if ($this->block->firstChild() === null) {
                // Blank line after empty list item
                return BlockContinue::none();
            }

            $activeBlock = $activeBlockParser->getBlock();
            // If the active block is a code block, blank lines in it should not affect if the list is tight.
            $this->hadBlankLine = $activeBlock instanceof Paragraph || $activeBlock instanceof ListItem;
            $cursor->advanceToNextNonSpaceOrTab();

            return BlockContinue::at($cursor);
        }

        $contentIndent = $this->block->getListData()->markerOffset + $this->getBlock()->getListData()->padding;
        if ($cursor->getIndent() >= $contentIndent) {
            $cursor->advanceBy($contentIndent, true);

            return BlockContinue::at($cursor);
        }

        // Note: We'll hit this case for lazy continuation lines, they will get added later.
        return BlockContinue::none();
    }
}
