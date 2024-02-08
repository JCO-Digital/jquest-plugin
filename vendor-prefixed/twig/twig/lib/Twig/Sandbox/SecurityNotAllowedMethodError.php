<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Sandbox\SecurityNotAllowedMethodError;

class_exists('JcoreBroiler\Twig\Sandbox\SecurityNotAllowedMethodError');

@trigger_error('Using the "JcoreBroiler_Twig_Sandbox_SecurityNotAllowedMethodError" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityNotAllowedMethodError" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityNotAllowedMethodError" instead */
    class JcoreBroiler_Twig_Sandbox_SecurityNotAllowedMethodError extends SecurityNotAllowedMethodError
    {
    }
}
