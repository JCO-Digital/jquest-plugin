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

use JcoreBroiler\League\CommonMark\CommonMarkConverter;
use Michelf\MarkdownExtra;
use Parsedown;

class DefaultMarkdown implements MarkdownInterface
{
    private $converter;

    public function __construct()
    {
        if (class_exists(CommonMarkConverter::class)) {
            $this->converter = new LeagueMarkdown();
        } elseif (class_exists(MarkdownExtra::class)) {
            $this->converter = new MichelfMarkdown();
        } elseif (class_exists(Parsedown::class)) {
            $this->converter = new ErusevMarkdown();
        } else {
            throw new \LogicException('You cannot use the "markdown_to_html" filter as no Markdown library is available; try running "composer require league/commonmark".');
        }
    }

    public function convert(string $body): string
    {
        return $this->converter->convert($body);
    }
}
