<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\TokenParser\ImportTokenParser;

class_exists('JcoreBroiler\Twig\TokenParser\ImportTokenParser');

@trigger_error('Using the "JcoreBroiler_Twig_TokenParser_Import" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\TokenParser\ImportTokenParser" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\TokenParser\ImportTokenParser" instead */
    class JcoreBroiler_Twig_TokenParser_Import extends ImportTokenParser
    {
    }
}
