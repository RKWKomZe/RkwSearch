<?php
namespace RKW\RkwSearch\OrientDb\Cache;
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
 * Class CacheAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
abstract class CacheAbstract implements \TYPO3\CMS\Core\SingletonInterface{


    /**
    * @var string Key for cache
    */
    private $_key = 'rkw_search';

    /**
     * @var string Identifier for cache
     */
    private $_identifier = 'rkw_search';

    /**
     * @var string Contains context mode (Production, Development...)
     */
    protected $contextMode = NULL;

    /**
     * @var string Contains environment mode (FE or BE)
     */
    protected $environmentMode = NULL;


    /**
     * Returns cache identifier
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->_identifier;
        //===
    }


    /**
     * Returns cache identifer
     *
     * @param mixed $identifier
     * @return $this
     */
    public function setIdentifier($identifier) {
        $this->_identifier = sha1($identifier);
        return $this;
        //===
    }


    /**
     * Returns cached object
     *
     * @param mixed $identifier
     * @return mixed
     */
    public function getContent($identifier = NULL) {

        if ($identifier)
            $this->setIdentifier($identifier);

        // only use cache when in production
        // and when called from FE
        if (
            ($this->getContextMode() != 'Production')
            || ($this->getEnvironmentMode() != 'FE')
        )
            return FALSE;
            //===

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')
            ->getCache($this->_key)
            ->get($this->getIdentifier());
            //===
    }


    /**
     * sets cached content
     *
     * @param mixed $data
     * @param array $tags
     * @param integer $lifetime
     * @param mixed $identifier
     * @return $this
     */
    public function setContent($data, $tags = array(), $lifetime = 86400, $identifier = NULL) {

        if ($identifier)
            $this->setIdentifier($identifier);

        // only use cache when in production
        // and when called from FE
        if (
            ($this->getContextMode() != 'Production')
            || ($this->getEnvironmentMode() != 'FE')
        )
            return $this;
            //===

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')
            ->getCache($this->_key)
            ->set($this->getIdentifier(), $data, $tags, $lifetime);

        return $this;
        //===
    }


    /**
     * Returns cached object
     *
     * @return \TYPO3\CMS\Core\Cache\CacheManager
     */
    public function getCacheManager() {

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        //===
    }


    /**
     * Function to return the current TYPO3_CONTEXT.
     *
     * @return string|NULL
     */
    protected function getContextMode() {

        if (! $this->contextMode)
            if (getenv('TYPO3_CONTEXT'))
                $this->contextMode = getenv('TYPO3_CONTEXT');

        return $this->contextMode;
        //===
    }

    /**
     * Function to return the current TYPO3_MODE.
     * This function can be mocked in unit tests to be able to test frontend behaviour.
     *
     * @return string
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getEnvironmentMode() {

        if (! $this->environmentMode)
            if (TYPO3_MODE)
                $this->environmentMode = TYPO3_MODE;

        return $this->environmentMode;
        //===
    }



    /**
     * Constructor
     *
     * @param string $environmentMode
     * @param string $contextMode
     */
    public function __construct($environmentMode = NULL, $contextMode = NULL) {

        if ($environmentMode)
            $this->environmentMode = $environmentMode;

        if ($contextMode)
            $this->contextMode = $contextMode;
    }

}