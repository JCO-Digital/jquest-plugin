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

use JcoreBroiler\Twig\TwigFunction;

/*
 * For Twig 1.x compatibility.
 */
class_exists(TwigFunction::class);

@trigger_error('Using the "JcoreBroiler_Twig_SimpleFunction" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\TwigFunction" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\TwigFunction" instead */
    final class JcoreBroiler_Twig_SimpleFunction extends TwigFunction
    {
    }
}
