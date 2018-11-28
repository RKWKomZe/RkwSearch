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
 * Class QueueAnalysedKeywords
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class QueueAnalysedKeywords extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {


    /**
     * crdate
     *
     * @var integer
     */
    protected $crdate;


    /**
     * crdate
     *
     * @var integer
     */
    protected $tstamp;

    /**
    * serialized
    *
    * @var string
    */
    protected $serialized = '';

    /**
     * keywordCount
     *
     * @var integer
     */
    protected $keywordCount = 0;

    /**
	 * ridMapping
	 *
	 * @var \RKW\RkwSearch\Domain\Model\RidMapping
	 */
	protected $ridMapping = NULL;

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
     * Returns the serialized
     *
     * @return string $serialized
     */
    public function getSerialized() {
        return unserialize($this->serialized);
    }

    /**
     * Sets the serialized
     *
     * @param string $serialized
     * @return void
     */
    public function setSerialized($serialized) {
        $this->serialized = serialize($serialized);
        if ($serialized instanceof \Countable)
            $this->setKeywordCount(count($serialized));

    }

    /**
     * Returns the keywordCount
     *
     * @return integer $serialized
     */
    public function getKeywordCount() {
        return $this->keywordCount;
    }

    /**
     * Sets the keywordCount
     *
     * @param integer $count
     * @return void
     */
    public function setKeywordCount($count) {
        $this->keywordCount = intval($count);
    }

	/**
	 * Returns the ridMapping
	 *
	 * @return \RKW\RkwSearch\Domain\Model\RidMapping $ridMapping
	 */
	public function getRidMapping() {
		return $this->ridMapping;
	}

	/**
	 * Sets the ridMapping
	 *
	 * @param \RKW\RkwSearch\Domain\Model\RidMapping $ridMapping
	 * @return void
	 */
	public function setRidMapping(\RKW\RkwSearch\Domain\Model\RidMapping $ridMapping) {
		$this->ridMapping = $ridMapping;
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
     * Returns the crdate
     *
     * @return integer
     */
    public function getCrdate() {
        return $this->crdate;
        //===
    }


    /**
     * Returns the tstamp
     *
     * @return integer
     */
    public function getTstamp() {
        return $this->tstamp;
        //===
    }



}