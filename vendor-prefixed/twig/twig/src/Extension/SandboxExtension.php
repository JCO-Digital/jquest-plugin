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

namespace JcoreBroiler\Twig\Extension;

use JcoreBroiler\Twig\NodeVisitor\SandboxNodeVisitor;
use JcoreBroiler\Twig\Sandbox\SecurityNotAllowedMethodError;
use JcoreBroiler\Twig\Sandbox\SecurityNotAllowedPropertyError;
use JcoreBroiler\Twig\Sandbox\SecurityPolicyInterface;
use JcoreBroiler\Twig\Sandbox\SourcePolicyInterface;
use JcoreBroiler\Twig\Source;
use JcoreBroiler\Twig\TokenParser\SandboxTokenParser;

final class SandboxExtension extends AbstractExtension
{
    private $sandboxedGlobally;
    private $sandboxed;
    private $policy;
    private $sourcePolicy;

    public function __construct(SecurityPolicyInterface $policy, $sandboxed = false, SourcePolicyInterface $sourcePolicy = null)
    {
        $this->policy = $policy;
        $this->sandboxedGlobally = $sandboxed;
        $this->sourcePolicy = $sourcePolicy;
    }

    public function getTokenParsers()
    {
        return [new SandboxTokenParser()];
    }

    public function getNodeVisitors()
    {
        return [new SandboxNodeVisitor()];
    }

    public function enableSandbox()
    {
        $this->sandboxed = true;
    }

    public function disableSandbox()
    {
        $this->sandboxed = false;
    }

    public function isSandboxed(Source $source = null)
    {
        return $this->sandboxedGlobally || $this->sandboxed || $this->isSourceSandboxed($source);
    }

    public function isSandboxedGlobally()
    {
        return $this->sandboxedGlobally;
    }

    private function isSourceSandboxed(?Source $source): bool
    {
        if (null === $source || null === $this->sourcePolicy) {
            return false;
        }

        return $this->sourcePolicy->enableSandbox($source);
    }

    public function setSecurityPolicy(SecurityPolicyInterface $policy)
    {
        $this->policy = $policy;
    }

    public function getSecurityPolicy()
    {
        return $this->policy;
    }

    public function checkSecurity($tags, $filters, $functions, Source $source = null)
    {
        if ($this->isSandboxed($source)) {
            $this->policy->checkSecurity($tags, $filters, $functions);
        }
    }

    public function checkMethodAllowed($obj, $method, int $lineno = -1, Source $source = null)
    {
        if ($this->isSandboxed($source)) {
            try {
                $this->policy->checkMethodAllowed($obj, $method);
            } catch (SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }
    }

    public function checkPropertyAllowed($obj, $property, int $lineno = -1, Source $source = null)
    {
        if ($this->isSandboxed($source)) {
            try {
                $this->policy->checkPropertyAllowed($obj, $property);
            } catch (SecurityNotAllowedPropertyError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }
    }

    public function ensureToStringAllowed($obj, int $lineno = -1, Source $source = null)
    {
        if ($this->isSandboxed($source) && \is_object($obj) && method_exists($obj, '__toString')) {
            try {
                $this->policy->checkMethodAllowed($obj, '__toString');
            } catch (SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }

        return $obj;
    }
}

class_alias('JcoreBroiler\Twig\Extension\SandboxExtension', 'Twig_Extension_Sandbox');
