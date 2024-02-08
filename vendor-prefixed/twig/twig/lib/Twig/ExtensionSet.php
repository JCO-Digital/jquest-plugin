<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\ExtensionSet;

class_exists('JcoreBroiler\Twig\ExtensionSet');

@trigger_error('Using the "JcoreBroiler_Twig_ExtensionSet" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\ExtensionSet" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\ExtensionSet" instead */
    class JcoreBroiler_Twig_ExtensionSet extends ExtensionSet
    {
    }
}
