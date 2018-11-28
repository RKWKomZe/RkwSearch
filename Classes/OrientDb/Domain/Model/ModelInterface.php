<?php
namespace RKW\RkwSearch\OrientDb\Domain\Model;

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
 * Interface ModelInterface
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

interface ModelInterface {

    /**
     * Sets properties
     *
     * @param array $data
     * @return $this
     * @api
     */
    public function setProperties($data);


    /**
     * Returns a hash map of property names and property values.
     *
     * @return array The properties
     * @api
     */
    public function getProperties();


    /**
     * Returns a hash map of all changed property names and property values since model has been loaded
     *
     * @param string $filter If set some properties are filtered out. Needed for checksum-validation and update!
     * @return array The properties
     * @api
     */
    public function getPropertiesChanged($filter = NULL);


    /**
     * Get cluster-id from rid
     *
     * @return integer
     */
    public function getClusterId();


    /**
     * Get position of record from rid
     *
     * @return integer
     */
    public function getPositionId();


    /**
     * Get rid
     *
     * @return string
     */
    public function getRid();


    /**
     * Set rid
     *
     * @param string $value
     * @return void
     */
    public function setRid($value);


    /**
     * Get class
     *
     * @return string
     */
    public function getClass();


    /**
     * Set class
     *
     * @param string $value
     * @return void
     */
    public function setClass($value);


    /**
     * Get type
     *
     * @return string
     */
    public function getType();


    /**
     * Set type
     *
     * @param string $value
     * @return void
     */
    public function setType($value);


    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion();


    /**
     * Set version
     *
     * @param integer $value
     * @return void
     */
    public function setVersion($value);




}
