<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\EmbedNode;

class_exists('JcoreBroiler\Twig\Node\EmbedNode');

@trigger_error('Using the "JcoreBroiler_Twig_Node_Embed" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\EmbedNode" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\EmbedNode" instead */
    class JcoreBroiler_Twig_Node_Embed extends EmbedNode
    {
    }
}
