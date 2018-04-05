<?php

/**
 * * This file is part of MetaModels/attribute_translatedmulti.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedMulti
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 MenAtWork
 * @copyright  2018 The MetaModels Team.
 * @license    https://github.com/menatwork/attribute_translatedmulti/blob/master/LICENSE LGPL-3.
 * @filesource
 */

namespace MetaModels\AttributeTranslatedMultiBundle\Test\DependencyInjection;

use MetaModels\AttributeTranslatedMultiBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeTranslatedMultiBundle\DependencyInjection\MetaModelsAttributeTranslatedMultiExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 */
class MetaModelsAttributeTranslatedMultiExtensionTest extends TestCase
{
    /**
     * Test that extension can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $extension = new MetaModelsAttributeTranslatedMultiExtension();

        $this->assertInstanceOf(MetaModelsAttributeTranslatedMultiExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testFactoryIsRegistered()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();

        $container
            ->expects($this->atLeastOnce())
            ->method('setDefinition')
            ->withConsecutive(
                [
                    'metamodels.attribute_translatedmulti.factory',
                    $this->callback(
                        function ($value) {
                            /** @var Definition $value */
                            $this->assertInstanceOf(Definition::class, $value);
                            $this->assertEquals(AttributeTypeFactory::class, $value->getClass());
                            $this->assertCount(1, $value->getTag('metamodels.attribute_factory'));

                            return true;
                        }
                    )
                ],
                [
                    $this->anything(),
                    $this->anything()
                ]
            );

        $extension = new MetaModelsAttributeTranslatedMultiExtension();
        $extension->load([], $container);
    }
}
