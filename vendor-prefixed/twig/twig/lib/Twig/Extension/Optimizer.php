<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Extension\OptimizerExtension;

class_exists('JcoreBroiler\Twig\Extension\OptimizerExtension');

@trigger_error('Using the "JcoreBroiler_Twig_Extension_Optimizer" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Extension\OptimizerExtension" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Extension\OptimizerExtension" instead */
    class JcoreBroiler_Twig_Extension_Optimizer extends OptimizerExtension
    {
    }
}
