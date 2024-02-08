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

namespace JcoreBroiler\League\CommonMark\Parser\Block;

use JcoreBroiler\League\CommonMark\Node\Block\Paragraph;
use JcoreBroiler\League\CommonMark\Parser\Cursor;
use JcoreBroiler\League\CommonMark\Parser\InlineParserEngineInterface;
use JcoreBroiler\League\CommonMark\Reference\ReferenceInterface;
use JcoreBroiler\League\CommonMark\Reference\ReferenceParser;

final class ParagraphParser extends AbstractBlockContinueParser implements BlockContinueParserWithInlinesInterface
{
    /** @psalm-readonly */
    private Paragraph $block;

    /** @psalm-readonly */
    private ReferenceParser $referenceParser;

    public function __construct()
    {
        $this->block           = new Paragraph();
        $this->referenceParser = new ReferenceParser();
    }

    public function canHaveLazyContinuationLines(): bool
    {
        return true;
    }

    public function getBlock(): Paragraph
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->isBlank()) {
            return BlockContinue::none();
        }

        return BlockContinue::at($cursor);
    }

    public function addLine(string $line): void
    {
        $this->referenceParser->parse($line);
    }

    public function closeBlock(): void
    {
        if ($this->referenceParser->hasReferences() && $this->referenceParser->getParagraphContent() === '') {
            $this->block->detach();
        }
    }

    public function parseInlines(InlineParserEngineInterface $inlineParser): void
    {
        $content = $this->getContentString();
        if ($content !== '') {
            $inlineParser->parse($content, $this->block);
        }
    }

    public function getContentString(): string
    {
        return $this->referenceParser->getParagraphContent();
    }

    /**
     * @return ReferenceInterface[]
     */
    public function getReferences(): iterable
    {
        return $this->referenceParser->getReferences();
    }
}
