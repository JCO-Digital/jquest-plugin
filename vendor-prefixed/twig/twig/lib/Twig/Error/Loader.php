<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use JcoreBroiler\Twig\Error\LoaderError;

class_exists('JcoreBroiler\Twig\Error\LoaderError');

@trigger_error('Using the "JcoreBroiler_Twig_Error_Loader" class is deprecated since Twig version 2.7, use "JcoreBroiler\Twig\Error\LoaderError" instead.', \E_USER_DEPRECATED);

if (false) {
    /** @deprecated since Twig 2.7, use "JcoreBroiler\Twig\Error\LoaderError" instead */
    class JcoreBroiler_Twig_Error_Loader extends LoaderError
    {
    }
}
