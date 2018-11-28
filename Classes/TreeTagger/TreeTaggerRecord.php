<?php
namespace RKW\RkwSearch\TreeTagger;

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
 * Class TreeTaggerRecord
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class TreeTaggerRecord {

    /**
     * @var array Contains all data
     */
    protected $_data;


    /**
     * Returns full data set
     *
     * @return array
     */
    public function getData() {

        if ($this->_data)
            return $this->_data;
            //===

        return array();
        //===
    }


    /**
     * Returns original word
     *
     * @return string
     */
    public function getWord() {

        if ($this->_data[0])
            return str_replace('.', '', $this->_data[0]);
            //===

        return NULL;
        //===
    }


    /**
     * Returns the tag
     *
     * @return string
     */
    public function getTag() {

        if ($this->_data[1])
            return $this->_data[1];
            //===

        return NULL;
        //===
    }


    /**
     * Returns the base of the word
     *
     * @return string
     */
    public function getBase() {

        mb_internal_encoding('UTF-8');
        if ($this->_data[2]) {

            // fallback for unknown-Tag
            if (mb_strpos($this->_data[2], '<') === 0)
                return $this->getWord();
                //===

            // fallback for cardinal numbers
            if ($this->_data[2] == '@card@')
                return $this->getWord();
                //===

            if (mb_strpos($this->_data[2], '|') !== FALSE)
                // return explode('|', str_replace('.', '', $this->_data[2]));
                return explode('|', $this->_data[2]);
               //===

            //return str_replace('.', '', $this->_data[2]);
            return $this->_data[2];
            //===

        }

        return NULL;
        //===
    }

    /**
     * Returns the base of the word in raw format
     *
     * @return string
     */
    public function getBaseRaw() {

        return $this->_data[2];
        //===

    }


    /**
     * Constructor
     *
     * @param string $data
     */
    public function __construct($data = '') {

        if (
            ($data)
            && is_string($data)
        )
            $this->_data = explode("\t", $data);

    }


} 