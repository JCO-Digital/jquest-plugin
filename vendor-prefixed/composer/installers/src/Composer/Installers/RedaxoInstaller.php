<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace JcoreBroiler\Composer\Installers;

class RedaxoInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'addon'          => 'redaxo/include/addons/{$name}/',
        'bestyle-plugin' => 'redaxo/include/addons/be_style/plugins/{$name}/'
    );
}
