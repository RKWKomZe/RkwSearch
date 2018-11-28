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
 * Class Pages
 *
 * @package RKW_Rkwsearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Pages extends \RKW\RkwBasics\Domain\Model\Pages {


    /**
     * txRkwsearchPubdate
     *
     * @var integer
     */
    protected $txRkwsearchPubdate;


    /**
     * txRkwsearchIndexTimestamp
     *
     * @var integer
     */
    protected $txRkwsearchIndexTimestamp = 0;


    /**
     * txRkwsearchIndexStatus
     *
     * @var integer
     */
    protected $txRkwsearchIndexStatus = 0;


    /**
     * txRkwsearchIndexResult
     *
     * @var string
     */
    protected $txRkwsearchIndexResult = '';


    /**
     * txRkwsearchDocumentType
     *
     * @var integer
     */
    protected $txRkwsearchDocumentType = 0;

    /**
     * txRkwsearchNoSearch
     *
     * @var integer
     */
    protected $txRkwsearchNoSearch = 0;



    /**
     * Returns the txRkwsearchPubdate
     *
     * @return integer
     */
    public function getTxRkwsearchPubdate() {
        return $this->txRkwsearchPubdate;
    }

    /**
     * Sets the txRkwsearchPubdate
     *
     * @param integer $txRkwsearchPubdate
     * @return void
     */
    public function setTxRkwsearchPubdate($txRkwsearchPubdate) {
        $this->txRkwsearchPubdate = $txRkwsearchPubdate;
    }

    /**
     * Returns the txRkwsearchIndexTimestamp
     *
     * @return integer $RkwsearchIndexTimestamp
     */
    public function getTxRkwsearchIndexTimestamp() {
        return $this->txRkwsearchIndexTimestamp;
    }


    /**
     * Sets the txRkwsearchIndexTimestamp
     *
     * @param integer $txRkwsearchIndexTimestamp
     * @return void
     */
    public function setTxRkwsearchIndexTimestamp($txRkwsearchIndexTimestamp) {
        $this->txRkwsearchIndexTimestamp = $txRkwsearchIndexTimestamp;
    }

    /**
     * Returns the txRkwsearchIndexStatus
     *
     * @return integer $RkwsearchIndexStatus
     */
    public function getTxRkwsearchIndexStatus() {
        return $this->txRkwsearchIndexStatus;
    }


    /**
     * Sets the txRkwsearchIndexStatus
     *
     * @param integer $txRkwsearchIndexStatus
     * @return void
     */
    public function setTxRkwsearchIndexStatus($txRkwsearchIndexStatus) {
        $this->txRkwsearchIndexStatus = $txRkwsearchIndexStatus;
    }


    /**
     * Returns the txRkwsearchIndexResult
     *
     * @return string $txRkwsearchIndexResult
     */
    public function getTxRkwsearchIndexResult() {
        return $this->txRkwsearchIndexResult;
    }


    /**
     * Sets the txRkwsearchIndexResult
     *
     * @param string $txRkwsearchIndexResult
     * @return void
     */
    public function setTxRkwsearchIndexResult($txRkwsearchIndexResult) {
        $this->txRkwsearchIndexResult = $txRkwsearchIndexResult;
    }


    /**
     * Returns the txRkwsearchDocumentType
     *
     * @return integer $txRkwsearchDocumentType
     */
    public function getTxRkwsearchDocumentType() {
        return $this->txRkwsearchDocumentType;
    }


    /**
     * Sets the txRkwsearchDocumentType
     *
     * @param integer $txRkwsearchDocumentType
     * @return void
     */
    public function setTxRkwsearchDocumentType($txRkwsearchDocumentType) {
        $this->txRkwsearchDocumentType = intval($txRkwsearchDocumentType);
    }

    /**
     * Returns the txRkwsearchNoSearch
     *
     * @return integer $txRkwsearchNoSearch
     */
    public function getTxRkwsearchNoSearch() {
        return $this->txRkwsearchNoSearch;
    }


    /**
     * Sets the txRkwsearchNoSearch
     *
     * @param integer $txRkwsearchNoSearch
     * @return void
     */
    public function setTxRkwsearchNoSearch($txRkwsearchNoSearch) {
        $this->txRkwsearchNoSearch = intval($txRkwsearchNoSearch);
    }

}
?>