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

use JcoreBroiler\Twig\Node\Expression\AssignNameExpression;
use JcoreBroiler\Twig\Node\ImportNode;
use JcoreBroiler\Twig\Token;

/**
 * Imports macros.
 *
 *   {% from 'forms.html' import forms %}
 */
final class FromTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect(/* Token::NAME_TYPE */ 5, 'import');

        $targets = [];
        do {
            $name = $stream->expect(/* Token::NAME_TYPE */ 5)->getValue();

            $alias = $name;
            if ($stream->nextIf('as')) {
                $alias = $stream->expect(/* Token::NAME_TYPE */ 5)->getValue();
            }

            $targets[$name] = $alias;

            if (!$stream->nextIf(/* Token::PUNCTUATION_TYPE */ 9, ',')) {
                break;
            }
        } while (true);

        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);

        $var = new AssignNameExpression($this->parser->getVarName(), $token->getLine());
        $node = new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());

        foreach ($targets as $name => $alias) {
            $this->parser->addImportedSymbol('function', $alias, 'macro_'.$name, $var);
        }

        return $node;
    }

    public function getTag()
    {
        return 'from';
    }
}

class_alias('JcoreBroiler\Twig\TokenParser\FromTokenParser', 'Twig_TokenParser_From');
