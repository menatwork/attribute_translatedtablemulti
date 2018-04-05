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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 MenAtWork
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/menatwork/attribute_translatedmulti/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeTranslatedMultiBundle\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\AbstractAttributeTypeFactory;

/**
 * Attribute type factory for translated multi attributes.
 */
class AttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;


    /**
     * Create a new instance.
     *
     * @param Connection               $connection      Database connection.
     */
    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->typeName     = 'translatedmulti';
        $this->typeIcon     = 'bundles/metamodelsattributetranslatedmulti/translatedmulti.png';
        $this->typeClass    = TranslatedMulti::class;
        $this->connection   = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new $this->typeClass($metaModel, $information, $this->connection);
    }
}
