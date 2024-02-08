<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Error\SyntaxError;

class_exists('JcoreBroiler\Twig\Error\SyntaxError');

@trigger_error('Using the "JcoreBroiler_Twig_Error_Syntax" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Error\SyntaxError" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Error\SyntaxError" instead */
    class JcoreBroiler_Twig_Error_Syntax extends SyntaxError
    {
    }
}
