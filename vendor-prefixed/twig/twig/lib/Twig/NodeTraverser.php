<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\NodeTraverser;

class_exists('JcoreBroiler\Twig\NodeTraverser');

@trigger_error('Using the "JcoreBroiler_Twig_NodeTraverser" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\NodeTraverser" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\NodeTraverser" instead */
    class JcoreBroiler_Twig_NodeTraverser extends NodeTraverser
    {
    }
}
