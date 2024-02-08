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
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JcoreBroiler\League\CommonMark\Node\Block;

use JcoreBroiler\League\CommonMark\Parser\Cursor;
use JcoreBroiler\League\CommonMark\Reference\ReferenceMap;
use JcoreBroiler\League\CommonMark\Reference\ReferenceMapInterface;

class Document extends AbstractBlock
{
    /** @psalm-readonly */
    protected ReferenceMapInterface $referenceMap;

    public function __construct(?ReferenceMapInterface $referenceMap = null)
    {
        parent::__construct();

        $this->setStartLine(1);

        $this->referenceMap = $referenceMap ?? new ReferenceMap();
    }

    public function getReferenceMap(): ReferenceMapInterface
    {
        return $this->referenceMap;
    }

    public function canContain(AbstractBlock $block): bool
    {
        return true;
    }

    public function isCode(): bool
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor): bool
    {
        return true;
    }
}
