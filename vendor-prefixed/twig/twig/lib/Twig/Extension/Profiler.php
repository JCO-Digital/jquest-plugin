<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Extension\ProfilerExtension;

class_exists('JcoreBroiler\Twig\Extension\ProfilerExtension');

@trigger_error('Using the "JcoreBroiler_Twig_Extension_Profiler" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Extension\ProfilerExtension" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Extension\ProfilerExtension" instead */
    class JcoreBroiler_Twig_Extension_Profiler extends ProfilerExtension
    {
    }
}
