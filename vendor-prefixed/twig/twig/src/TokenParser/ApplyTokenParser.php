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

namespace JcoreBroiler\Twig\TokenParser;

use JcoreBroiler\Twig\Node\Expression\TempNameExpression;
use JcoreBroiler\Twig\Node\Node;
use JcoreBroiler\Twig\Node\PrintNode;
use JcoreBroiler\Twig\Node\SetNode;
use JcoreBroiler\Twig\Token;

/**
 * Applies filters on a section of a template.
 *
 *   {% apply upper %}
 *      This text becomes uppercase
 *   {% endapply %}
 */
final class ApplyTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $name = $this->parser->getVarName();

        $ref = new TempNameExpression($name, $lineno);
        $ref->setAttribute('always_defined', true);

        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideApplyEnd'], true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new Node([
            new SetNode(true, $ref, $body, $lineno, $this->getTag()),
            new PrintNode($filter, $lineno, $this->getTag()),
        ]);
    }

    public function decideApplyEnd(Token $token)
    {
        return $token->test('endapply');
    }

    public function getTag()
    {
        return 'apply';
    }
}
