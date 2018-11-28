<?php
namespace RKW\RkwSearch\Domain\Model;

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
 * Class RidMapping
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class RidMapping extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {


    /**
     * class
     *
     * @var string
     * @validate NotEmpty
     */
    protected $class = '';

    /**
     * rid
     *
     * @var string
     * @validate NotEmpty
     */
    protected $rid = '';

    /**
     * t3table
     *
     * @var string
     * @validate NotEmpty
     */
    protected $t3table = '';

    /**
     * t3id
     *
     * @var integer
     * @validate NotEmpty
     */
    protected $t3id = 0;

    /**
     * t3pid
     *
     * @var integer
     */
    protected $t3pid = 0;

    /**
     * t3lid
     *
     * @var integer
     */
    protected $t3lid = 0;

    /**
     * checksum
     *
     * @var string
     * @validate NotEmpty
     */
    protected $checksum = '';


    /**
     * relations
     *
     * @var string
     */
    protected $relationChecksums  = '';


    /**
     * index_tstamp
     *
     * @var integer
     */
    protected $indexTstamp = 0;

    /**
     * analyse_tstamp
     *
     * @var integer
     */
    protected $analyseTstamp = 0;


    /**
     * tag_tstamp
     *
     * @var integer
     */
    protected $tagTstamp = 0;


    /**
     * import_tstamp
     *
     * @var integer
     */
    protected $importTstamp = 0;


    /**
     * status
     *
     * @var integer
     */
    protected $status = 0;


    /**
     * message
     *
     * @var string
     */
    protected $message = '';


    /**
     * status
     *
     * @var integer
     */
    protected $debug = 0;


    /**
     * noSearch
     *
     * @var integer
     */
    protected $noSearch = 0;


    /**
     * Returns the class
     *
     * @return string $class
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Sets the class
     *
     * @param string $class
     * @return void
     */
    public function setClass($class) {
        $this->class = $class;
    }

    /**
     * Returns the rid
     *
     * @return string $rid
     */
    public function getRid() {
        return $this->rid;
    }

    /**
     * Sets the rid
     *
     * @param string $rid
     * @return void
     */
    public function setRid($rid) {
        $this->rid = $rid;
    }

    /**
     * Returns the t3id
     *
     * @return integer $t3id
     */
    public function getT3id() {
        return $this->t3id;
    }

    /**
     * Sets the t3id
     *
     * @param integer $t3id
     * @return void
     */
    public function setT3id($t3id) {
        $this->t3id = $t3id;
    }


    /**
     * Returns the t3lid
     *
     * @return integer
     */
    public function getT3pid() {
        return $this->t3pid;
    }

    /**
     * Sets the t3pid
     *
     * @param integer $t3pid
     * @return void
     */
    public function setT3pid($t3pid) {
        $this->t3pid = $t3pid;
    }


    /**
     * Returns the t3lid
     *
     * @return integer
     */
    public function getT3lid() {
        return $this->t3lid;
    }


    /**
     * Sets the t3lid
     *
     * @param integer $t3lid
     * @return void
     */
    public function setT3lid($t3lid) {
        $this->t3lid = $t3lid;
    }


    /**
     * Returns the t3table
     *
     * @return string $t3table
     */
    public function getT3table() {
        return $this->t3table;
    }

    /**
     * Sets the t3table
     *
     * @param string $t3table
     * @return void
     */
    public function setT3table($t3table) {
        $this->t3table = $t3table;
    }

    /**
     * Returns the checksum
     *
     * @return string checksum
     */
    public function getChecksum() {
        return $this->checksum;
    }

    /**
     * Sets the checksum
     *
     * @param string $checksum
     * @return string checksum
     */
    public function setChecksum($checksum) {
        $this->checksum = $checksum;
    }

    /**
     * Returns the relationChecksums
     *
     * @return string relationChecksum
     */
    public function getRelationChecksums() {
        return $this->relationChecksums;
    }

    /**
     * Sets the relationChecksum
     *
     * @param string $relationChecksums
     * @return string relationChecksums
     */
    public function setRelationChecksums($relationChecksums) {
        $this->relationChecksums = $relationChecksums;
    }

    /**
     * Returns the index timestamp
     *
     * @return integer $tstamp
     */
    public function getIndexTstamp() {
        return $this->indexTstamp;
    }

    /**
     * Sets the index timestamp
     *
     * @param integer $tstamp
     * @return void
     */
    public function setIndexTstamp($tstamp) {
        $this->indexTstamp= $tstamp;
    }

    /**
     * Returns the analyse timestamp
     *
     * @return integer $tstamp
     */
    public function getAnalyseTstamp() {
        return $this->analyseTstamp;
    }

    /**
     * Sets the analyse timestamp
     *
     * @param integer $tstamp
     * @return void
     */
    public function setAnalyseTstamp($tstamp) {
        $this->analyseTstamp= $tstamp;
    }

    /**
     * Returns the index timestamp
     *
     * @return integer $tstamp
     */
    public function getTagTstamp() {
        return $this->tagTstamp;
    }

    /**
     * Sets the tag timestamp
     *
     * @param integer $tstamp
     * @return void
     */
    public function setTagTstamp($tstamp) {
        $this->tagTstamp= $tstamp;
    }

    /**
     * Returns the import timestamp
     *
     * @return integer $tstamp
     */
    public function getImportTstamp() {
        return $this->importTstamp;
    }

    /**
     * Sets the import timestamp
     *
     * @param integer $tstamp
     * @return void
     */
    public function setImportTstamp($tstamp) {
        $this->importTstamp= $tstamp;
    }

    /**
     * Returns the status
     *
     * @return integer $status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param integer $status
     * @return void
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Returns the message
     *
     * @return string $message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Sets the message
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Returns the debug
     *
     * @return integer $debug
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * Sets the debug
     *
     * @param integer $debug
     * @return void
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }

    /**
     * Returns the noSearch
     *
     * @return integer $debug
     */
    public function getNoSearch() {
        return $this->noSearch;
    }

    /**
     * Sets the noSearch
     *
     * @param integer $noSearch
     * @return void
     */
    public function setNoSearch($noSearch) {
        $this->noSearch = $noSearch;
    }

}