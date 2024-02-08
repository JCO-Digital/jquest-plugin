<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Twig\Extra\Markdown;

use League\HTMLToMarkdown\HtmlConverter;
use JcoreBroiler\Twig\Extension\AbstractExtension;
use JcoreBroiler\Twig\TwigFilter;

final class MarkdownExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('markdown_to_html', ['JcoreBroiler\Twig\\Extra\\Markdown\\MarkdownRuntime', 'convert'], ['is_safe' => ['all']]),
            new TwigFilter('html_to_markdown', 'JcoreBroiler\Twig\\Extra\\Markdown\\twig_html_to_markdown', ['is_safe' => ['all']]),
        ];
    }
}

function twig_html_to_markdown(string $body, array $options = []): string
{
    static $converters;

    if (!class_exists(HtmlConverter::class)) {
        throw new \LogicException('You cannot use the "html_to_markdown" filter as league/html-to-markdown is not installed; try running "composer require league/html-to-markdown".');
    }

    $options = $options + [
        'hard_break' => true,
        'strip_tags' => true,
        'remove_nodes' => 'head style',
    ];

    if (!isset($converters[$key = serialize($options)])) {
        $converters[$key] = new HtmlConverter($options);
    }

    return $converters[$key]->convert($body);
}
