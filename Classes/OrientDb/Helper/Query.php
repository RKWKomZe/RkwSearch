<?php
namespace RKW\RkwSearch\OrientDb\Helper;
use RKW\RkwSearch\OrientDb\Helper\Tca\OrientDbFields;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class Query
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Query {


    /**
     * Get the WHERE clause for the enabled fields of this TCA table
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query|string $query
     * @param string $className Name of OrientDb class
     * @param boolean $includeNoSearch Include the no-search condition
     * @return void
     * @throws \RKW\RkwSearch\Exception
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseForEnableFields(&$query, $className, $includeNoSearch = FALSE) {

        if (! $className)
            throw new \RKW\RkwSearch\Exception('No valid className given.', 1424971447);
            //===

        // get field names
        $disabledField = OrientDbFields::getCtrlEnableField($className, 'disabled');
        $starttimeField = OrientDbFields::getCtrlEnableField($className, 'starttime');
        $endtimeField = OrientDbFields::getCtrlEnableField($className, 'endtime');
        $deletedField = OrientDbFields::getCtrlField($className, 'delete');
        $noSearchField = OrientDbFields::getCtrlField($className, 'noSearch');
        $dokTypeField = OrientDbFields::getCtrlField($className, 'dokType');
        $dokTypeList = preg_replace('/[^0-9,]+/', '' ,OrientDbFields::getCtrlField($className, 'dokTypeList'));

        if (
            ($noSearchField)
            && ($includeNoSearch)
        ) {
            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(' . $noSearchField . ' = ? OR ' . $noSearchField . ' IS NULL)', 0);
            } else {
                $query .=  ' AND (' . $noSearchField . ' = ? OR ' . $noSearchField . ' IS NULL)';
            }
        }

        if (
            ($dokTypeField)
            && ($dokTypeList)
        ) {
            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(' . $dokTypeField . ' IN [' . $dokTypeList .'] OR ' . $dokTypeField . ' IS NULL)');
            } else {
                $query .=  ' AND (' . $dokTypeField . ' IN [' . $dokTypeList .'] OR ' . $dokTypeField . ' IS NULL)';
            }

        }

        if ($disabledField) {
            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(' . $disabledField . ' = ? OR ' . $disabledField . ' IS NULL)', 0);
            } else {
                $query .=  ' AND (' . $disabledField . ' = 0 OR ' . $disabledField . ' IS NULL)';
            }

        }

        if ($deletedField) {
            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(' . $deletedField . ' = ? OR '. $deletedField . ' IS NULL)', 0);
            } else {
                $query .=  ' AND (' . $deletedField . ' = 0 OR '. $deletedField . ' IS NULL)';
            }

        }

        if ($starttimeField) {
            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(' . $starttimeField . ' <= ? OR ' . $starttimeField . ' IS NULL)', time());
            } else {
                $query .=  ' AND (' . $starttimeField . ' <= ' . time() . ' OR ' . $starttimeField . ' IS NULL)';
            }
        }

        if ($endtimeField) {

            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(' . $endtimeField . ' IS NULL OR ' . $endtimeField . ' = 0 OR ' . $endtimeField .' > ?)', time());
            } else {
                $query .=  ' AND (' . $endtimeField . ' IS NULL OR ' . $endtimeField . ' = 0 OR ' . $endtimeField .' > ' . time() . ')';
            }
        }
    }


    /**
     * Get the WHERE clause for some language fields
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query|string $query
     * @param integer $languageId
     * @param string $className Name of OrientDb class
     * @returns void
     * @throws \RKW\RkwSearch\Exception
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseForLanguageFields(&$query, $languageId, $className) {

        if (! $className)
            throw new \RKW\RkwSearch\Exception('No valid className given.', 1424971514);
            //===

        $languageField = OrientDbFields::getCtrlField($className, 'languageField');
        if ($languageField) {
            if ($languageId > 0) {
                if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                    $query->andWhere($languageField . ' = ?', intval($languageId));
                } else {
                    $query .= ' AND ' . $languageField . ' = ' . intval($languageId);
                }
            } else {
                if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                    $query->andWhere('(' . $languageField . ' = ? OR ' . $languageField . ' IS NULL)', intval($languageId));
                } else {
                    $query .= ' AND (' . $languageField . ' = ' . intval($languageId) . ' OR ' . $languageField . ' IS NULL)';
                }
            }
        }

    }


    /**
     * get the WHERE clause for the versioning
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query|string $query
     * @param string $className Name of OrientDb class
     * @return void
     * @throws \RKW\RkwSearch\Exception
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    static public function getWhereClauseForVersioning(&$query, $className) {

        if (! $className)
            throw new \RKW\RkwSearch\Exception('No valid className given.', 1424971447);
            //===

        $versionField = OrientDbFields::getCtrlField($className, 'versioningWS');
        if ($versionField) {
            if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query) {
                $query->andWhere('(t3ver_state = ' . new \TYPO3\CMS\Core\Versioning\VersionState(\TYPO3\CMS\Core\Versioning\VersionState::DEFAULT_STATE) . ')');
            } else {
                $query .= ' AND (t3ver_state = ' . new \TYPO3\CMS\Core\Versioning\VersionState(\TYPO3\CMS\Core\Versioning\VersionState::DEFAULT_STATE) . ')';
            }
        }
    }



}