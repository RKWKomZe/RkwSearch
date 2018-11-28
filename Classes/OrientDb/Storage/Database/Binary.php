<?php
namespace RKW\RkwSearch\OrientDb\Storage\Database;
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('rkw_search') . 'Classes/Libs/OrientDB-PHP/OrientDB/OrientDB.php');

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
 * Class Binary
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 * @uses \Doctrine\OrientDB\Binding
 * @uses \Doctrine\OrientDB
 */
class Binary extends DatabaseAbstract {

    /**
     * Connects to database
     *
     * @return $this
     * @throws \RKW\RkwSearch\StorageException
     * @api
     */
    public function connect() {

        if (! $this->hasConnection()) {

            // set port and host
            $port = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['port']) > 0 ? intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['port']) : 2424;
            $host = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['host'] ? addslashes($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['host']) : 'localhost';
            if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['database']) {
                try {

                    // prepare database connection and connect
                    $this->link = new \OrientDB($host, $port);
                    $this->link->DBOpen($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['database'], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['user'], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['password']);
                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::DEBUG, 'Connected to database "' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['database'] . '" on ' . $host . ':' .  $port);

                } catch (\OrientDBException $e) {
                    $message = 'Could not connect to database "' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['database'] . '" on ' . $host . ':' .  $port . ' - ' . $e->getMessage();
                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, $message);
                    throw new \RKW\RkwSearch\StorageException($message, 1396524545);
                    //===
                }
            } else {
                $message = 'No database configured.';
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, $message);
                throw new \RKW\RkwSearch\StorageException($message, 1396524545);
                //===
            }
       }

       return $this;
       //===
    }

    /**
     * Disconnect from database if connected
     *
     * @return $this
     * @throws \RKW\RkwSearch\StorageException
     */
    public function disconnect() {

        // is done on destruct in driver
        return $this;
        //===
    }


    /**
     * Execute query
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query|string $query
     * @return mixed
     * @throws \RKW\RkwSearch\StorageException
     * @throws \RKW\RkwSearch\Exception
     */
    public function executeRaw($query) {

        $queryRaw = $query;
        if ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query)
            $queryRaw = $query->getRaw();

        if (! is_string($queryRaw))
            throw new \RKW\RkwSearch\Exception('Invalid data given.', 1397130358);
            //===

        // Append some import fields for select
        if (
            (strpos(strtolower($queryRaw), 'select') === 0)
            && (strpos(strtolower($queryRaw), 'select from') !== 0)
            && (strpos(strtolower($queryRaw), 'select count(') !== 0)
            && ($query instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query)
        ){
            $query->select(array('@class', '@rid', '@type'), TRUE);
            $queryRaw = $query->getRaw();
        }

        // get the response data and check it
        $this->debugLastBuiltQuery = $queryRaw;

        try {

            // set command modes
            $queryType = \OrientDB::COMMAND_QUERY;
            if (strpos(strtolower($queryRaw), 'select') === 0)
                $queryType = \OrientDB::COMMAND_SELECT_SYNC;


            // readOnly behaviour - only SELECT is allowed
            if (
                ($this->isReadOnly())
                && (strpos($queryRaw, 'SELECT') !== 0)
            ) {

                $message = 'Database is set to readOnly. Can not execute command `' . str_replace(array ("\n", "\r"), '', $queryRaw) . '`';
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, $message);

                throw new \RKW\RkwSearch\StorageException($message, 1430746073);
                //===
            }

            // do command
            $response = $this->getHandle()->command($queryType, $queryRaw);

            $this->debug('execute');
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::DEBUG, 'Executed command `' . str_replace(array ("\n", "\r"), '', $queryRaw) . '`');
            return $response;
            //===

        }  catch (\OrientDBException $e) {

            $message = 'Could not execute command `' . $queryRaw . '` - ' .  $e->getMessage();

            // only info for duplicates (i.e. duplicate edges)
            if (strpos($e->getMessage(), 'com.orientechnologies.orient.core.storage.ORecordDuplicatedException') !== FALSE) {
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, $message);
            } else {
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, $message);
            }

            throw new \RKW\RkwSearch\StorageException($message, 1397130683);
            //===
        }
    }

    /**
     * Execute query and return collection
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|\RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @throws \RKW\RkwSearch\StorageException
     */
    public function execute(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        // Load results as collection
        if ($result = $this->executeRaw($query)) {

            $collection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $result);

            // if only one element is selected, we only need the first element in the collection
            if ($query->getCommand() instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\SelectSingle) {

                $collection->rewind();
                return $collection->current();
                //===
            }

            return $collection;
            //===
        }

        // if only one element is selected, we return NULL
        if ($query->getCommand() instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\SelectSingle)
            return NULL;
            //===

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Collection\\Document', array ());
        //===

    }

    /**
     * Execute query and return insert id
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return string
     * @throws \RKW\RkwSearch\StorageException
     */
    public function insert(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        $result = $this->executeRaw($query);
        if ($result instanceof \OrientDBRecord)
            return '#' . $result->recordID;
            //===

        throw new \RKW\RkwSearch\StorageException(sprintf('Could not insert record(s). Query: %s', $query->getRaw()), 1400653435);
        //===
    }


    /**
     * Execute query and return number of updated entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function update(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        $result = $this->executeRaw($query);
        if (
            (isset($result))
            && (is_numeric($result))
        )
            return intval($result);
            //===

        throw new \RKW\RkwSearch\StorageException(sprintf('Could not update record(s). Query: %s', $query->getRaw()), 1400653391);
        //===
    }

    /**
     * Execute query and return number of deleted entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function delete(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        $result = $this->executeRaw($query);
        if (
            (isset($result))
            && (is_numeric($result))
        )
            return intval($result);
            //===

        throw new \RKW\RkwSearch\StorageException(sprintf('Could not delete record(s). Query: %s', $query->getRaw()), 1400653391);
        //===
    }

    /**
     * Execute query and return number of counted entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function count(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        $result = $this->executeRaw($query);
        if (
            (is_array($result))
            && (! empty($result[0]))
            && ($result[0] instanceof \OrientDBRecord)
        )
            return intval(filter_var($result[0]->content, FILTER_SANITIZE_NUMBER_INT));
            //===

        throw new \RKW\RkwSearch\StorageException(sprintf('Could not count record(s). Query: %s', $query->getRaw()), 1400653358);
        //===
    }


    /**
     * Insert edge(s) and return number of edges
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function createEdge(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        $result = $this->executeRaw($query);
        if (is_array($result))
            return count($result);
            //===

        throw new \RKW\RkwSearch\StorageException(sprintf('Could not insert edge(s). Query: %s', $query->getRaw()), 1400653332);
        //===
    }



}
