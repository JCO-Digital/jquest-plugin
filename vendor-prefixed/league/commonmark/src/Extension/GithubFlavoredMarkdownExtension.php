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

namespace JcoreBroiler\League\CommonMark\Extension;

use JcoreBroiler\League\CommonMark\Environment\EnvironmentBuilderInterface;
use JcoreBroiler\League\CommonMark\Extension\Autolink\AutolinkExtension;
use JcoreBroiler\League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use JcoreBroiler\League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use JcoreBroiler\League\CommonMark\Extension\Table\TableExtension;
use JcoreBroiler\League\CommonMark\Extension\TaskList\TaskListExtension;

final class GithubFlavoredMarkdownExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
    }
}
