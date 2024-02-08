<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Node\NodeCaptureInterface;

class_exists('JcoreBroiler\Twig\Node\NodeCaptureInterface');

@trigger_error('Using the "JcoreBroiler_Twig_NodeCaptureInterface" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Node\NodeCaptureInterface" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Node\NodeCaptureInterface" instead */
    class JcoreBroiler_Twig_NodeCaptureInterface extends NodeCaptureInterface
    {
    }
}
