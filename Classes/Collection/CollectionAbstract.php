<?php
namespace RKW\RkwSearch\Collection;

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
 * Class CollectionAbstract
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @see \Iterator, \Serializable, \Countable
 */

abstract class CollectionAbstract implements \TYPO3\CMS\Core\Collection\CollectionInterface {


    /**
     * @var integer Current position
     */
    protected $position = 0;


    /**
     * @var array List of valid array keys
     */
    protected $keys = array ();


    /**
     * @var array Data array
     */
    protected $data = array();


    /**
     * Set position to zero
     *
     */
    public function rewind() {
        $this->position = 0;
    }


    /**
     * Get current entry
     *
     * @return object | array
     */
    public function current() {
        return $this->getElement($this->position);
        //===
    }

    /**
     * Get first entry
     *
     * @return object | array
     */
    public function first() {
        return $this->getElement(0);
        //===
    }


    /**
     * Get current key
     *
     * @return integer | string
     */
    public function key() {
        if ($this->keys[$this->position] === NULL)
            return -1;
            //===

        return $this->keys[$this->position];
        //===
    }


    /**
     * Jump to next key
     *
     */
    public function next() {
        ++$this->position;
    }


    /**
     * Jump to previous key
     *
     */
    public function prev() {
        --$this->position;
    }


    /**
     * Check if key is set
     *
     */
    public function valid() {
        return isset($this->data[$this->keys[$this->position]]);
        //===
    }


    /**
     * Serialize data
     *
     * @return string
     */
    public function serialize() {
        return serialize($this->data);
        //===
    }


    /**
     * Unserialize data
     *
     * @param string $data
    */
    public function unserialize($data) {
        $this->data = unserialize($data);
        $this->keys = array_keys($this->data);
    }


    /**
     * Return number of elements
     *
     * @return integer
     */
    public function count() {

        return count($this->data);
        //===
    }


    /**
     * Get data
     *
     * @return mixed | NULL
     */
    public function getData () {

        return $this->data;
        //===
    }

    /**
     * Get data by given key
     *
     * @param string $key
     * @param integer $tolerance If set we use the levenshstein-distance to exclude follow-up-items that are nearly the same
     * @param boolean $takeLonger If TRUE we use the longer of the follow-up-items (only active if $tolerance is set)
     * @return mixed | NULL
     */
    public function getDataByKey ($key, $tolerance = NULL, $takeLonger = FALSE) {

        $result = array ();
        foreach ($this->keys as $internalKey) {

            $data = $this->getElement($internalKey);
            if (is_object($data)) {
                $getter = 'get' . ucfirst($key);
                if ($temp = $data->$getter()) {

                    if (
                        ($formerEntry = $result[count($result) -1])
                        && (is_numeric($tolerance))
                    ){
                        // compare last string added with current
                        // and ignore it if nearly equal
                        if (levenshtein($formerEntry, $temp) > $tolerance) {

                            $result[] = $temp;

                        // items with lower difference are ignored
                        // except if they are longer!
                        } else {

                            if (
                                ($takeLonger)
                                && (strlen($formerEntry) < strlen($temp))
                            )
                                $result[count($result) -1] = $temp;
                        }

                    } else {
                        $result[] = $temp;
                    }
                }

            } else if (is_array($data)) {
                if ($temp = $data[$key]) {

                    if (
                        ($formerEntry = $result[count($result) -1])
                        && (is_numeric($tolerance))
                    ){

                        // compare last string added with current
                        // and ignore it if nearly equal
                        if (levenshtein($formerEntry, $temp) > $tolerance) {

                            $result[] = $temp;

                        // items with lower difference are ignored
                        // except if they are longer!
                        } else {

                            if (
                                ($takeLonger)
                                && (strlen($formerEntry) < strlen($temp))
                            )
                                $result[count($result) -1] = $temp;
                        }

                    } else {
                        $result[] = $temp;
                    }
                }
            }
        }

        return $result;
        //===
    }

    /**
     * Return next element
     *
     * @param integer $i Number of items to go forth
     * @return object | array
     */
    public function getNext($i = 1) {

        return $this->getElement($this->position + intval($i));
        //===
    }


    /**
     * Return previous element
     *
     *@param integer $i Number of items to go back
     * @return object | array
     */
    public function getPrev($i = 1) {

        $value = $this->position - intval($i);
        if ($this->position >= $this->count())
            $value = $this->count() - intval($i +1);

        return $this->getElement($value);
        //===
    }


    /**
     * Get element by position
     *
     * @param integer $position Position of array to return
     * @return mixed | NULL
     */
    public function getElement ($position) {

        if (($this->data[$this->keys[$position]]))
            return $this->data[$this->keys[$position]];
            //===

        return NULL;
        //===
    }



    /**
     * Constructor function
     *
     * @param array $data
     */
    public function __construct($data = array()) {

        $this->position = 0;

        // try to load data into object
        if (
            (! empty($data))
            && (is_array($data))
        ) {
            $this->data = $data;
            $this->keys = array_keys($data);
        }

    }

}