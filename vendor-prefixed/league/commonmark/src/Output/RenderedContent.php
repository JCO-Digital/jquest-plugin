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

namespace JcoreBroiler\League\CommonMark\Output;

use JcoreBroiler\League\CommonMark\Node\Block\Document;

class RenderedContent implements RenderedContentInterface, \JcoreBroiler_StringableStringable;

    /** @psalm-readonly */
    private string $content;

    public function __construct(Document $document, string $content)
    {
        $this->document = $document;
        $this->content  = $content;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @psalm-mutation-free
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
