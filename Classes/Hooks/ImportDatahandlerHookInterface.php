<?php
namespace RKW\RkwSearch\Hooks;

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
 * Class ImportDatahandlerHookInterface
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */


interface ImportDatahandlerHookInterface  {

    /**
     * Imports data and sends backend messages accordingly
     *
     * @return void
     */
    public function import();


    /**
     * Imports data into OrientDb
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface
     * @param boolean $relate Activates setting of relations. Set this to true only for the last call of this method
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     */
    public function importSub($model, $relate = FALSE);


    /**
     * Prepares the import data for current record
     *
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface|NULL
     */
    public function prepareImport();


    /**
     * Prepares the import data for tt_content.
     * This is needed, because content-elements can be copied into other languages
     *
     * @return array
     */
    public function prepareImportContent();


    /**
     * Gets uid of element from given hook object
     *
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     */
    public function getUid ();


    /**
     * Gets the table of the given data
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getOldUid();


    /**
     * Gets the action of the given data
     *
     * @param boolean $switch If set to FALSE we return the value "as is"
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getAction ($switch = TRUE);


    /**
     * Gets the table of the given data
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getTable();


    /**
     * Gets the table of the given data without "cleanup"
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getTableRaw();


    /**
     * Get the language overlay table if one exists
     *
     * @return string|NULL
     */
    public function getTableOverlay();


    /**
     * Get the base table if the language overlay table is used for translation
     *
     * @return string
     */
    public function getTableBase();


    /**
     * Checks if the given table is an language overlay table
     *
     * @return boolean
     */
    public function isTableOverlay();



    /**
     * Gets the data record
     *
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRecord();


    /**
     * Check if there is a record
     *
     * @return boolean
     */
    public function hasRecord();


    /**
     * Gets the data record
     *
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getObject();


    /**
     * Check if there is a record
     *
     * @return boolean
     */
    public function hasObject();


    /**
     * Gets the DataHandler object
     *
     * @return \TYPO3\CMS\Core\DataHandling\DataHandler
     * @throws \RKW\RkwSearch\Exception
     */
    public function getDatabaseHandler();



    /**
     * Returns repository for given table
     *
     * @return \RKW\RkwSearch\OrientDb\Domain\Repository\RepositoryInterface | NULL
     */
    public function getRepository();



    /**
     * Checks if repository for given table exists and returns its name
     *
     * @return string | NULL
     */
    public function hasRepository();



    /**
     * Returns model for given table
     *
     * @param array $data Data to be loaded into object
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface | NULL
     */
    public function getModel($data = array());



    /**
     * Checks if model for given table exists and returns its name
     *
     * @return string | NULL
     */
    public function hasModel();


    /**
     * Gets an array of mapped fields based on the given table
     *
     * @param string $table The table to fetch contents from
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getQueryFields ($table);


    /**
     * Sets the init data of the object
     *
     * @param string $hookAction Status of the current operation, 'new', 'swap', 'update' or 'delete'
     * @param string $hookTable The table currently processing data for
     * @param integer $hookUid The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param object $hookDataHandlerObject (reference) \TYPO3\CMS\Core\DataHandling\DataHandler
     * @param integer $hookOldUid Id of page record an element has been moved from, only available when record is moved
     * @return void
     * @throws \RKW\RkwSearch\Exception
     */
    public function init ($hookAction, $hookTable, $hookUid, $hookDataHandlerObject, $hookOldUid = NULL);


}
?>