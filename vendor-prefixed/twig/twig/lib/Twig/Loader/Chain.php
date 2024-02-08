<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Loader\ChainLoader;

class_exists('JcoreBroiler\Twig\Loader\ChainLoader');

@trigger_error('Using the "JcoreBroiler_Twig_Loader_Chain" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Loader\ChainLoader" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Loader\ChainLoader" instead */
    class JcoreBroiler_Twig_Loader_Chain extends ChainLoader
    {
    }
}
