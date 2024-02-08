<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Profiler\Dumper\BlackfireDumper;

class_exists('JcoreBroiler\Twig\Profiler\Dumper\BlackfireDumper');

@trigger_error('Using the "JcoreBroiler_Twig_Profiler_Dumper_Blackfire" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Profiler\Dumper\BlackfireDumper" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Profiler\Dumper\BlackfireDumper" instead */
    class JcoreBroiler_Twig_Profiler_Dumper_Blackfire extends BlackfireDumper
    {
    }
}
