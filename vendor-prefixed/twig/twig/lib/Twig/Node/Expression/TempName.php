<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\Expression\TempNameExpression;

class_exists('JcoreBroiler\Twig\Node\Expression\TempNameExpression');

@trigger_error('Using the "JcoreBroiler_Twig_Node_Expression_TempName" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\Expression\TempNameExpression" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\Expression\TempNameExpression" instead */
    class JcoreBroiler_Twig_Node_Expression_TempName extends TempNameExpression
    {
    }
}
