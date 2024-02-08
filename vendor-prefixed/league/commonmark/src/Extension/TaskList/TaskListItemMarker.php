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

namespace JcoreBroiler\League\CommonMark\Extension\TaskList;

use JcoreBroiler\League\CommonMark\Node\Inline\AbstractInline;

final class TaskListItemMarker extends AbstractInline
{
    /** @psalm-readonly-allow-private-mutation */
    private bool $checked;

    public function __construct(bool $isCompleted)
    {
        parent::__construct();

        $this->checked = $isCompleted;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): void
    {
        $this->checked = $checked;
    }
}
