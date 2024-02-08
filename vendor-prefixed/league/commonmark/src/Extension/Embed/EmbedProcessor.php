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

namespace JcoreBroiler\League\CommonMark\Extension\Embed;

use JcoreBroiler\League\CommonMark\Event\DocumentParsedEvent;
use JcoreBroiler\League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use JcoreBroiler\League\CommonMark\Node\Block\Paragraph;
use JcoreBroiler\League\CommonMark\Node\Inline\Text;
use JcoreBroiler\League\CommonMark\Node\NodeIterator;

final class EmbedProcessor
{
    public const FALLBACK_REMOVE = 'remove';
    public const FALLBACK_LINK   = 'link';

    private EmbedAdapterInterface $adapter;
    private string $fallback;

    public function __construct(EmbedAdapterInterface $adapter, string $fallback = self::FALLBACK_REMOVE)
    {
        $this->adapter  = $adapter;
        $this->fallback = $fallback;
    }

    public function __invoke(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $embeds   = [];
        foreach (new NodeIterator($document) as $node) {
            if (! ($node instanceof Embed)) {
                continue;
            }

            if ($node->parent() !== $document) {
                $replacement = new Paragraph();
                $replacement->appendChild(new Text($node->getUrl()));
                $node->replaceWith($replacement);
            } else {
                $embeds[] = $node;
            }
        }

        $this->adapter->updateEmbeds($embeds);

        foreach ($embeds as $embed) {
            if ($embed->getEmbedCode() !== null) {
                continue;
            }

            if ($this->fallback === self::FALLBACK_REMOVE) {
                $embed->detach();
            } elseif ($this->fallback === self::FALLBACK_LINK) {
                $paragraph = new Paragraph();
                $paragraph->appendChild(new Link($embed->getUrl(), $embed->getUrl()));
                $embed->replaceWith($paragraph);
            }
        }
    }
}
