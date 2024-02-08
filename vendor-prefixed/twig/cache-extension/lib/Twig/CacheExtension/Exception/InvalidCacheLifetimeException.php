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

namespace JcoreBroiler\Twig\CacheExtension\Exception;

class InvalidCacheLifetimeException extends BaseException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($lifetime, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Value "%s" is not a valid lifetime.', gettype($lifetime)), $code, $previous);
    }
}
