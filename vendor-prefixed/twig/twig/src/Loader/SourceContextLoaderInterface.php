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

namespace JcoreBroiler\Twig\Loader;

/**
 * Empty interface for Twig 1.x compatibility.
 */
interface SourceContextLoaderInterface extends LoaderInterface
{
}

class_alias('JcoreBroiler\Twig\Loader\SourceContextLoaderInterface', 'Twig_SourceContextLoaderInterface');
