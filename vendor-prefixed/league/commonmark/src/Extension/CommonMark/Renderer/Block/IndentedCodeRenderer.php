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

namespace JcoreBroiler\League\CommonMark\Extension\CommonMark\Renderer\Block;

use JcoreBroiler\League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use JcoreBroiler\League\CommonMark\Node\Node;
use JcoreBroiler\League\CommonMark\Renderer\ChildNodeRendererInterface;
use JcoreBroiler\League\CommonMark\Renderer\NodeRendererInterface;
use JcoreBroiler\League\CommonMark\Util\HtmlElement;
use JcoreBroiler\League\CommonMark\Util\Xml;
use JcoreBroiler\League\CommonMark\Xml\XmlNodeRendererInterface;

final class IndentedCodeRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param IndentedCode $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \JcoreBroiler_StringableStringable;

        $attrs = $node->data->get('attributes');

        return new HtmlElement(
            'pre',
            [],
            new HtmlElement('code', $attrs, Xml::escape($node->getLiteral()))
        );
    }

    public function getXmlTagName(Node $node): string
    {
        return 'code_block';
    }

    /**
     * @return array<string, scalar>
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
