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

use JcoreBroiler\Twig\Node\FlushNode;
use JcoreBroiler\Twig\Token;

/**
 * Flushes the output to the client.
 *
 * @see flush()
 */
final class FlushTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $this->parser->getStream()->expect(/* Token::BLOCK_END_TYPE */ 3);

        return new FlushNode($token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'flush';
    }
}

class_alias('JcoreBroiler\Twig\TokenParser\FlushTokenParser', 'Twig_TokenParser_Flush');
