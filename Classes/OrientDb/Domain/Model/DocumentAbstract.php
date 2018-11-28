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
 * Class DocumentAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */
abstract class DocumentAbstract extends VertexAbstract implements DocumentInterface{

    /**
     * @var int The uid in TYPO3 of the object
     */
    protected $uid;

    /**
     * @var int The pid in TYPO3 of the object
     */
    protected $pid;


    /**
     * @var int Hidden or not
     */
    protected $hidden;

    /**
     * @var int deleted or not
     */
    protected $deleted;

    /**
     * @var integer Ignores page in search and indexing
     */
    protected $noSearch;

    /**
     * @var int Sorting number
     */
    protected $sorting;

    /**
     * @var int Start time of object
     */
    protected $endtime;

    /**
     * @var int End time of object
     */
    protected $starttime;

    /**
     * @var int Publication time of object
     */
    protected $pubdate;

    /**
     * @var string Merged content for search
     */
    protected $searchContent;

    /**
     * @var string Merged content for fuzzy search
     */
    protected $searchContentFuzzy;

    /**
     * @var string Search content title
     */
    protected $searchContentTitle;

    /**
     * @var string Search content type
     */
    protected $searchContentType;

    /**
     * @var string Search content size
     */
    protected $searchContentSize;


    /**
     * @var string Top keywords of document
     */
    protected $topKeywords;

    /**
     * @var integer Length of indexed text
     */
    protected $textLength;

    /**
     * @var string Template-name for boxes in search results
     */
    protected $boxTemplateName;


    /**
     * Get the top keywords
     *
     * @return array
     */
    public function getTopKeywords() {
        return unserialize($this->getProperty('topKeywords'));
        //===
    }


    /**
     * set the top keywords
     *
     * @param array $value
     * @return void
     */
    public function setTopKeywords($value) {
        $this->setProperty('topKeywords', serialize($value));
    }

}