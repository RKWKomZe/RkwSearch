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
 * Class DatabaseAbstract
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */
abstract class DatabaseAbstract implements DatabaseInterface, \TYPO3\CMS\Core\SingletonInterface {


    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     * @inject
     */
    protected $logger;


    /**
     * Set "TRUE" or "1" if you want database errors outputted. Set to "2" if you also want successful database actions outputted.
     *
     * @var integer|boolean
     */
    public $debugOutput = FALSE;

    /**
     * Internally: Set to last built query (not necessarily executed...)
     *
     * @var string
     */
    public $debugLastBuiltQuery = '';

    /**
     * Internally: Error from last query (if any)
     *
     * @var string
     */
    public $debugError = '';


    /**
     * @var object $link Default database link object
     */
    protected $link = NULL;


    /*********************************************
     * Getter and setter
     *********************************************/
     /**
     * Returns current database handle
     *
     * @return \Doctrine\OrientDb\Binding\HttpBinding|NULL
     */
    public function getHandle() {
        return $this->link;
        //===
    }


    /**
     * Checks if database is connected
     *
     * @return boolean
     */
    public function hasConnection() {

        if (
            ($this->getHandle())
            && (is_object($this->getHandle()))
        ) {
            return TRUE;
            //===
        }
        return FALSE;
        //===
    }

    /**
     * Checks if database is readonly
     *
     * @return boolean
     */
    public function isReadOnly() {

        return (boolean) intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['readOnly']);
        //===

    }

    /**
     * checks if a valid rid is returned
     *
     * @param string $body
     * @return boolean
     */
    public function isValidRid($body)
    {
        return (boolean) preg_match('/#\d+:\d+/', $body);
        //===
    }


    /**
     * Constructor
     *
     */
    public function __construct() {

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['debug']))
            $this->debugOutput = intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['debug']);

        $this->connect();
    }


    /**
     * Destructor
     *
     */
    public function __destruct() {
        $this->disconnect();
    }


    /**
     * Create vertex(es) and return insert id
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @alias insert
     * @throws \RKW\RkwSearch\StorageException
     */
    public function createVertex(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {
        return $this->insert($query);
        //===
    }


    /**
     * Update vertex(es) and return number of updated entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @alias update
     * @throws \RKW\RkwSearch\StorageException
     */
    public function updateVertex(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {
        return $this->update($query);
        //==
    }


    /**
     * Delete vertex(es) and return number of deleted entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @alias update
     * @throws \RKW\RkwSearch\StorageException
     */
    public function deleteVertex(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {
        return $this->delete($query);
        //==
    }


    /**
     * Delete edge(s) and return number of edges
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @alias update
     * @throws \RKW\RkwSearch\StorageException
     */
    public function deleteEdge(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {
        return $this->update($query);
        //===
    }

    /**
     * Update edge(s) and return number of updated entries
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return integer
     * @alias update
     * @throws \RKW\RkwSearch\StorageException
     */
    public function updateEdge(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {
        return $this->update($query);
        //==
    }

    /**
     * Debug function: Outputs error if any
     *
     * @param string $func Function calling debug()
     * @return void
     */
    protected function debug($func) {
        $error = $this->debugError;
        if ($error || (int)$this->debugOutput === 2) {
            \TYPO3\CMS\Core\Utility\DebugUtility::debug(
                array(
                    'caller' => __NAMESPACE__ . '::' . $func,
                    'ERROR' => $error,
                    'lastBuiltQuery' => $this->debugLastBuiltQuery,
                    'debug_backtrace' => \TYPO3\CMS\Core\Utility\DebugUtility::debugTrail()
                ),
                $func,
                is_object($GLOBALS['error']) && @is_callable(array($GLOBALS['error'], 'debug'))
                    ? ''
                    : 'DB Debug'
            );
        }
    }

    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger() {

        if (! $this->logger instanceof \TYPO3\CMS\Core\Log\Logger)
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);

        return $this->logger;
        //===
    }



}
