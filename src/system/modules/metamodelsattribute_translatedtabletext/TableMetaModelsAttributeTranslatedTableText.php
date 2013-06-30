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

/**
 * This is the helper class for handling translated table text fields.
 *
 * @package	   MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 */
class TableMetaModelsAttributeTranslatedTableText extends TableMetaModelAttribute
{

	/**
	 * @var TableMetaModelsAttributeTranslatedTableText
	 */
	protected static $objInstance = null;

	/**
	 * Get the static instance.
	 *
	 * @static
	 * @return TableMetaModelsAttributeTranslatedTableText
	 */
	public static function getInstance()
	{
		if (self::$objInstance == null)
		{
			self::$objInstance = new self;
		}
		return self::$objInstance;
	}

	////////////////////////////////////////////////////////////////////////////
	// Callback
	////////////////////////////////////////////////////////////////////////////

	public function loadValues($varValue, $objDC)
	{
		$intCols = $this->getQuantityCols();
		$objMetaModel = $this->getMM();
		$arrLanguages = $objMetaModel->getAvailableLanguages();

		// Kick unused lines.
		foreach ((array) $varValue as $strLanguage => $arrRows)
		{
			if (count($arrRows) > $intCols)
			{
				$varValue[$strLanguage] = array_slice($varValue[$strLanguage], 0, $intCols);
			}
		}

		$arrLangValues = deserialize($varValue);
		if (!$objMetaModel->isTranslated())
		{
			// if we have an array, return the first value and exit, if not an array, return the value itself.
			return is_array($arrLangValues) ? $arrLangValues[key($arrLangValues)] : $arrLangValues;
		}

		// sort like in metamodel definition
		if ($arrLanguages)
		{
			$arrOutput = array();
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
					$arrOutput[] = array('langcode' => $strLangCode, 'rowLabeles' => $varSubValue);
				}
				else
				{
					$arrOutput[] = array('langcode' => $strLangCode, 'value' => $varSubValue);
				}
			}
		}

		return serialize($arrOutput);
	}

	public function saveValues($varValue, $objDc)
	{


		$objMetaModel = $this->getMM();

		// not translated, make it a plain string.
		if (!$objMetaModel->isTranslated())
		{
			return $varValue;
		}
		$arrLangValues = deserialize($varValue);
		$arrOutput = array();



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

		return serialize($arrOutput);
	}

	public function loadTableTextCols(DataContainer $objDC)
	{
		$intCols = $this->getQuantityCols();
		$objMetaModel = $this->getMM();
		$objModel = $objMetaModel ? $objMetaModel->getAttributeById($this->Input->get('id')) : array();
		$arrValues = $objModel ? $objModel->get('name') : array();

		if ($objMetaModel && $objMetaModel->isTranslated())
		{
			$this->loadLanguageFile('languages');
			$arrLanguages = array();
			foreach ((array) $objMetaModel->getAvailableLanguages() as $strLangCode)
			{
				$arrLanguages[$strLangCode] = $GLOBALS['TL_LANG']['LNG'][$strLangCode];
			}
			asort($arrLanguages);

			// Ensure we have the values present.
			if (empty($arrValues))
			{
				foreach ((array) $objMetaModel->getAvailableLanguages() as $strLangCode)
				{
					$arrValues[$strLangCode] = '';
				}
			}

			$arrRowClasses = array();
			foreach (array_keys(deserialize($arrValues)) as $strLangcode)
			{
				$arrRowClasses[] = ($strLangcode == $objMetaModel->getFallbackLanguage()) ? 'fallback_language' : 'normal_language';
			}

			$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['translatedtabletext_cols']['eval'] = array_merge_recursive($GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['translatedtabletext_cols']['eval'], array(
				'minCount' => count($arrLanguages),
				'maxCount' => count($arrLanguages),
				'disableSorting' => true,
				'tl_class' => 'clr',
				'columnFields' => array(
					'langcode' => array(
						'label' => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['name_langcode'],
						'exclude' => true,
						'inputType' => 'justtextoption',
						'options' => $arrLanguages,
						'eval' => array(
							'rowClasses' => $arrRowClasses,
							'valign' => 'center',
							'style' => 'min-width:75px;display:block;padding-top:28px;',
							'valign' => 'top'
						)
					),
					'rowLabeles' => array(
						'label' => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['tabletext_rowLabels'],
						'exclude' => true,
						'inputType' => 'multiColumnWizard',
						'eval' => array(
							'minCount' => $intCols,
							'maxCount' => $intCols,
							'disableSorting' => true,
							'tl_class' => 'clr',
							'columnFields' => array(
								'rowLabel' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['tabletext_rowLabel'],
									'exclude' => true,
									'inputType' => 'text',
									'eval' => array(
										'rowClasses' => $arrRowClasses,
										'style' => 'width:400px;',
										'rows' => 2
									)
								),
								'rowStyle' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['tabletext_rowStyle'],
									'inputType' => 'text',
									'eval' => array('allowHtml' => false, 'style' => 'width: 90px;'),
								)
							),
						)
					),
				)
			));
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// Helper
	////////////////////////////////////////////////////////////////////////////

	/**
	 * Get current MM from Input/Database
	 * 
	 * @return IMetaModel The MetaModel.
	 */
	protected function getMM()
	{
		if ($this->Input->get('pid'))
		{
			return MetaModelFactory::byId($this->Input->get('pid'));
		}
		else
		{
			return MetaModelFactory::byId($this->Database
									->prepare('SELECT pid FROM tl_metamodel_attribute WHERE id=?')
									->execute($this->Input->get('id'))
									->pid
			);
		}
	}

	protected function getQuantityCols()
	{
		$intCols = 1;

		// Get cols.
		if ($this->Input->get('tabletext_quantity_cols'))
		{
			$intCols = intval($this->Input->get('tabletext_quantity_cols'));
		}
		else if ($this->Input->get('id'))
		{
			$objResult = $this->Database
					->prepare('SELECT tabletext_quantity_cols FROM tl_metamodel_attribute WHERE id=?')
					->execute($this->Input->get('id'));

			if ($objResult->numRows != 0)
			{
				$intCols = $objResult->tabletext_quantity_cols;
			}
		}

		return $intCols;
	}

}