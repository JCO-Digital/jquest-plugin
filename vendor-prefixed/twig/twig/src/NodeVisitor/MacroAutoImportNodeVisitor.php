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

namespace JcoreBroiler\Twig\NodeVisitor;

use JcoreBroiler\Twig\Environment;
use JcoreBroiler\Twig\Node\Expression\AssignNameExpression;
use JcoreBroiler\Twig\Node\Expression\ConstantExpression;
use JcoreBroiler\Twig\Node\Expression\GetAttrExpression;
use JcoreBroiler\Twig\Node\Expression\MethodCallExpression;
use JcoreBroiler\Twig\Node\Expression\NameExpression;
use JcoreBroiler\Twig\Node\ImportNode;
use JcoreBroiler\Twig\Node\ModuleNode;
use JcoreBroiler\Twig\Node\Node;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MacroAutoImportNodeVisitor implements NodeVisitorInterface
{
    private $inAModule = false;
    private $hasMacroCalls = false;

    public function enterNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {
            $this->inAModule = true;
            $this->hasMacroCalls = false;
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {
            $this->inAModule = false;
            if ($this->hasMacroCalls) {
                $node->getNode('constructor_end')->setNode('_auto_macro_import', new ImportNode(new NameExpression('_self', 0), new AssignNameExpression('_self', 0), 0, 'import', true));
            }
        } elseif ($this->inAModule) {
            if (
                $node instanceof GetAttrExpression &&
                $node->getNode('node') instanceof NameExpression &&
                '_self' === $node->getNode('node')->getAttribute('name') &&
                $node->getNode('attribute') instanceof ConstantExpression
            ) {
                $this->hasMacroCalls = true;

                $name = $node->getNode('attribute')->getAttribute('value');
                $node = new MethodCallExpression($node->getNode('node'), 'macro_'.$name, $node->getNode('arguments'), $node->getTemplateLine());
                $node->setAttribute('safe', true);
            }
        }

        return $node;
    }

    public function getPriority()
    {
        // we must be ran before auto-escaping
        return -10;
    }
}
