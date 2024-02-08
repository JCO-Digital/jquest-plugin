<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class PhiftyInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'bundle' => 'bundles/{$name}/',
        'library' => 'libraries/{$name}/',
        'framework' => 'frameworks/{$name}/',
    );
}
