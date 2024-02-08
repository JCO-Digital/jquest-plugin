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

namespace JcoreBroiler\Twig\CacheExtension;

/**
 * Cache provider interface.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
interface CacheProviderInterface
{
    /**
     * @param string $key
     *
     * @return mixed False, if there was no value to be fetched. Null or a string otherwise.
     */
    public function fetch($key);

    /**
     * @param string  $key
     * @param string  $value
     * @param integer $lifetime
     *
     * @return boolean
     */
    public function save($key, $value, $lifetime = 0);
}
