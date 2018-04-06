<?php
/**
 * This is the MetaModelAttribute class for handling translated table multi fields.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 MenAtWork
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/menatwork/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
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
     */
    public function run()
    {
        if ($this->Database->tableExists('tl_metamodel_translatedtablemulti')) {
            return;
        }

        if($this->Database->tableExists('tl_metamodel_translatedmulti')){
            $this->Database->prepare('RENAME TABLE tl_metamodel_translatedmulti TO tl_metamodel_translatedtablemulti')->execute();

            $this->Database->prepare("UPDATE tl_metamodel_attribute SET type='translatedtablemulti' WHERE type='translatedmulti'")->execute();
        }
    }
}

$objRunonce = new TranslatedTableMultiRunOnce();
$objRunonce->run();