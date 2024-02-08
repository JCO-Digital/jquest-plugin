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
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\DescriptionList;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\DescriptionTerm;
use JcoreBroiler\League\CommonMark\Node\Block\AbstractBlock;
use JcoreBroiler\League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinue;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockContinueParserInterface;
use JcoreBroiler\League\CommonMark\Parser\Cursor;

final class DescriptionListContinueParser extends AbstractBlockContinueParser
{
    private DescriptionList $block;

    public function __construct()
    {
        $this->block = new DescriptionList();
    }

    public function getBlock(): DescriptionList
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        return BlockContinue::at($cursor);
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return $childBlock instanceof DescriptionTerm || $childBlock instanceof Description;
    }
}
