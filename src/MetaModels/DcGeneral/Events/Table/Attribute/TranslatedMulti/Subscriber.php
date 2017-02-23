<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Nölke <zero@brothers-project.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\DcGeneral\Events\Table\Attribute\TranslatedTableText;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\DecodePropertyValueForWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\EncodePropertyValueFromWidgetEvent;
use MetaModels\DcGeneral\Events\BaseSubscriber;

/**
 * This is the helper class for handling translated table text fields.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 */
class Subscriber extends BaseSubscriber
{
    /**
     * {@inheritdoc}
     */
    protected function registerEventsInDispatcher()
    {
        $this
            ->addListener(
                BuildWidgetEvent::NAME,
                array($this, 'fillExtraData')
            )
            ->addListener(
                DecodePropertyValueForWidgetEvent::NAME,
                array($this, 'loadValues')
            )
            ->addListener(
                EncodePropertyValueFromWidgetEvent::NAME,
                array($this, 'saveValues')
            );
    }

    /**
     * Populate the extra data of the widget.
     *
     * @param BuildWidgetEvent $event The event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fillExtraData(BuildWidgetEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getProperty()->getName() !== 'translatedtabletext_cols')) {
            return;
        }

        $model        = $event->getModel();
        $objMetaModel = $this->getMetaModelById($event->getModel()->getProperty('pid'));
        $translator   = $event->getEnvironment()->getTranslator();

        // Check model and input for the cols and get the max value.
        $intModelCols = $model->getProperty('tabletext_quantity_cols');
        $intInputCols = $event->getEnvironment()->getInputProvider()->getValue('tabletext_quantity_cols');
        $intCols      = max(intval($intModelCols), intval($intInputCols));

        // For new models, we might not have a value.
        if (!$intCols) {
            return;
        }

        if (!($objMetaModel && $objMetaModel->isTranslated())) {
            return;
        }

        $attribute = $objMetaModel->getAttributeById($model->getProperty('id'));
        $arrValues = $attribute ? $attribute->get('name') : array();

        $languageEvent = new LoadLanguageFileEvent('languages');
        $this
            ->getServiceContainer()
            ->getEventDispatcher()
            ->dispatch(ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE, $languageEvent);

        $arrLanguages = array();
        foreach ((array) $objMetaModel->getAvailableLanguages() as $strLangCode) {
            $arrLanguages[$strLangCode] = $translator->translate($strLangCode, 'LNG');
        }
        asort($arrLanguages);

        // Ensure we have the values present.
        if (empty($arrValues)) {
            foreach ((array) $objMetaModel->getAvailableLanguages() as $strLangCode) {
                $arrValues[$strLangCode] = '';
            }
        }

        $arrRowClasses = array();
        foreach (array_keys(deserialize($arrValues)) as $strLangcode) {
            $arrRowClasses[] = ($strLangcode == $objMetaModel->getFallbackLanguage())
                ? 'fallback_language'
                : 'normal_language';
        }

        $data                                      = $event->getProperty()->getExtra();
        $data['minCount']                          = count($arrLanguages);
        $data['maxCount']                          = count($arrLanguages);
        $data['columnFields']['langcode']['label'] = $translator->translate(
            'name_langcode',
            'tl_metamodel_attribute'
        );

        $data['columnFields']['langcode']['options']            = $arrLanguages;
        $data['columnFields']['langcode']['eval']['rowClasses'] = $arrRowClasses;
        $data['columnFields']['rowLabels']['label']             = $translator->translate(
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
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function loadValues(DecodePropertyValueForWidgetEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getProperty() !== 'translatedtabletext_cols')) {
            return;
        }

        $objMetaModel = $this->getMetaModelById($event->getModel()->getProperty('pid'));
        $arrLanguages = $objMetaModel->getAvailableLanguages();

        // Check model and input for the cols and get the max value.
        $intModelCols = $event->getModel()->getProperty('tabletext_quantity_cols');
        $intInputCols = $event->getEnvironment()->getInputProvider()->getValue('tabletext_quantity_cols');
        $intCols      = max(intval($intModelCols), intval($intInputCols));

        $varValue = $event->getValue();

        // Kick unused lines.
        foreach ((array) $varValue as $strLanguage => $arrRows) {
            if (count($arrRows) > $intCols) {
                $varValue[$strLanguage] = array_slice($varValue[$strLanguage], 0, $intCols);
            }
        }

        $arrLangValues = deserialize($varValue);
        if (!$objMetaModel->isTranslated()) {
            // If we have an array, return the first value and exit, if not an array, return the value itself.
            if (is_array($arrLangValues)) {
                $event->setValue($arrLangValues[key($arrLangValues)]);
            } else {
                $event->setValue($arrLangValues);
            }

            return;
        }

        $arrOutput = array();
        // Sort like in MetaModel definition.
        if ($arrLanguages) {
            foreach ($arrLanguages as $strLangCode) {
                if (is_array($arrLangValues)) {
                    $varSubValue = $arrLangValues[$strLangCode];
                } else {
                    $varSubValue = $arrLangValues;
                }

                if (is_array($varSubValue)) {
                    $arrOutput[] = array('langcode' => $strLangCode, 'rowLabels' => $varSubValue);
                } else {
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
    public function saveValues(EncodePropertyValueFromWidgetEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getProperty() !== 'translatedtabletext_cols')) {
            return;
        }

        $objMetaModel = $this->getMetaModelById($event->getModel()->getProperty('pid'));
        $varValue     = $event->getValue();

        // Not translated, make it a plain string.
        if (!$objMetaModel->isTranslated()) {
            $event->setValue(serialize($varValue));

            return;
        }

        $arrLangValues = deserialize($varValue);
        $arrOutput     = array();

        foreach ($arrLangValues as $varSubValue) {
            $strLangCode = $varSubValue['langcode'];
            unset($varSubValue['langcode']);
            if (count($varSubValue) > 1) {
                $arrOutput[$strLangCode] = $varSubValue;
            } else {
                $arrKeys = array_keys($varSubValue);

                $arrOutput[$strLangCode] = $varSubValue[$arrKeys[0]];
            }
        }

        $event->setValue(serialize($arrOutput));
    }
}
