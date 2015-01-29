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
 * @author      Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author      Andreas Isaak <andy.jared@googlemail.com>
 * @author      David Greminger <david.greminger@1up.io>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\TranslatedTableText;

use MetaModels\Attribute\Base;
use MetaModels\Attribute\ITranslated;
use MetaModels\Attribute\IComplex;

/**
 * This is the MetaModelAttribute class for handling translated table text fields.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     David Maack <david.maack@arcor.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 */
class TranslatedTableText extends Base implements ITranslated, IComplex
{
    /**
     * {@inheritDoc}
     */
    public function getAttributeSettingNames()
    {
        return array_merge(parent::getAttributeSettingNames(), array(
            'translatedtabletext_cols',
        ));
    }

    /**
     * Retrieve the table name containing the values.
     *
     * @return string
     */
    protected function getValueTable()
    {
        return 'tl_metamodel_translatedtabletext';
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldDefinition($arrOverrides = array())
    {
        $strActiveLanguage   = $this->getMetaModel()->getActiveLanguage();
        $strFallbackLanguage = $this->getMetaModel()->getFallbackLanguage();
        $arrAllColLabels     = deserialize($this->get('translatedtabletext_cols'), true);
        $arrColLabels        = null;

        if (array_key_exists($strActiveLanguage, $arrAllColLabels)) {
            $arrColLabels = $arrAllColLabels[$strActiveLanguage];
        } elseif (array_key_exists($strActiveLanguage, $strFallbackLanguage)) {
            $arrColLabels = $arrAllColLabels[$strFallbackLanguage];
        } else {
            $arrColLabels = array_pop(array_reverse($arrAllColLabels));
        }

        // Build DCA.
        $arrFieldDef                         = parent::getFieldDefinition($arrOverrides);
        $arrFieldDef['inputType']            = 'multiColumnWizard';
        $arrFieldDef['eval']['columnFields'] = array();

        $count = count($arrColLabels);
        for ($i = 0; $i < $count; $i++) {
            $arrFieldDef['eval']['columnFields']['col_' . $i] = array(
                'label' => $arrColLabels[$i]['rowLabel'],
                'inputType' => 'text',
                'eval' => array(),
            );

            if ($arrColLabels[$i]['rowStyle']) {
                $arrFieldDef['eval']['columnFields']['col_' . $i]['eval']['style'] =
                    'width:' . $arrColLabels[$i]['rowStyle'];
            }
        }

        return $arrFieldDef;
    }

    /**
     * Build a where clause for the given id(s) and rows/cols.
     *
     * @param mixed  $mixIds      One, none or many ids to use.
     *
     * @param string $strLangCode The language code.
     *
     * @param int    $intRow      The row number, optional.
     *
     * @param int    $intCol      The col number, optional.
     *
     * @return string
     */
    protected function getWhere($mixIds, $strLangCode, $intRow = null, $intCol = null)
    {
        $strWhereIds = '';
        $strRowCol   = '';
        if ($mixIds) {
            if (is_array($mixIds)) {
                $strWhereIds = ' AND item_id IN ('.implode(',', $mixIds).')';
            } else {
                $strWhereIds = ' AND item_id='.$mixIds;
            }
        }

        if (is_int($intRow) && is_int($intCol)) {
            $strRowCol = ' AND row = ? AND col = ?';
        }

        $arrReturn = array(
            'procedure' => 'att_id=?'.$strWhereIds.$strRowCol,
            'params' => ($strRowCol)
                ? array(intval($this->get('id')), $intRow, $intCol)
                : array(intval($this->get('id'))),
        );

        if ($strLangCode) {
            $arrReturn['procedure'] .= ' AND langcode=?';
            $arrReturn['params'][]   = $strLangCode;
        }

        return $arrReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        if (!is_array($varValue)) {
            return array();
        }

        $widgetValue = array();
        foreach ($varValue as $row) {
            foreach ($row as $key => $col) {
                $widgetValue[$col['row']]['col_' . $key] = $col['value'];
            }
        }

        return $widgetValue;
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        if (!is_array($varValue)) {
            return null;
        }

        $newValue = array();
        foreach ($varValue as $k => $row) {
            foreach ($row as $kk => $col) {
                $kk = str_replace('col_', '', $kk);

                $newValue[$k][$kk]['value'] = $col;
                $newValue[$k][$kk]['col']   = $kk;
                $newValue[$k][$kk]['row']   = $k;
            }
        }

        return $newValue;
    }

    /**
     * Retrieve the setter array.
     *
     * @param array  $arrCell     The cells of the table.
     *
     * @param int    $intId       The id of the item.
     *
     * @param string $strLangCode The language code.
     *
     * @return array
     */
    protected function getSetValues($arrCell, $intId, $strLangCode)
    {
        return array(
            'tstamp'   => time(),
            'value'    => (string) $arrCell['value'],
            'att_id'   => $this->get('id'),
            'row'      => (int) $arrCell['row'],
            'col'      => (int) $arrCell['col'],
            'item_id'  => $intId,
            'langcode' => $strLangCode,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatedDataFor($arrIds, $strLangCode)
    {
        $objDB = \Database::getInstance();

        $arrWhere = $this->getWhere($arrIds, $strLangCode);
        $strQuery = sprintf(
            'SELECT * FROM %s %s ORDER BY row ASC, col ASC',
            $this->getValueTable(),
            ($arrWhere ? ' WHERE ' . $arrWhere['procedure'] : '')
        );
        $objValue = $objDB->prepare($strQuery)
            ->executeUncached(($arrWhere ? $arrWhere['params'] : null));

        $arrReturn = array();
        while ($objValue->next()) {
            $arrReturn[$objValue->item_id][$objValue->row][] = $objValue->row();
        }

        return $arrReturn;
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function searchForInLanguages($strPattern, $arrLanguages = array())
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function setTranslatedDataFor($arrValues, $strLangCode)
    {
        $objDB = \Database::getInstance();

        // Get the ids.
        $arrIds         = array_keys($arrValues);
        $strQueryUpdate = 'UPDATE %s';

        // Insert or Update the cells.
        $strQuery = 'INSERT INTO ' . $this->getValueTable() . ' %s';
        foreach ($arrIds as $intId) {
            // No values give, delete all values.
            if (empty($arrValues[$intId])) {
                $strDelQuery = 'DELETE FROM ' . $this->getValueTable() . ' WHERE att_id=? AND item_id=? AND langcode=?';

                $objDB->prepare($strDelQuery)
                        ->execute(intval($this->get('id')), $intId, $strLangCode);

                continue;
            }

            // Delete missing rows.
            $rowIds      = array_keys($arrValues[$intId]);
            $strDelQuery = sprintf(
                'DELETE FROM %s WHERE att_id=? AND item_id=? AND langcode=? AND row NOT IN (%s)',
                $this->getValueTable(),
                implode(',', $rowIds)
            );

            $objDB->prepare($strDelQuery)
                    ->execute(intval($this->get('id')), $intId, $strLangCode);

            // Walk every row.
            foreach ($arrValues[$intId] as $row) {
                // Walk every column and update/insert the value.
                foreach ($row as $col) {
                    $values   = $this->getSetValues($col, $intId, $strLangCode);
                    $subQuery = $objDB
                        ->prepare($strQueryUpdate)
                        ->set($values)
                        ->query;

                    $objDB
                        ->prepare($strQuery . ' ON DUPLICATE KEY ' . str_replace('SET ', '', $subQuery))
                        ->set($values)
                        ->execute();
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unsetValueFor($arrIds, $strLangCode)
    {
        $objDB = \Database::getInstance();

        $arrWhere = $this->getWhere($arrIds, $strLangCode);
        $strQuery = 'DELETE FROM ' . $this->getValueTable() . ($arrWhere ? ' WHERE ' . $arrWhere['procedure'] : '');

        $objDB->prepare($strQuery)
                ->execute(($arrWhere ? $arrWhere['params'] : null));
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDataFor($arrValues)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDataFor($arrIds)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function unsetDataFor($arrIds)
    {
        return array();
    }
}
