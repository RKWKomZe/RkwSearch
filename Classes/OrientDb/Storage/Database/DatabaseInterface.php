<?php
namespace RKW\RkwSearch\OrientDb\Storage\Database;

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
 * Class DatabaseInterface
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */
interface DatabaseInterface {


    /**
     * Connects to database
     *
     * @return $this
     * @throws \RKW\RkwSearch\StorageException
     * @api
     */
    public function connect();


    /**
     * Disconnect from database if connected
     *
     * @return $this
     * @throws \RKW\RkwSearch\StorageException
     */
    public function disconnect();


    /**
     * Checks if database is connected
     *
     * @return boolean
     */
    public function hasConnection();


    /**
     * checks if a valid rid is returned
     *
     * @param string $body
     * @return boolean
     */
    public function isValidRid($body);


    /**
     * Execute query
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query|string $query
     * @return mixed
     * @throws \RKW\RkwSearch\StorageException
     * @throws \RKW\RkwSearch\Exception
     */
    public function executeRaw($query);


    /**
     * Execute query and return collection
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return \RKW\RkwSearch\OrientDb\Collection\Document | array
     * @throws \RKW\RkwSearch\StorageException
     */
    public function execute(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Execute query and return insert id
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return string
     * @throws \RKW\RkwSearch\StorageException
     */
    public function insert(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Execute query and return number of updated entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer Number of updated records
     * @throws \RKW\RkwSearch\StorageException
     */
    public function update(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Execute query and return number of deleted entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer Number of updated records
     * @throws \RKW\RkwSearch\StorageException
     */
    public function delete(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Execute query and return number of counted entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function count(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Create vertex(es) and return insert id
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function createVertex(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Update vertex(es) and return number of updated entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function updateVertex(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Delete vertex(es) and return number of deleted entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function deleteVertex(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Create edge(s) and return number of edges
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function createEdge(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Update edge(s) and return number of updated entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @alias update
     * @throws \RKW\RkwSearch\StorageException
     */
    public function updateEdge(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


    /**
     * Delete edge(s) and return number of edges
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @throws \RKW\RkwSearch\StorageException
     */
    public function deleteEdge(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query);


}
