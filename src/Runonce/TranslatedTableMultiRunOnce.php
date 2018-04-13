<?php

/**
 * This is the MetaModelAttribute class for handling translated table multi fields.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 */

namespace MetaModels\AttributeTranslatedTableMultiBundle\Runonce;

use Contao\Controller;

/**
 * Class TranslatedTableMultiRunOnce
 *
 * @package MetaModels\AttributeTranslatedTableMultiBundle
 */
class TranslatedTableMultiRunOnce extends Controller
{
    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
    }

    /**
     * Run the controller
     * Update from attribute_translatedmulto to attribute_translatedtablemulti
     *
     * @return void|null
     */
    public function run()
    {
        if ($this->Database->tableExists('tl_metamodel_translatedtablemulti')) {
            return;
        }

        if ($this->Database->tableExists('tl_metamodel_translatedmulti')) {
            $this->Database
                ->prepare('RENAME TABLE tl_metamodel_translatedmulti TO tl_metamodel_translatedtablemulti')
                ->execute();

            $this->Database
                ->prepare("UPDATE tl_metamodel_attribute SET type='translatedtablemulti' WHERE type='translatedmulti'")
                ->execute();
        }

        $this->Database
            ->prepare("UPDATE tl_metamodel_attribute SET type='translatedtablemulti' WHERE type='translatedmulti'")
            ->execute();

        $this->Database
            ->prepare("UPDATE tl_metamodel_rendersetting SET template='mm_attr_translatedtablemulti' WHERE template='mm_attr_translatedmulti'")
            ->execute();
    }
}
