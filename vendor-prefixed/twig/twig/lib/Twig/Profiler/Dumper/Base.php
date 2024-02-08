<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Profiler\Dumper\BaseDumper;

class_exists('JcoreBroiler\Twig\Profiler\Dumper\BaseDumper');

@trigger_error('Using the "JcoreBroiler_Twig_Profiler_Dumper_Base" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Profiler\Dumper\BaseDumper" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Profiler\Dumper\BaseDumper" instead */
    class JcoreBroiler_Twig_Profiler_Dumper_Base extends BaseDumper
    {
    }
}
