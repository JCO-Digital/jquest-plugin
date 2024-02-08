<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\TokenStream;

class_exists('JcoreBroiler\Twig\TokenStream');

@trigger_error('Using the "JcoreBroiler_Twig_TokenStream" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\TokenStream" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\TokenStream" instead */
    class JcoreBroiler_Twig_TokenStream extends TokenStream
    {
    }
}
