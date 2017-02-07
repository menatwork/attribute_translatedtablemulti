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
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

/**
 * Table tl_metamodel_attribute
 */

/**
 * Add palette configuration.
 */
$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['translatedtabletext extends _complexattribute_'] = array(
    '+advanced' => array('tabletext_quantity_cols', 'translatedtabletext_cols'),
);

/**
 * Add data provider.
 */
$GLOBALS['TL_DCA']['tl_metamodel_attribute']['dca_config']['data_provider']['tl_metamodel_translatedtabletext'] = array
(
    'source' => 'tl_metamodel_translatedtabletext'
);

/**
 * Add child condition.
 */
$GLOBALS['TL_DCA']['tl_metamodel_attribute']['dca_config']['childCondition'][] = array
(
    'from'   => 'tl_metamodel_attribute',
    'to'     => 'tl_metamodel_translatedtabletext',
    'setOn'  => array
    (
        array
        (
            'to_field'   => 'att_id',
            'from_field' => 'id',
        ),
    ),
    'filter' => array
    (
        array
        (
            'local'     => 'att_id',
            'remote'    => 'id',
            'operation' => '=',
        ),
    )
);

/**
 * Add field configuration.
 */
$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['translatedtabletext_cols'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['tabletext_cols'],
    'exclude'   => true,
    'inputType' => 'multiColumnWizard',
    'eval'      => array(
        'disableSorting'                     => true,
        'tl_class'                           => 'clr',
        'columnFields'                       => array(
            'langcode'                       => array(
                'exclude'                    => true,
                'inputType'                  => 'justtextoption',
                'eval'                       => array(
                    'style'                  => 'min-width:75px;display:block;padding-top:28px;',
                    'valign'                 => 'top',
                ),
            ),
            'rowLabels'                     => array(
                'exclude'                    => true,
                'inputType'                  => 'multiColumnWizard',
                'eval'                       => array(
                    'disableSorting'         => true,
                    'tl_class'               => 'clr',
                    'columnFields'           => array(
                        'rowLabel'           => array(
                            'exclude'        => true,
                            'inputType'      => 'text',
                            'eval'           => array(
                                'style'      => 'width:400px;',
                                'rows'       => 2,
                            ),
                        ),
                        'rowStyle'           => array(
                            'inputType'      => 'text',
                            'eval'           => array(
                                'allowHtml'  => false,
                                'style'      => 'width: 90px;',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
