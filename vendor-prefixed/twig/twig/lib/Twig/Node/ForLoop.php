<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\ForLoopNode;

class_exists('JcoreBroiler\Twig\Node\ForLoopNode');

@trigger_error('Using the "JcoreBroiler_Twig_Node_ForLoop" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\ForLoopNode" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\ForLoopNode" instead */
    class JcoreBroiler_Twig_Node_ForLoop extends ForLoopNode
    {
    }
}
