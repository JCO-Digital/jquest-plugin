<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace JcoreBroiler\League\CommonMark\Extension\Footnote\Renderer;

use JcoreBroiler\League\CommonMark\Extension\Footnote\Node\FootnoteContainer;
use JcoreBroiler\League\CommonMark\Node\Node;
use JcoreBroiler\League\CommonMark\Renderer\ChildNodeRendererInterface;
use JcoreBroiler\League\CommonMark\Renderer\NodeRendererInterface;
use JcoreBroiler\League\CommonMark\Util\HtmlElement;
use JcoreBroiler\League\CommonMark\Xml\XmlNodeRendererInterface;
use JcoreBroiler\League\Config\ConfigurationAwareInterface;
use JcoreBroiler\League\Config\ConfigurationInterface;

final class FootnoteContainerRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    /**
     * @param FootnoteContainer $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \JcoreBroiler_StringableStringable;

        $attrs = $node->data->getData('attributes');

        $attrs->append('class', $this->config->get('footnote/container_class'));
        $attrs->set('role', 'doc-endnotes');

        $contents = new HtmlElement('ol', [], $childRenderer->renderNodes($node->children()));
        if ($this->config->get('footnote/container_add_hr')) {
            $contents = [new HtmlElement('hr', [], null, true), $contents];
        }

        return new HtmlElement('div', $attrs->export(), $contents);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getXmlTagName(Node $node): string
    {
        return 'footnote_container';
    }

    /**
     * @return array<string, scalar>
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
