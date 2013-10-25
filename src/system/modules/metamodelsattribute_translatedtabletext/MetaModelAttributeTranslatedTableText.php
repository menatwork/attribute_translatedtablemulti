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
 * This is the MetaModelAttribute class for handling translated table text fields.
 *
 * @package	   MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     David Maack <david.maack@arcor.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 */
class MetaModelAttributeTranslatedTableText extends MetaModelAttribute implements IMetaModelAttributeTranslated, IMetaModelAttributeComplex
{

	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'translatedtabletext_cols'
		));
	}

	protected function getValueTable()
	{
		return 'tl_metamodel_translatedtabletext';
	}

	public function getFieldDefinition($arrOverrides = array())
	{
		$strActiveLanguage = $this->getMetaModel()->getActiveLanguage();
		$strFallbackLanguage = $this->getMetaModel()->getFallbackLanguage();
		$arrAllColLabels = deserialize($this->get('translatedtabletext_cols'), true);
		$arrColLabels = null;

		if (array_key_exists($strActiveLanguage, $arrAllColLabels))
		{
			$arrColLabels = $arrAllColLabels[$strActiveLanguage];
		}
		else if (array_key_exists($strActiveLanguage, $strFallbackLanguage))
		{
			$arrColLabels = $arrAllColLabels[$strFallbackLanguage];
		}
		else
		{
			$arrColLabels = array_pop(array_reverse($arrAllColLabels));
		}

		// Build DCA.
		$arrFieldDef = parent::getFieldDefinition($arrOverrides);
		$arrFieldDef['inputType'] = 'multiColumnWizard';
		$arrFieldDef['eval']['columnFields'] = array();

		for ($i = 0; $i < count($arrColLabels); $i++)
		{
			$arrFieldDef['eval']['columnFields']['col_'.$i] = array(
				'label' => $arrColLabels[$i]['rowLabel'],
				'inputType' => 'text',
				'eval' => array(),
			);

			if ($arrColLabels[$i]['rowStyle'])
			{
				$arrFieldDef['eval']['columnFields']['col_'.$i]['eval']['style'] = 'width:' . $arrColLabels[$i]['rowStyle'];
			}
		}

		return $arrFieldDef;
	}

	/**
	 * Build a where clause for the given id(s) and rows/cols.
	 *
	 * @param mixed  $mixIds        one, none or many ids to use.
	 * @param type   $intRow        the row number, optional
	 * @param type   $intCol        the col number, optional
	 * @return string
	 */
	protected function getWhere($mixIds, $strLangCode, $intRow = null, $intCol = null)
	{
		$strWhereIds = '';
		$strRowCol = '';
		if ($mixIds)
		{
			if (is_array($mixIds))
			{
				$strWhereIds = ' AND item_id IN (' . implode(',', $mixIds) . ')';
			}
			else
			{
				$strWhereIds = ' AND item_id=' . $mixIds;
			}
		}

		if (is_int($intRow) && is_int($intCol))
		{
			$strRowCol = ' AND row = ? AND col = ?';
		}

		$arrReturn = array(
			'procedure' => 'att_id=?' . $strWhereIds . $strRowCol,
			'params' => ($strRowCol) ? array(intval($this->get('id')), $intRow, $intCol) : array(intval($this->get('id')))
		);

		if ($strLangCode)
		{
			$arrReturn['procedure'] .= ' AND langcode=?';
			$arrReturn['params'][] = $strLangCode;
		}

		return $arrReturn;
	}

	/**
	 * {@inheritdoc}
	 */
	public function valueToWidget($varValue)
	{
		if (!is_array($varValue))
			return array();
		$widgetValue = array();
		foreach ($varValue as $row)
		{
			foreach ($row as $key => $col)
			{
				$widgetValue[$col['row']]['col_'.$key] = $col['value'];
			}
		}
		return $widgetValue;
	}

	/**
	 * {@inheritdoc}
	 */
	public function widgetToValue($varValue, $intId)
	{
		if (!is_array($varValue))
			return null;
		$newValue = array();
		foreach ($varValue as $k => $row)
		{
			foreach ($row as $kk => $col)
			{
				$kk = str_replace('col_', '', $kk);
				$newValue[$k][$kk]['value'] = $col;
				$newValue[$k][$kk]['col'] = $kk;
				$newValue[$k][$kk]['row'] = $k;
			}
		}
		return $newValue;
	}

	protected function getSetValues($arrCell, $intId, $strLangcode)
	{
		return array
			(
			'tstamp' => time(),
			'value' => (string) $arrCell['value'],
			'att_id' => $this->get('id'),
			'row' => (int) $arrCell['row'],
			'col' => (int) $arrCell['col'],
			'item_id' => $intId,
			'langcode' => $strLangcode,
		);
	}

	////////////////////////////////////////////////////////////////////////////
	// IMetaModelAttributeTranslated
	////////////////////////////////////////////////////////////////////////////

	public function getTranslatedDataFor($arrIds, $strLangCode)
	{
		$objDB = Database::getInstance();

		$arrWhere = $this->getWhere($arrIds, $strLangCode);
		$strQuery = 'SELECT * FROM ' . $this->getValueTable() . ($arrWhere ? ' WHERE ' . $arrWhere['procedure'] : '') . ' ORDER BY row ASC, col ASC';
		$objValue = $objDB->prepare($strQuery)
				->executeUncached(($arrWhere ? $arrWhere['params'] : null));

		$arrReturn = array();
		while ($objValue->next())
		{
			$arrReturn[$objValue->item_id][$objValue->row][] = $objValue->row();
		}

		return $arrReturn;
	}

	public function searchForInLanguages($strPattern, $arrLanguages = array())
	{
		return array();
	}

	public function setTranslatedDataFor($arrValues, $strLangCode)
	{
		$objDB = Database::getInstance();
		// get the ids
		$arrIds = array_keys($arrValues);
		$strQueryUpdate = 'UPDATE %s';

		// insert or Update the cells
		$strQuery = 'INSERT INTO ' . $this->getValueTable() . ' %s';
		foreach ($arrIds as $intId)
		{
			// No values give, delete all values.		
			if (empty($arrValues[$intId]))
			{
				$strDelQuery = 'DELETE FROM ' . $this->getValueTable() . ' WHERE att_id=? AND item_id=? AND langcode=?';

				$objDB->prepare($strDelQuery)
						->execute(intval($this->get('id')), $intId, $strLangCode);

				continue;
			}

			//delete missing rows
			$rowIds		 = array_keys($arrValues[$intId]);
			$strDelQuery = 'DELETE FROM ' . $this->getValueTable() . ' WHERE att_id=? AND item_id=? AND langcode=? AND row NOT IN (' . implode(',', $rowIds) . ')';

			$objDB->prepare($strDelQuery)
					->execute(intval($this->get('id')), $intId, $strLangCode);

			//walk every row
			foreach ($arrValues[$intId] as $k => $row)
			{
				//walk every column and update / insert the value
				foreach ($row as $kk => $col)
				{
					$objDB->prepare($strQuery . ' ON DUPLICATE KEY ' . str_replace('SET ', '', $objDB->prepare($strQueryUpdate)->set($this->getSetValues($col, $intId, $strLangCode))->query))
							->set($this->getSetValues($col, $intId, $strLangCode))
							->execute();
				}
			}
		}
	}

	public function unsetValueFor($arrIds, $strLangCode)
	{
		$objDB = Database::getInstance();

		$arrWhere = $this->getWhere($arrIds, $strLangCode);
		$strQuery = 'DELETE FROM ' . $this->getValueTable() . ($arrWhere ? ' WHERE ' . $arrWhere['procedure'] : '');

		$objDB->prepare($strQuery)
				->execute(($arrWhere ? $arrWhere['params'] : null));
	}

	////////////////////////////////////////////////////////////////////////////
	// MetaModelAttributeComplex
	////////////////////////////////////////////////////////////////////////////

	public function getFilterOptions($arrIds, $usedOnly, &$arrCount = null)
	{
		return array();;
	}

	public function setDataFor($arrValues)
	{
		return array();
	}

	public function getDataFor($arrIds)
	{
		return array();
	}

	public function unsetDataFor($arrIds)
	{
		return array();
	}

}