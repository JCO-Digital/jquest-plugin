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

namespace JcoreBroiler\League\CommonMark\Extension\FrontMatter\Output;

use JcoreBroiler\League\CommonMark\Extension\FrontMatter\FrontMatterProviderInterface;
use JcoreBroiler\League\CommonMark\Node\Block\Document;
use JcoreBroiler\League\CommonMark\Output\RenderedContent;

/**
 * @psalm-immutable
 */
final class RenderedContentWithFrontMatter extends RenderedContent implements FrontMatterProviderInterface
{
    /**
     * @var mixed
     *
     * @psalm-readonly
     */
    private $frontMatter;

    /**
     * @param Document   $document    The parsed Document object
     * @param string     $content     The final HTML
     * @param mixed|null $frontMatter Any parsed front matter
     */
    public function __construct(Document $document, string $content, $frontMatter)
    {
        parent::__construct($document, $content);

        $this->frontMatter = $frontMatter;
    }

    /**
     * {@inheritDoc}
     */
    public function getFrontMatter()
    {
        return $this->frontMatter;
    }
}
