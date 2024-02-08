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

use JcoreBroiler\Twig\Node\DoNode;
use JcoreBroiler\Twig\Token;

/**
 * Evaluates an expression, discarding the returned value.
 */
final class DoTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(/* Token::BLOCK_END_TYPE */ 3);

        return new DoNode($expr, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'do';
    }
}

class_alias('JcoreBroiler\Twig\TokenParser\DoTokenParser', 'Twig_TokenParser_Do');
