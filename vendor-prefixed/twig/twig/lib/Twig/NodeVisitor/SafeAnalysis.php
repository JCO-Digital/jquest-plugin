<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\NodeVisitor\SafeAnalysisNodeVisitor;

class_exists('JcoreBroiler\Twig\NodeVisitor\SafeAnalysisNodeVisitor');

@trigger_error('Using the "JcoreBroiler_Twig_NodeVisitor_SafeAnalysis" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\NodeVisitor\SafeAnalysisNodeVisitor" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\NodeVisitor\SafeAnalysisNodeVisitor" instead */
    class JcoreBroiler_Twig_NodeVisitor_SafeAnalysis extends SafeAnalysisNodeVisitor
    {
    }
}
