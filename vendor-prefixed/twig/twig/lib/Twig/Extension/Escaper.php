<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Extension\EscaperExtension;

class_exists('JcoreBroiler\Twig\Extension\EscaperExtension');

@trigger_error('Using the "JcoreBroiler_Twig_Extension_Escaper" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Extension\EscaperExtension" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Extension\EscaperExtension" instead */
    class JcoreBroiler_Twig_Extension_Escaper extends EscaperExtension
    {
    }
}
