<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Sandbox\SecurityPolicyInterface;

class_exists('JcoreBroiler\Twig\Sandbox\SecurityPolicyInterface');

@trigger_error('Using the "JcoreBroiler_Twig_Sandbox_SecurityPolicyInterface" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityPolicyInterface" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Sandbox\SecurityPolicyInterface" instead */
    class JcoreBroiler_Twig_Sandbox_SecurityPolicyInterface extends SecurityPolicyInterface
    {
    }
}
