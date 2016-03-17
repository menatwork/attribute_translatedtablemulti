<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\TranslatedTableText;

use MetaModels\Attribute\AbstractAttributeTypeFactory;

/**
 * Attribute type factory for translated table text attributes.
 */
class AttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->typeName  = 'translatedtabletext';
        $this->typeIcon  = 'system/modules/metamodelsattribute_translatedtabletext/html/translatedtabletext.png';
        $this->typeClass = 'MetaModels\Attribute\TranslatedTableText\TranslatedTableText';
    }
}
