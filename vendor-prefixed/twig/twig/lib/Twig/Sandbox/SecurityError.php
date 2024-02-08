<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Sandbox\SecurityError;

class_exists('JcoreBroiler\Twig\Sandbox\SecurityError');

@trigger_error('Using the "JcoreBroiler_Twig_Sandbox_SecurityError" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityError" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityError" instead */
    class JcoreBroiler_Twig_Sandbox_SecurityError extends SecurityError
    {
    }
}
