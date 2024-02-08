<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\Expression\Binary\AddBinary;

class_exists('JcoreBroiler\Twig\Node\Expression\Binary\AddBinary');

@trigger_error('Using the "JcoreBroiler_Twig_Node_Expression_Binary_Add" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\Expression\Binary\AddBinary" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\Expression\Binary\AddBinary" instead */
    class JcoreBroiler_Twig_Node_Expression_Binary_Add extends AddBinary
    {
    }
}
