<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

/**
 * An installer to handle MODX Evolution specifics when installing packages.
 */
class MODXEvoInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'snippet'       => 'assets/snippets/{$name}/',
        'plugin'        => 'assets/plugins/{$name}/',
        'module'        => 'assets/modules/{$name}/',
        'template'      => 'assets/templates/{$name}/',
        'lib'           => 'assets/lib/{$name}/'
    );
}
