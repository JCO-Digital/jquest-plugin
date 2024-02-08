<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class EliasisInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'component' => 'components/{$name}/',
        'module'    => 'modules/{$name}/',
        'plugin'    => 'plugins/{$name}/',
        'template'  => 'templates/{$name}/',
    );
}
