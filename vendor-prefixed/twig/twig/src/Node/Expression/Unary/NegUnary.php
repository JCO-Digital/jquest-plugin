<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Twig\Node\Expression\Unary;

use JcoreBroiler\Twig\Compiler;

class NegUnary extends AbstractUnary
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('-');
    }
}

class_alias('JcoreBroiler\Twig\Node\Expression\Unary\NegUnary', 'Twig_Node_Expression_Unary_Neg');
