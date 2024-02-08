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

class LeagueMarkdown implements MarkdownInterface
{
    private $converter;
    private $legacySupport;

    public function __construct(CommonMarkConverter $converter = null)
    {
        $this->converter = $converter ?: new CommonMarkConverter();
        $this->legacySupport = !method_exists($this->converter, 'convert');
    }

    public function convert(string $body): string
    {
        if ($this->legacySupport) {
            return $this->converter->convertToHtml($body);
        }

        return $this->converter->convert($body);
    }
}
