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

namespace JcoreBroiler\League\CommonMark\Environment;

use JcoreBroiler\League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use JcoreBroiler\League\CommonMark\Extension\ExtensionInterface;
use JcoreBroiler\League\CommonMark\Node\Node;
use JcoreBroiler\League\CommonMark\Normalizer\TextNormalizerInterface;
use JcoreBroiler\League\CommonMark\Parser\Block\BlockStartParserInterface;
use JcoreBroiler\League\CommonMark\Parser\Inline\InlineParserInterface;
use JcoreBroiler\League\CommonMark\Renderer\NodeRendererInterface;
use JcoreBroiler\League\Config\ConfigurationProviderInterface;
use JcoreBroiler\Psr\EventDispatcher\EventDispatcherInterface;

interface EnvironmentInterface extends ConfigurationProviderInterface, EventDispatcherInterface
{
    /**
     * Get all registered extensions
     *
     * @return ExtensionInterface[]
     */
    public function getExtensions(): iterable;

    /**
     * @return iterable<BlockStartParserInterface>
     */
    public function getBlockStartParsers(): iterable;

    /**
     * @return iterable<InlineParserInterface>
     */
    public function getInlineParsers(): iterable;

    public function getDelimiterProcessors(): DelimiterProcessorCollection;

    /**
     * @psalm-param class-string<Node> $nodeClass
     *
     * @return iterable<NodeRendererInterface>
     */
    public function getRenderersForClass(string $nodeClass): iterable;

    public function getSlugNormalizer(): TextNormalizerInterface;
}
