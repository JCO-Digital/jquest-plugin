<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class PhpBBInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'extension' => 'ext/{$vendor}/{$name}/',
        'language'  => 'language/{$name}/',
        'style'     => 'styles/{$name}/',
    );
}
