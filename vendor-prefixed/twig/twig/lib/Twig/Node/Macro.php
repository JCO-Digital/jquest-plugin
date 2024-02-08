<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\MacroNode;

class_exists('JcoreBroiler\Twig\Node\MacroNode');

@trigger_error('Using the "JcoreBroiler_Twig_Node_Macro" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\MacroNode" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\MacroNode" instead */
    class JcoreBroiler_Twig_Node_Macro extends MacroNode
    {
    }
}
