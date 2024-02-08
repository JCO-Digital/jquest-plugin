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

use JcoreBroiler\Twig\Environment;
use JcoreBroiler\Twig\Extension\AbstractExtension;

/**
 * Extension for caching template blocks with twig.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class Extension extends AbstractExtension
{
    private $cacheStrategy;

    /**
     * @param CacheStrategyInterface $cacheStrategy
     */
    public function __construct(CacheStrategyInterface $cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
    }

    /**
     * @return CacheStrategyInterface
     */
    public function getCacheStrategy()
    {
        return $this->cacheStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers()
    {
        return array(
            new TokenParser\Cache(),
        );
    }
}
