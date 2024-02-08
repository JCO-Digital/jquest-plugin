<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\TokenParser\SandboxTokenParser;

class_exists('JcoreBroiler\Twig\TokenParser\SandboxTokenParser');

@trigger_error('Using the "JcoreBroiler_Twig_TokenParser_Sandbox" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\TokenParser\SandboxTokenParser" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\TokenParser\SandboxTokenParser" instead */
    class JcoreBroiler_Twig_TokenParser_Sandbox extends SandboxTokenParser
    {
    }
}
