<?php

/**
 * This is the MetaModelAttribute runonce calling for handling translated table multi fields update modifications.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 */

$objRunonce = new \MetaModels\AttributeTranslatedTableMultiBundle\Runonce\TranslatedTableMultiRunOnce();
$objRunonce->run();
