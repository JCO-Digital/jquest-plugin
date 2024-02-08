<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace JcoreBroiler\League\CommonMark\Extension\DescriptionList\Parser;

use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\Description;
use JcoreBroiler\League\CommonMark\Node\Block\AbstractBlock;
use JcoreBroiler\League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinue;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinueParserInterface;
use JcoreBroiler\League\CommonMark\Parser\Cursor;

final class DescriptionContinueParser extends AbstractBlockContinueParser
{
    private Description $block;

    private int $indentation;

    public function __construct(bool $tight, int $indentation)
    {
        $this->block       = new Description($tight);
        $this->indentation = $indentation;
    }

    public function getBlock(): Description
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->isBlank()) {
            if ($this->block->firstChild() === null) {
                // Blank line after empty item
                return BlockContinue::none();
            }

            $cursor->advanceToNextNonSpaceOrTab();

            return BlockContinue::at($cursor);
        }

        if ($cursor->getIndent() >= $this->indentation) {
            $cursor->advanceBy($this->indentation, true);

            return BlockContinue::at($cursor);
        }

        return BlockContinue::none();
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }
}
