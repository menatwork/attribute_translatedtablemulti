<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * @package    MetaModels
 * @subpackage Tests
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

error_reporting(E_ALL);

function includeIfExists($file)
{
    return file_exists($file) ? include $file : false;
}

if (
    // Locally installed dependencies
    (!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php'))
    // We are within an composer install.
    && (!$loader = includeIfExists(__DIR__.'/../../../autoload.php'))) {
    echo 'You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL;
    exit(1);
}

$loader->add('MetaModels\Test', __DIR__);
