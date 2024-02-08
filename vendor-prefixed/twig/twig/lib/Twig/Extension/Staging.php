<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Extension\StagingExtension;

class_exists('JcoreBroiler\Twig\Extension\StagingExtension');

@trigger_error('Using the "JcoreBroiler_Twig_Extension_Staging" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Extension\StagingExtension" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Extension\StagingExtension" instead */
    class JcoreBroiler_Twig_Extension_Staging extends StagingExtension
    {
    }
}
