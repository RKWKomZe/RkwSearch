<?php

namespace RKW\RkwSearch\OrientDb\Helper\Tca;
use RKW\RkwSearch\OrientDb\Helper\Common;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;

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
 * Class OrientDbFields
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class OrientDbFields {


    /**
     * get the fieldname form configuration name
     *
     * @param string|object $class OrientDB model or classname
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getCtrlField($class, $field) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl'][$field])
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl'][$field];
            //===

        return NULL;
        //===
    }


    /**
     * get the fieldname form configuration name
     *
     * @param string|object $class OrientDB model or classname
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getCtrlEnableField($class, $field) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl']['enablecolumns'][$field])
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl']['enablecolumns'][$field];
            //===

        return NULL;
        //===

    }

    /**
     * get the name of the language-overlay field
     *
     * @param string $class string|object $class OrientDB model or classname
     * @return NULL|string
     */
    public static function getLanguageField($class) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        // check if a separate overlay-field is used and take this instead
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl']['languageField'])
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl']['languageField'];
            //===

        return NULL;
        //===
    }


    /**
     * returns whether given table has an language overlay field
     *
     * @param string|object $class OrientDB model or classname
     * @return boolean
     */
    public static function hasLanguageField($class) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        return isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['ctrl']['languageField']);
        //===

    }

    /**
     * Checks if relation-field is set
     *
     * @param string|object $class OrientDB model or classname
     * @param string $field configuration name
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     */
    public static function hasRelationField($class, $field) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        return isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]) && (! empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]));
        //===

    }

    /**
     * Checks if relation-field is set
     *
     * @param string|object $class OrientDB model or classname
     * @param array $filterList
     * @return array | NULL
     * @throws \RKW\RkwSearch\Exception
     */
    public static function getRelationFields($class, $filterList = array()) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations']) {

            // set filter if a filter list is given
            if ($filterList) {

                $result = array ();
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'] as $field => $config)
                    if (in_array($field, $filterList))
                        $result[$field] = $config;

                return $result;
                //===
            }

            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'];
            //===
        }

        return NULL;
        //===

    }


    /**
     * Returns mm-table of given field for relation

     * @param string|object $class OrientDB model or classname
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getRelationMmTable($class, $field) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]['mmTable'])
             return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]['mmTable'];
            //===

        return NULL;
        //==
    }


    /**
     * Returns foreign-table of given field for relation
     *
     * @param string|object $class OrientDB model or classname
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getRelationForeignTable($class, $field) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]['foreignTable'])
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]['foreignTable'];
            //===

        return NULL;
        //===
    }


    /**
     * Returns edge-class of given field for relation
     *
     * @param string|object $class OrientDB model or classname
     * @param string $field configuration name
     * @return NULL|string
     */
    public static function getRelationEdgeClass($class, $field) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]['edgeClass'])
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$class]['relations'][$field]['edgeClass'];
            //===

        return NULL;
        //===

    }

    /**
     * Check if the given edge-class is defined in OrientDB-TCA
     *
     * @param string $edgeClass OrientDB model or classname
     * @return boolean
     */
    public static function hasRelationEdgeClass($edgeClass) {

        return isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'][$edgeClass]);
        //===

    }

}