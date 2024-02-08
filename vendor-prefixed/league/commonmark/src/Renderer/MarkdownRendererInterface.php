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

namespace JcoreBroiler\League\CommonMark\Renderer;

use JcoreBroiler\League\CommonMark\Node\Block\Document;
use JcoreBroiler\League\CommonMark\Output\RenderedContentInterface;

/**
 * Renders a parsed Document AST
 *
 * @deprecated since 2.3; use {@link DocumentRendererInterface} instead
 */
interface MarkdownRendererInterface
{
    /**
     * Render the given Document node (and all of its children)
     */
    public function renderDocument(Document $document): RenderedContentInterface;
}
