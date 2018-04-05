<?php

/**
 * This file is part of MetaModels/attribute_translatedmulti.
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

namespace MetaModels\AttributeTranslatedMultiBundle\Test;

use MetaModels\AttributeTranslatedMultiBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeTranslatedMultiBundle\Attribute\TranslatedMulti;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 *
 * @package MetaModels\AttributeTranslatedMultiBundle\Test
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * TranslatedMulties of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
        'MetaModels\AttributeTranslatedMultiBundle\Attribute\TranslatedMulti'       => TranslatedMulti::class,
        'MetaModels\AttributeTranslatedMultiBundle\Attribute\AttributeTypeFactory'  => AttributeTypeFactory::class
    ];

    /**
     * Provide the multi class map.
     *
     * @return array
     */
    public function provideMultiClassMap()
    {
        $values = [];

        foreach (static::$classes as $translatedMulti => $class) {
            $values[] = [$translatedMulti, $class];
        }

        return $values;
    }

    /**
     * Test if the deprecated classes are aliased to the new one.
     *
     * @param string $oldClass Old class name.
     * @param string $newClass New class name.
     *
     * @dataProvider provideMultiClassMap
     */
    public function testDeprecatedClassesAreMultied($oldClass, $newClass)
    {
        $this->assertTrue(class_exists($oldClass), sprintf('Class TranslatedMulti "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
