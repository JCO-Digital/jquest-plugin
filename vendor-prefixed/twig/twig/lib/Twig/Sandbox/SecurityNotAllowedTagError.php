<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Sandbox\SecurityNotAllowedTagError;

class_exists('JcoreBroiler\Twig\Sandbox\SecurityNotAllowedTagError');

@trigger_error('Using the "JcoreBroiler_Twig_Sandbox_SecurityNotAllowedTagError" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityNotAllowedTagError" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityNotAllowedTagError" instead */
    class JcoreBroiler_Twig_Sandbox_SecurityNotAllowedTagError extends SecurityNotAllowedTagError
    {
    }
}
