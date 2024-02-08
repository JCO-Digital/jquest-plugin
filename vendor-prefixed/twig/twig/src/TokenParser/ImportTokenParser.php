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
 *   {% import 'forms.html' as forms %}
 */
final class ImportTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(/* Token::NAME_TYPE */ 5, 'as');
        $var = new AssignNameExpression($this->parser->getStream()->expect(/* Token::NAME_TYPE */ 5)->getValue(), $token->getLine());
        $this->parser->getStream()->expect(/* Token::BLOCK_END_TYPE */ 3);

        $this->parser->addImportedSymbol('template', $var->getAttribute('name'));

        return new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
    }

    public function getTag()
    {
        return 'import';
    }
}

class_alias('JcoreBroiler\Twig\TokenParser\ImportTokenParser', 'Twig_TokenParser_Import');
