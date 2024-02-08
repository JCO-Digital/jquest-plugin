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

namespace JcoreBroiler\League\CommonMark\Extension\Mention\Generator;

use JcoreBroiler\League\CommonMark\Extension\Mention\Mention;
use JcoreBroiler\League\CommonMark\Node\Inline\AbstractInline;

final class StringTemplateLinkGenerator implements MentionGeneratorInterface
{
    private string $urlTemplate;

    public function __construct(string $urlTemplate)
    {
        $this->urlTemplate = $urlTemplate;
    }

    public function generateMention(Mention $mention): ?AbstractInline
    {
        $mention->setUrl(\sprintf($this->urlTemplate, $mention->getIdentifier()));

        return $mention;
    }
}
