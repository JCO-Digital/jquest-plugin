<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Util\DeprecationCollector;

class_exists('JcoreBroiler\Twig\Util\DeprecationCollector');

@trigger_error('Using the "JcoreBroiler_Twig_Util_DeprecationCollector" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Util\DeprecationCollector" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Util\DeprecationCollector" instead */
    class JcoreBroiler_Twig_Util_DeprecationCollector extends DeprecationCollector
    {
    }
}
