<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\DeprecatedNode;

class_exists('JcoreBroiler\Twig\Node\DeprecatedNode');

@trigger_error('Using the "JcoreBroiler_Twig_Node_Deprecated" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\DeprecatedNode" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\DeprecatedNode" instead */
    class JcoreBroiler_Twig_Node_Deprecated extends DeprecatedNode
    {
    }
}
