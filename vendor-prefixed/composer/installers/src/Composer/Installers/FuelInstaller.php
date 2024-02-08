<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class FuelInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'module'  => 'fuel/app/modules/{$name}/',
        'package' => 'fuel/packages/{$name}/',
        'theme'   => 'fuel/app/themes/{$name}/',
    );
}
