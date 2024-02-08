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

namespace JcoreBroiler\League\CommonMark\Renderer\Block;

use JcoreBroiler\League\CommonMark\Node\Block\Paragraph;
use JcoreBroiler\League\CommonMark\Node\Block\TightBlockInterface;
use JcoreBroiler\League\CommonMark\Node\Node;
use JcoreBroiler\League\CommonMark\Renderer\ChildNodeRendererInterface;
use JcoreBroiler\League\CommonMark\Renderer\NodeRendererInterface;
use JcoreBroiler\League\CommonMark\Util\HtmlElement;
use JcoreBroiler\League\CommonMark\Xml\XmlNodeRendererInterface;

final class ParagraphRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param Paragraph $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        Paragraph::assertInstanceOf($node);

        if ($this->inTightList($node)) {
            return $childRenderer->renderNodes($node->children());
        }

        $attrs = $node->data->get('attributes');

        return new HtmlElement('p', $attrs, $childRenderer->renderNodes($node->children()));
    }

    public function getXmlTagName(Node $node): string
    {
        return 'paragraph';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }

    private function inTightList(Paragraph $node): bool
    {
        // Only check up to two (2) levels above this for tightness
        $i = 2;
        while (($node = $node->parent()) && $i--) {
            if ($node instanceof TightBlockInterface) {
                return $node->isTight();
            }
        }

        return false;
    }
}
