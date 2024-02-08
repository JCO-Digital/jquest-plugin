<?php

/*
 * This file is part of twig-cache-extension.
 *
 * (c) Alexander <iam.asm89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Twig\CacheExtension\CacheStrategy;

/**
 * Generates a key for a given value.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
interface KeyGeneratorInterface
{
    /**
     * Generate a cache key for a given value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function generateKey($value);
}
