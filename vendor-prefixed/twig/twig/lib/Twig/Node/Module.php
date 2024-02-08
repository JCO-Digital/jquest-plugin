<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\ModuleNode;

class_exists('JcoreBroiler\Twig\Node\ModuleNode');

@trigger_error('Using the "JcoreBroiler_Twig_Node_Module" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\ModuleNode" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\ModuleNode" instead */
    class JcoreBroiler_Twig_Node_Module extends ModuleNode
    {
    }
}
