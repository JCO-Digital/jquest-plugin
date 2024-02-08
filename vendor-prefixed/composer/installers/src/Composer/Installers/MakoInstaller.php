<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class MakoInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'package' => 'app/packages/{$name}/',
    );
}
