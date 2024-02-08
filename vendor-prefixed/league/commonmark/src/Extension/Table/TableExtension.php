<?php
/**
 * @license BSD-3-Clause
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JcoreBroiler\League\CommonMark\Extension\Table;

use JcoreBroiler\League\CommonMark\Environment\EnvironmentBuilderInterface;
use JcoreBroiler\League\CommonMark\Extension\ConfigurableExtensionInterface;
use JcoreBroiler\League\CommonMark\Renderer\HtmlDecorator;
use JcoreBroiler\League\Config\ConfigurationBuilderInterface;
use JcoreBroiler\Nette\Schema\Expect;

final class TableExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $attributeArraySchema = Expect::arrayOf(
            Expect::type('string|string[]|bool'), // attribute value(s)
            'string' // attribute name
        )->mergeDefaults(false);

        $builder->addSchema('table', Expect::structure([
            'wrap' => Expect::structure([
                'enabled' => Expect::bool()->default(false),
                'tag' => Expect::string()->default('div'),
                'attributes' => Expect::arrayOf(Expect::string()),
            ]),
            'alignment_attributes' => Expect::structure([
                'left' => (clone $attributeArraySchema)->default(['align' => 'left']),
                'center' => (clone $attributeArraySchema)->default(['align' => 'center']),
                'right' => (clone $attributeArraySchema)->default(['align' => 'right']),
            ]),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $tableRenderer = new TableRenderer();
        if ($environment->getConfiguration()->get('table/wrap/enabled')) {
            $tableRenderer = new HtmlDecorator($tableRenderer, $environment->getConfiguration()->get('table/wrap/tag'), $environment->getConfiguration()->get('table/wrap/attributes'));
        }

        $environment
            ->addBlockStartParser(new TableStartParser())

            ->addRenderer(Table::class, $tableRenderer)
            ->addRenderer(TableSection::class, new TableSectionRenderer())
            ->addRenderer(TableRow::class, new TableRowRenderer())
            ->addRenderer(TableCell::class, new TableCellRenderer($environment->getConfiguration()->get('table/alignment_attributes')));
    }
}
