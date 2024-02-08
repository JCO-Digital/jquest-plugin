<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class PantheonInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'script' => 'web/private/scripts/quicksilver/{$name}',
        'module' => 'web/private/scripts/quicksilver/{$name}',
    );
}
