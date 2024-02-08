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

namespace JcoreBroiler\League\CommonMark\Extension\FrontMatter;

use JcoreBroiler\League\CommonMark\Environment\EnvironmentBuilderInterface;
use JcoreBroiler\League\CommonMark\Event\DocumentPreParsedEvent;
use JcoreBroiler\League\CommonMark\Event\DocumentRenderedEvent;
use JcoreBroiler\League\CommonMark\Extension\ExtensionInterface;
use JcoreBroiler\League\CommonMark\Extension\FrontMatter\Data\FrontMatterDataParserInterface;
use JcoreBroiler\League\CommonMark\Extension\FrontMatter\Data\LibYamlFrontMatterParser;
use JcoreBroiler\League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use JcoreBroiler\League\CommonMark\Extension\FrontMatter\Listener\FrontMatterPostRenderListener;
use JcoreBroiler\League\CommonMark\Extension\FrontMatter\Listener\FrontMatterPreParser;

final class FrontMatterExtension implements ExtensionInterface
{
    /** @psalm-readonly */
    private FrontMatterParserInterface $frontMatterParser;

    public function __construct(?FrontMatterDataParserInterface $dataParser = null)
    {
        $this->frontMatterParser = new FrontMatterParser($dataParser ?? LibYamlFrontMatterParser::capable() ?? new SymfonyYamlFrontMatterParser());
    }

    public function getFrontMatterParser(): FrontMatterParserInterface
    {
        return $this->frontMatterParser;
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentPreParsedEvent::class, new FrontMatterPreParser($this->frontMatterParser));
        $environment->addEventListener(DocumentRenderedEvent::class, new FrontMatterPostRenderListener(), -500);
    }
}
