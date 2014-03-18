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

$GLOBALS['METAMODELS']['attributes']['translatedtabletext']['class'] = 'MetaModelAttributeTranslatedTableText';
$GLOBALS['METAMODELS']['attributes']['translatedtabletext']['image'] =
	'system/modules/metamodelsattribute_translatedtabletext/html/translatedtabletext.png';

$GLOBALS['TL_EVENTS']['dc-general.factory.build-data-definition[tl_metamodel_attribute]'][] =
	'DcGeneral\Events\Table\Attribute\TranslatedTableText\TranslatedTableTextCols::registerEvents';
