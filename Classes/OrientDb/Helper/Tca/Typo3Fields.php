<?php

namespace RKW\RkwSearch\OrientDb\Helper\Tca;
use RKW\RkwSearch\OrientDb\Helper\Common;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Class Typo3Tca
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Typo3Fields {


    /**
     * Returns an array of default fields for the given table to match against the orient model
     *
     * @param string|object $table OrientDB model or table name
     * @param string $filter
     * @return array The fields for the database
     */
    public static function getDefaultFieldsForOrientClass($table, $filter = NULL) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        $orientClass = Common::getOrientClassNameFromTableName(self::getLanguageOriginalPointerTable($table));

        $fields = array ('uid', 'pid');
        $tcaFields = array (
            'tstamp',
            'crdate',
            'delete',
            'sortby',
            'languageField',
            'transOrigPointerField'
        );

        $tcaFieldsEnable = array (
            'disabled',
            'starttime',
            'endtime'
        );

        $tcaFieldsSpecial = array (
            'noSearch' => 'tx_rkwsearch_no_search',
        );


        // special treatment for delete-filter
        // we only fetch what is really needed!
        if ($filter == 'delete') {

            $tcaFields = array (
                'delete',
                'languageField',
                'transOrigPointerField'
            );

            $tcaFieldsEnable = array ();
            $tcaFieldsSpecial = array ();

        }

        // go through the array of fields and set the fields
        foreach ($tcaFields as $field) {
            if (
                ($GLOBALS['TCA'][$table]['ctrl'][$field])
                && ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl'][$field])
                && (! in_array ($GLOBALS['TCA'][$table]['ctrl'][$field], $fields))
            )
                $fields[] = $GLOBALS['TCA'][$table]['ctrl'][$field] . ' AS ' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl'][$field];
        }

        // go through the array of enable-fields and set the fields
        foreach ($tcaFieldsEnable as $field) {
            if (
                ($GLOBALS['TCA'][$table]['ctrl']['enablecolumns'][$field])
                && ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl']['enablecolumns'][$field])
                && (! in_array ($GLOBALS['TCA'][$table]['ctrl']['enablecolumns'][$field], $fields))
            )
                $fields[] = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns'][$field] . ' AS ' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl']['enablecolumns'][$field];
        }

        // go through the array of special-fields and set the fields
        foreach ($tcaFieldsSpecial as $field => $fieldName) {
            if (
                ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl'][$field])
                && (! in_array ($GLOBALS['TCA'][$table]['ctrl'][$field], $fields))
            )
                $fields[] = $fieldName . ' AS ' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl'][$field];
        }

        return $fields;
        //===
    }

    /**
     * Returns an array of rootline fields for the pages-table to match against the orient model
     *
     * @return array The rootline fields
     */
    public static function getRootlineFields() {

        // get OrientDb class
        $orientClass = Common::getOrientClassNameFromTableName('pages');
        if ($orientClass)
            return GeneralUtility::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'], TRUE);
            //===

        return array();
        //===

    }


    /**
     * Returns an array of rootline fields for the pages-table to match against the orient model
     *
     * @return array The fields for the database
     */
    public static function getRootlineFieldsForOrientClass() {

        $fields = array ();

        // get OrientDb class
        $orientClass = Common::getOrientClassNameFromTableName('pages');
        if ($orientClass) {

            // OrientDb- records are deleted without delete-flag
            $tcaFieldsCtrl = array (
                // 'delete',
            );

            $tcaFieldsEnable = array (
                'disabled',
            );

            // get all rootline-fields that match against the orientDB model
            // for later use: self::getRootlineFields()
            $tcaFields = array (
                'tx_rkwsearch_pubdate'
            );

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'])) {
                foreach ($tcaFields AS $field) {

                    if (
                        ($GLOBALS['TCA']['pages']['columns'][$field])
                        && ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'][$field]['mappingField'])
                        && (! $fields[$field])
                    )
                    $fields[$field] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'][$field]['mappingField'];
                }
            }


            // go through the array of ctrl-fields and set the fields
            foreach ($tcaFieldsCtrl as $field) {
                if (
                    ($GLOBALS['TCA']['pages']['ctrl'][$field])
                    && ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl'][$field])
                    && (! in_array ($GLOBALS['TCA']['pages']['ctrl'][$field], $fields))
                    && (! $fields[$field])
                )
                    $fields[$GLOBALS['TCA']['pages']['ctrl'][$field]] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl'][$field];
            }

            // go through the array of enable-fields and set the fields
            foreach ($tcaFieldsEnable as $field) {
                if (
                    ($GLOBALS['TCA']['pages']['ctrl']['enablecolumns'][$field])
                    && ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl']['enablecolumns'][$field])
                    && (! in_array ($GLOBALS['TCA']['pages']['ctrl']['enablecolumns'][$field], $fields))
                    && (! $fields[$field])
                )
                    $fields[$GLOBALS['TCA']['pages']['ctrl']['enablecolumns'][$field]] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['ctrl']['enablecolumns'][$field];
            }

        }

        return $fields;
        //===

    }

    /**
     * Returns an array of normal fields for the given table that match against the orientDB model
     *
     * @param string|object $table OrientDB model or table name
     * @return array The fields for the database
     * @throws \RKW\RkwSearch\Exception
     */
    public static function getFieldsForOrientClass($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        $orientClass = Common::getOrientClassNameFromTableName(self::getLanguageOriginalPointerTable($table));

        // get all normal fields from TCA-configuration that match against the orientDB model
        $fields = array ();
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'] AS $field => $config) {

                if (
                    ($GLOBALS['TCA'][$table]['columns'][$field])
                    && ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'][$field]['mappingField'])
                )
                    $fields[] = $field . ' AS ' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$orientClass]['columns'][$field]['mappingField'];
            }
        }

        return $fields;
        //===
    }


    /**
     * Returns an array of fields for the versioning of the given table
     *
     * @param string|object $table OrientDB model or table name
     * @return array The fields for the database
     */
    public static function getVersionFields($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        $fields = array ();
        if ($GLOBALS['TCA'][$table]['ctrl']['versioningWS']) {
            $fields[] = 't3ver_oid';
            $fields[] = 't3ver_state';
        }

        return $fields;
        //===
    }


    /**
     * Returns an array of fields for the versioning of the given table
     *
     * @param string|object $table OrientDB model or table name
     * @return array The fields for the database
     */
    public static function getLanguageFields($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        $fields = array ();
        if ($GLOBALS['TCA'][$table]['ctrl']['languageField'])
            $fields[] =  $GLOBALS['TCA'][$table]['ctrl']['languageField'];

        return $fields;
        //===
    }



    /**
     * get the tablename of the language-overlay table
     *
     * @param string|object $table OrientDB model or table name
     * @return string | NULL
     */
    public static function getLanguageForeignTable($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        // check if a separate overlay-table is used and take this instead
        if ($GLOBALS['TCA'][$table]['ctrl']['transForeignTable'])
            return $GLOBALS['TCA'][$table]['ctrl']['transForeignTable'];
            //===

        return NULL;
        //===
    }


    /**
     * returns whether given table has an language overlay table
     * with this method we can check, if the given table is an language overlay table
     *
     * @param string|object $table OrientDB model or table name
     * @return boolean
     */
    public static function hasLanguageForeignTable($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        return isset($GLOBALS['TCA'][$table]['ctrl']['transForeignTable']);
        //===
    }


    /**
     * get the tablename of the language-overlay pointer
     *
     * @param string|object $table OrientDB model or table name
     * @return string | NULL
     */
    public static function getLanguageOriginalPointerField($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        // check if a separate overlay-field is used and take this instead
        if ($GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField'])
            return $GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField'];
            //===

        return NULL;
        //===
    }


    /**
     * returns whether given table has an language overlay pointer
     *
     * @param string|object $table OrientDB model or table name
     * @return boolean
     */
    public static function hasLanguageOriginalPointerField($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        // check if a separate overlay-field is used and take this instead
        return isset($GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField']);
        //===

    }

    /**
     * get the name of the language-overlay field
     *
     * @param string|object $table OrientDB model or table name
     * @return string | NULL
     */
    public static function getLanguageField($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        // check if a separate overlay-field is used and take this instead
        if ($GLOBALS['TCA'][$table]['ctrl']['languageField'])
            return $GLOBALS['TCA'][$table]['ctrl']['languageField'];
            //===

        return NULL;
        //===
    }


    /**
     * returns whether given table has an language overlay field
     *
     * @param string|object $table OrientDB model or table name
     * @return boolean
     */
    public static function hasLanguageField($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        // check if a separate overlay-field is used and take this instead
        return isset($GLOBALS['TCA'][$table]['ctrl']['languageField']);
        //===

    }

    /**
     * get the tablename of the language-overlay table
     *
     * @param string|object $table OrientDB model or table name
     * @return string
     */
    public static function getLanguageOriginalPointerTable($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        // check if a separate overlay-table is used and take this instead
        if ($GLOBALS['TCA'][$table]['ctrl']['transOrigPointerTable'])
            return $GLOBALS['TCA'][$table]['ctrl']['transOrigPointerTable'];
            //===

        return $table;
        //===
    }


    /**
     * returns whether given table has an original pointer table
     * with this method we can check, if the given table is an language overlay table
     *
     * @param string|object $table OrientDB model or table name
     * @return boolean
     */
    public static function hasLanguageOriginalPointerTable($table) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        return isset($GLOBALS['TCA'][$table]['ctrl']['transOrigPointerTable']);
        //===
    }



    /**
     * Returns mm-table of given field for relation

     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getMmTable($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['MM'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['MM'];
            //===

        return NULL;
        //==
    }

    /**
     * Returns mm-match-fields of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return array
     */
    public static function getMmMatchFields($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['MM_match_fields'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['MM_match_fields'];
        //===

        return array();
        //==
    }



    /**
     * Returns foreign-table of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getForeignTable($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_table'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_table'];
            //===

        return NULL;
        //==
    }

    /**
     * Returns foreign-field of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return string
     */
    public static function getForeignField($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_field'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_field'];
            //===

        return 'uid_foreign';
        //==
    }

    /**
     * Returns foreign-table-field of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getForeignTableField($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_table_field'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_table_field'];
            //===

        return NULL;
        //==
    }

    /**
     * Returns foreign-sortby-field of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getForeignSortBy($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_sortby'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_sortby'];
        //===

        return NULL;
        //==
    }


    /**
     * Returns foreign-match-fields of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return array
     */
    public static function getForeignMatchFields($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_match_fields'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_match_fields'];
            //===

        return array();
        //==
    }


    /**
     * Returns foreign-sortby-field of given field for relation
     *
     * @param string|object $table OrientDB model or table name
     * @param string $field configuration name
     * @return string
     * @deprecated
     */
    public static function getLocalField($table, $field) {

        if (is_object($table))
            $table = Common::getTypo3TableFromOrientClass($table);

        if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_label'])
            return $GLOBALS['TCA'][$table]['columns'][$field]['config']['foreign_label'];
            //===

        return 'uid_local';
        //==
    }


}