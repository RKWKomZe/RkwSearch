<?php
namespace RKW\RkwSearch\OrientDb\Collection;

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
 * Class Document
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @see \Iterator, \Serializable, \Countable
 */
class Document extends CollectionAbstract {


    /**
     * Constructor function
     *
     * @param array $data
     * @param string $type
     */
    public function __construct($data = array(), $type= '') {

        // call parent function
        parent::__construct($data);

        // try to load data into object
        if (
            (! empty($data))
            && (is_array($data)
            && (isset($data['result']))
            && (is_array($data['result'])))
        ) {
            $this->data = $data['result'];
            $this->keys = array_keys($data['result']);
        }
    }



}