<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_translatedtablemulti'] = [
    'config'                            => [
        'sql'                           => [
            'keys'                      => [
                'id'                    => 'primary',
                'att_id,item_id,row,col,langcode'    => 'unique',
                'att_id,item_id'        => 'index',
            ],
        ],
    ],
    'fields'                      => [
        'id'                      => [
            'sql'                 => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'                  => [
            'sql'                 => 'int(10) unsigned NOT NULL default \'0\'',
        ],
        'att_id'                  => [
            'sql'                 => 'int(10) unsigned NOT NULL default \'0\'',
        ],
        'item_id'                 => [
            'sql'                 => 'int(10) unsigned NOT NULL default \'0\'',
        ],
        'langcode'                => [
            'sql'                 => 'varchar(5) NOT NULL default \'\'',
        ],
        'row'                 => [
            'sql'                 => 'int(5) unsigned NOT NULL default \'0\'',
        ],
        'col'                 => [
            'sql'                 => 'varchar(255) NOT NULL default \'\'',
        ],
        'value'                   => [
            'sql'                 => 'text NULL',
        ],
    ],
];