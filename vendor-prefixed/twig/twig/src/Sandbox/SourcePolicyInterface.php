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

namespace JcoreBroiler\Twig\Sandbox;

use JcoreBroiler\Twig\Source;

/**
 * Interface for a class that can optionally enable the sandbox mode based on a template's Twig\Source.
 *
 * @author Yaakov Saxon
 */
interface SourcePolicyInterface
{
    public function enableSandbox(Source $source): bool;
}
