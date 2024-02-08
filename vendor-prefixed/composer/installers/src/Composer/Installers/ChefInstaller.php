<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class ChefInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'cookbook'  => 'Chef/{$vendor}/{$name}/',
        'role'      => 'Chef/roles/{$name}/',
    );
}
