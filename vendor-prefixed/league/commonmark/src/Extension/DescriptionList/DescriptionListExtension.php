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

namespace JcoreBroiler\League\CommonMark\Extension\DescriptionList;

use JcoreBroiler\League\CommonMark\Environment\EnvironmentBuilderInterface;
use JcoreBroiler\League\CommonMark\Event\DocumentParsedEvent;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Event\ConsecutiveDescriptionListMerger;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Event\LooseDescriptionHandler;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\Description;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\DescriptionList;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Node\DescriptionTerm;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Parser\DescriptionStartParser;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Renderer\DescriptionListRenderer;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Renderer\DescriptionRenderer;
use JcoreBroiler\League\CommonMark\Extension\DescriptionList\Renderer\DescriptionTermRenderer;
use JcoreBroiler\League\CommonMark\Extension\ExtensionInterface;

final class DescriptionListExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addBlockStartParser(new DescriptionStartParser());

        $environment->addEventListener(DocumentParsedEvent::class, new LooseDescriptionHandler(), 1001);
        $environment->addEventListener(DocumentParsedEvent::class, new ConsecutiveDescriptionListMerger(), 1000);

        $environment->addRenderer(DescriptionList::class, new DescriptionListRenderer());
        $environment->addRenderer(DescriptionTerm::class, new DescriptionTermRenderer());
        $environment->addRenderer(Description::class, new DescriptionRenderer());
    }
}
