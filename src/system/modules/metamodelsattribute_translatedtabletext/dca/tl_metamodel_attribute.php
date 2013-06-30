<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package     MetaModels
 * @subpackage  AttributeTranslatedTableText
 * @author      David Maack <david.maack@arcor.de>
 * @author      Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

/**
 * Table tl_metamodel_attribute 
 */
$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['translatedtabletext extends _complexattribute_'] = array
	(
	'+advanced' => array('tabletext_quantity_cols', 'translatedtabletext_cols'),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['config']['onload_callback'][] = array('TableMetaModelsAttributeTranslatedTableText', 'loadTableTextCols');

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['tabletext_quantity_cols'] = array
	(
	'label' => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['tabletext_quantity_cols'],
	'exclude' => true,
	'inputType' => 'select',
	'options' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
	'eval' => array('tl_class' => 'clr m12','alwaysSave' => true, 'submitOnChange' => true),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['translatedtabletext_cols'] = array
	(
	'label' => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['tabletext_cols'],
	'exclude' => true,
	'inputType' => 'multiColumnWizard',
	'load_callback' => array
		(
		array('TableMetaModelsAttributeTranslatedTableText', 'loadValues')
	),
	'save_callback' => array
		(
		array('TableMetaModelsAttributeTranslatedTableText', 'saveValues')
	),
	'eval' => array()
);