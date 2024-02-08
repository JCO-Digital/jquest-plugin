<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Profiler\Node\LeaveProfileNode;

class_exists('JcoreBroiler\Twig\Profiler\Node\LeaveProfileNode');

@trigger_error('Using the "JcoreBroiler_Twig_Profiler_Node_LeaveProfile" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Profiler\Node\LeaveProfileNode" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Profiler\Node\LeaveProfileNode" instead */
    class JcoreBroiler_Twig_Profiler_Node_LeaveProfile extends LeaveProfileNode
    {
    }
}
