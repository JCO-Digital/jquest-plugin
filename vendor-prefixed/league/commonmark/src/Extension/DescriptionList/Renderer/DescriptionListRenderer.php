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

namespace JcoreBroiler\League\CommonMark\Extension\DescriptionList\Renderer;

use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\DescriptionList;
use JcoreBroiler\League\CommonMark\Node\Node;
use JcoreBroiler\League\CommonMark\Renderer\ChildNodeRendererInterface;
use JcoreBroiler\League\CommonMark\Renderer\NodeRendererInterface;
use JcoreBroiler\League\CommonMark\Util\HtmlElement;

final class DescriptionListRenderer implements NodeRendererInterface
{
    /**
     * @param DescriptionList $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        DescriptionList::assertInstanceOf($node);

        $separator = $childRenderer->getBlockSeparator();

        return new HtmlElement('dl', [], $separator . $childRenderer->renderNodes($node->children()) . $separator);
    }
}
