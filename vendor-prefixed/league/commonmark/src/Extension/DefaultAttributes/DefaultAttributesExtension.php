<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JcoreBroiler\League\CommonMark\Extension\DefaultAttributes;

use JcoreBroiler\League\CommonMark\Environment\EnvironmentBuilderInterface;
use JcoreBroiler\League\CommonMark\Event\DocumentParsedEvent;
use JcoreBroiler\League\CommonMark\Extension\ConfigurableExtensionInterface;
use JcoreBroiler\League\Config\ConfigurationBuilderInterface;
use JcoreBroiler\Nette\Schema\Expect;

final class DefaultAttributesExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('default_attributes', Expect::arrayOf(
            Expect::arrayOf(
                Expect::type('string|string[]|bool|callable'), // attribute value(s)
                'string' // attribute name
            ),
            'string' // node FQCN
        )->default([]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, [new ApplyDefaultAttributesProcessor(), 'onDocumentParsed']);
    }
}
