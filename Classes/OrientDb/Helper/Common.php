<?php

namespace RKW\RkwSearch\OrientDb\Helper;

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
 * Class Common
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Common extends \RKW\RkwSearch\Helper\Common {

    /**
     * @const string Path to OrientDb Models
     */
    const ORIENTDB_MODEL_PATH = 'RKW\\RkwSearch\\OrientDb\\Domain\\Model';


    /**
     * @const string Path to OrientDb Repositories
     */
    const ORIENTDB_REPOSITORY_PATH = 'RKW\\RkwSearch\\OrientDb\\Domain\\Repository';


    /**
     * Gets orientDb class name for orient DB
     *
     * @param string $table The original table name
     * @return string|NULL
     */
    public static function getOrientClassNameFromTableName($table) {

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['typo3TableMapping'][$table])
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['typo3TableMapping'][$table];
            //===

        return NULL;
        //===
    }

    /**
     * get the table name from the given orientDb class name or object
     *
     * @param string|object $class OrientDB model or classname
     * @return string|NULL
     */
    public static function getTypo3TableFromOrientClass($class) {

        if (is_object($class))
            $class = Common::getShortName($class, TRUE);

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['typo3TableMapping']) {

            $mappingArray = array_flip($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['typo3TableMapping']);
            if ($mappingArray[$class])
                return $mappingArray[$class];
                //===
        }

        return NULL;
        //===

    }


    /**
     * Gets orientDb class name for orient DB
     *
     * @param string $table The original table name
     * @return string
     */
    public static function getOrientModelFromTableName($table) {

        $class = self::getOrientClassNameFromTableName($table);
        return self::ORIENTDB_MODEL_PATH . '\\' . $class;
        //===
    }



    /**
     * Gets orientDb class name for orient DB
     *
     * @param string $table The original table name
     * @return string
     */
    public static function getOrientRepositoryFromTableName($table) {

        $class = self::getOrientClassNameFromTableName($table);
        return self::ORIENTDB_REPOSITORY_PATH . '\\' . $class . 'Repository';
        //===
    }



    /**
     * Gets orientDb class name for orient DB
     *
     * @param string $class The class name
     * @return string
     */
    public static function getOrientModelFromClassName($class) {

        return self::ORIENTDB_MODEL_PATH . '\\' . $class;
        //===
    }


    /**
     * Gets orientDb class name for orient DB
     *
     * @param string $class The class name
     * @return string
     */
    public static function getOrientRepositoryFromClassName($class) {

        return self::ORIENTDB_REPOSITORY_PATH . '\\' . $class . 'Repository';
        //===
    }



}