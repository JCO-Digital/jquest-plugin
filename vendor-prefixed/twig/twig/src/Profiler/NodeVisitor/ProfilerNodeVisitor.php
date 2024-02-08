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

namespace JcoreBroiler\Twig\Profiler\NodeVisitor;

use JcoreBroiler\Twig\Environment;
use JcoreBroiler\Twig\Node\BlockNode;
use JcoreBroiler\Twig\Node\BodyNode;
use JcoreBroiler\Twig\Node\MacroNode;
use JcoreBroiler\Twig\Node\ModuleNode;
use JcoreBroiler\Twig\Node\Node;
use JcoreBroiler\Twig\NodeVisitor\AbstractNodeVisitor;
use JcoreBroiler\Twig\Profiler\Node\EnterProfileNode;
use JcoreBroiler\Twig\Profiler\Node\LeaveProfileNode;
use JcoreBroiler\Twig\Profiler\Profile;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ProfilerNodeVisitor extends AbstractNodeVisitor
{
    private $extensionName;
    private $varName;

    public function __construct(string $extensionName)
    {
        $this->extensionName = $extensionName;
        $this->varName = sprintf('__internal_%s', hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $extensionName));
    }

    protected function doEnterNode(Node $node, Environment $env)
    {
        return $node;
    }

    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {
            $node->setNode('display_start', new Node([new EnterProfileNode($this->extensionName, Profile::TEMPLATE, $node->getTemplateName(), $this->varName), $node->getNode('display_start')]));
            $node->setNode('display_end', new Node([new LeaveProfileNode($this->varName), $node->getNode('display_end')]));
        } elseif ($node instanceof BlockNode) {
            $node->setNode('body', new BodyNode([
                new EnterProfileNode($this->extensionName, Profile::BLOCK, $node->getAttribute('name'), $this->varName),
                $node->getNode('body'),
                new LeaveProfileNode($this->varName),
            ]));
        } elseif ($node instanceof MacroNode) {
            $node->setNode('body', new BodyNode([
                new EnterProfileNode($this->extensionName, Profile::MACRO, $node->getAttribute('name'), $this->varName),
                $node->getNode('body'),
                new LeaveProfileNode($this->varName),
            ]));
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}

class_alias('JcoreBroiler\Twig\Profiler\NodeVisitor\ProfilerNodeVisitor', 'Twig_Profiler_NodeVisitor_Profiler');
