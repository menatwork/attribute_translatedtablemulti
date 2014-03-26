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
 * @author      Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

namespace MetaModels\DcGeneral\Events\Table\Attribute\TranslatedTableText;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\DecodePropertyValueForWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\EncodePropertyValueFromWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use MetaModels\DcGeneral\Events\BaseSubscriber;
use MetaModels\DcGeneral\Events\Table\Attribute\AttributeBase;

/**
 * This is the helper class for handling translated table text fields.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 */
class TranslatedTableTextCols
{
	/**
	 * Register various events related to tl_metamodel_attribute.
	 *
	 * @param BuildDataDefinitionEvent $event The event.
	 *
	 * @return void
	 */
	public static function registerEvents(BuildDataDefinitionEvent $event)
	{
		if ($event->getContainer()->getName() != 'tl_metamodel_attribute')
		{
			return;
		}

		BaseSubscriber::registerListeners(
			array(
				BuildWidgetEvent::NAME                   => __CLASS__ . '::fillExtraData',
				DecodePropertyValueForWidgetEvent::NAME  => __CLASS__ . '::loadValues',
				EncodePropertyValueFromWidgetEvent::NAME => __CLASS__ . '::saveValues'
			),
			$event->getDispatcher(),
			array(
				'tl_metamodel_attribute',
				'translatedtabletext_cols'
			)
		);
	}

	/**
	 * Populate the extra data of the widget.
	 *
	 * @param BuildWidgetEvent $event The event.
	 *
	 * @return void
	 */
	public static function fillExtraData(BuildWidgetEvent $event)
	{
		$model        = $event->getModel();
		$intCols      = $model->getProperty('tabletext_quantity_cols');
		$objMetaModel = AttributeBase::getMetaModelFromModel($event->getModel());
		$translator   = $event->getEnvironment()->getTranslator();

		// For new models, we might not have a value.
		if (!$intCols)
		{
			return;
		}

		if (!($objMetaModel && $objMetaModel->isTranslated()))
		{
			return;
		}

		$attribute = $objMetaModel->getAttributeById($model->getProperty('id'));
		$arrValues = $attribute ? $attribute->get('name') : array();

		$languageEvent = new LoadLanguageFileEvent('languages');
		$event
			->getEnvironment()
			->getEventPropagator()
			->propagate(ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE, $languageEvent);

		$arrLanguages = array();
		foreach ((array)$objMetaModel->getAvailableLanguages() as $strLangCode)
		{
			$arrLanguages[$strLangCode] = $translator->translate($strLangCode, 'LNG');
		}
		asort($arrLanguages);

		// Ensure we have the values present.
		if (empty($arrValues))
		{
			foreach ((array)$objMetaModel->getAvailableLanguages() as $strLangCode)
			{
				$arrValues[$strLangCode] = '';
			}
		}

		$arrRowClasses = array();
		foreach (array_keys(deserialize($arrValues)) as $strLangcode)
		{
			$arrRowClasses[] = ($strLangcode == $objMetaModel->getFallbackLanguage()) ? 'fallback_language' : 'normal_language';
		}

		$data                                                                           = $event->getProperty()->getExtra();
		$data['minCount']                                                               = count($arrLanguages);
		$data['maxCount']                                                               = count($arrLanguages);
		$data['columnFields']['langcode']['label']                                      = $translator->translate(
			'name_langcode',
			'tl_metamodel_attribute'
		);
		$data['columnFields']['langcode']['options']                                    = $arrLanguages;
		$data['columnFields']['langcode']['eval']['rowClasses']                         = $arrRowClasses;
		$data['columnFields']['rowLabels']['label']                                     = $translator->translate(
			'tabletext_rowLabels',
			'tl_metamodel_attribute'
		);
		$data['columnFields']['rowLabels']['eval']['minCount']                          = $intCols;
		$data['columnFields']['rowLabels']['eval']['maxCount']                          = $intCols;
		$data['columnFields']['rowLabels']['label']                                     = $translator->translate(
			'tabletext_rowLabels',
			'tl_metamodel_attribute'
		);
		$data['columnFields']['rowLabels']['eval']['minCount']                          = $intCols;
		$data['columnFields']['rowLabels']['eval']['maxCount']                          = $intCols;
		$data['columnFields']['rowLabels']['eval']['columnFields']['rowLabel']['label'] = $translator->translate(
			'tabletext_rowLabel',
			'tl_metamodel_attribute'
		);
		$data['columnFields']['rowLabels']['eval']['columnFields']['rowLabel']['eval']  = $arrRowClasses;
		$data['columnFields']['rowLabels']['eval']['columnFields']['rowStyle']['label'] = $translator->translate(
			'tabletext_rowStyle',
			'tl_metamodel_attribute'
		);

		$event->getProperty()->setExtra($data);
	}

	/**
	 * Decode the values into a real table array.
	 *
	 * @param DecodePropertyValueForWidgetEvent $event The event.
	 *
	 * @return void
	 */
	public static function loadValues(DecodePropertyValueForWidgetEvent $event)
	{
		$intCols      = $event->getModel()->getProperty('tabletext_quantity_cols');
		$objMetaModel = AttributeBase::getMetaModelFromModel($event->getModel());
		$arrLanguages = $objMetaModel->getAvailableLanguages();

		$varValue = $event->getValue();

		// Kick unused lines.
		foreach ((array)$varValue as $strLanguage => $arrRows)
		{
			if (count($arrRows) > $intCols)
			{
				$varValue[$strLanguage] = array_slice($varValue[$strLanguage], 0, $intCols);
			}
		}

		$arrLangValues = deserialize($varValue);
		if (!$objMetaModel->isTranslated())
		{
			// If we have an array, return the first value and exit, if not an array, return the value itself.
			if (is_array($arrLangValues))
			{
				$event->setValue($arrLangValues[key($arrLangValues)]);
			}
			else
			{
				$event->setValue($arrLangValues);
			}
			return;
		}

		$arrOutput = array();
		// Sort like in MetaModel definition.
		if ($arrLanguages)
		{
			foreach ($arrLanguages as $strLangCode)
			{
				if (is_array($arrLangValues))
				{
					$varSubValue = $arrLangValues[$strLangCode];
				}
				else
				{
					$varSubValue = $arrLangValues;
				}

				if (is_array($varSubValue))
				{
					$arrOutput[] = array('langcode' => $strLangCode, 'rowLables' => $varSubValue);
				}
				else
				{
					$arrOutput[] = array('langcode' => $strLangCode, 'value' => $varSubValue);
				}
			}
		}

		$event->setValue(serialize($arrOutput));
	}

	/**
	 * Encode the values into a serialized array.
	 *
	 * @param EncodePropertyValueFromWidgetEvent $event The event.
	 *
	 * @return void
	 */
	public static function saveValues(EncodePropertyValueFromWidgetEvent $event)
	{
		$objMetaModel = AttributeBase::getMetaModelFromModel($event->getModel());
		$varValue     = $event->getValue();

		// Not translated, make it a plain string.
		if (!$objMetaModel->isTranslated())
		{
			$event->setValue(serialize($varValue));

			return;
		}

		$arrLangValues = deserialize($varValue);
		$arrOutput     = array();

		foreach ($arrLangValues as $varSubValue)
		{
			$strLangCode = $varSubValue['langcode'];
			unset($varSubValue['langcode']);
			if (count($varSubValue) > 1)
			{
				$arrOutput[$strLangCode] = $varSubValue;
			}
			else
			{
				$arrKeys = array_keys($varSubValue);

				$arrOutput[$strLangCode] = $varSubValue[$arrKeys[0]];
			}
		}

		$event->setValue(serialize($arrOutput));
	}
}
