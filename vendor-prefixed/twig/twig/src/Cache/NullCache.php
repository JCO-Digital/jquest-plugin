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

namespace JcoreBroiler\Twig\Cache;

/**
 * Implements a no-cache strategy.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class NullCache implements CacheInterface
{
    public function generateKey($name, $className)
    {
        return '';
    }

    public function write($key, $content)
    {
    }

    public function load($key)
    {
    }

    public function getTimestamp($key)
    {
        return 0;
    }
}

class_alias('JcoreBroiler\Twig\Cache\NullCache', 'Twig_Cache_Null');
