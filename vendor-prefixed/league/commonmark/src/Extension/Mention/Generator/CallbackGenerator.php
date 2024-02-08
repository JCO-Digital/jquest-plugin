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

use JcoreBroiler\League\CommonMark\Exception\LogicException;
use JcoreBroiler\League\CommonMark\Extension\Mention\Mention;
use JcoreBroiler\League\CommonMark\Node\Inline\AbstractInline;

final class CallbackGenerator implements MentionGeneratorInterface
{
    /**
     * A callback function which sets the URL on the passed mention and returns the mention, return a new AbstractInline based object or null if the mention is not a match
     *
     * @var callable(Mention): ?AbstractInline
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @throws LogicException
     */
    public function generateMention(Mention $mention): ?AbstractInline
    {
        $result = \call_user_func($this->callback, $mention);
        if ($result === null) {
            return null;
        }

        if ($result instanceof AbstractInline && ! ($result instanceof Mention)) {
            return $result;
        }

        if ($result instanceof Mention && $result->hasUrl()) {
            return $mention;
        }

        throw new LogicException('CallbackGenerator callable must set the URL on the passed mention and return the mention, return a new AbstractInline based object or null if the mention is not a match');
    }
}
