<?php

/**
 * This file is part of MetaModels/attribute_translatedtablemulti.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 The MetaModels Team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeTranslatedTableMultiBundle\Test;

use MetaModels\AttributeTranslatedTableMultiBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeTranslatedTableMultiBundle\Attribute\TranslatedTableMulti;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 *
 * @package MetaModels\AttributeTranslatedTableMultiBundle\Test
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * TranslatedMulties of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
        'MetaModels\AttributeTranslatedTableMultiBundle\Attribute\TranslatedTableMulti' => TranslatedTableMulti::class,
        'MetaModels\AttributeTranslatedTableMultiBundle\Attribute\AttributeTypeFactory' => AttributeTypeFactory::class
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
        $this->assertTrue(class_exists($oldClass), sprintf('Class TranslatedTableMulti "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
