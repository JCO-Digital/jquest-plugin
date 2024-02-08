<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Twig\Node;

use JcoreBroiler\Twig\Compiler;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CheckSecurityCallNode extends Node
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write("\$this->sandbox = \$this->env->getExtension('\JcoreBroiler\Twig\Extension\SandboxExtension');\n")
            ->write("\$this->checkSecurity();\n")
        ;
    }
}
