<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\TokenParser\BlockTokenParser;

class_exists('JcoreBroiler\Twig\TokenParser\BlockTokenParser');

@trigger_error('Using the "JcoreBroiler_Twig_TokenParser_Block" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\TokenParser\BlockTokenParser" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\TokenParser\BlockTokenParser" instead */
    class JcoreBroiler_Twig_TokenParser_Block extends BlockTokenParser
    {
    }
}
