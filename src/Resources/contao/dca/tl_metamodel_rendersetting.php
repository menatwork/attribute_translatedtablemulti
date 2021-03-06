<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['translatedtablemulti extends default']
    = array(
    '+advanced' => array('translatedtablemulti_hide_tablehead'),
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['translatedtablemulti_hide_tablehead'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['translatedtablemulti_hide_tablehead'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class' => 'clr w50'
    ),
    'sql'       => "varchar(1) NOT NULL default '0'",
);