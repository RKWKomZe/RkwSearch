<?php
namespace RKW\RkwSearch\Search\Filters;
use RKW\RkwSearch\OrientDb\Helper\Common;

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
 * Class DateFrom
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DateFrom extends DateAbstract {


    /**
     * Returns conditions for the filter
     *
     * @return array
     */
    public function getFilter() {

        // check if there is at least one match
        if (
            ($fromFields = $this->getConfiguration('searchFields'))
            && ($timestamp = $this->getDataPrepared())
            && (is_numeric($timestamp))
            && (is_array($fromFields))
        ) {

            // build where condition
            $whereClause = array ();
            foreach ($fromFields as $field) {
                $whereClause[] = '(' . $field . ' >= ' . intval($timestamp) . ')';
            }

            return array (
                'selectFields' => (($this->getConfiguration('selectFieldsAddition')) ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ()),
                'searchClass' => (($this->getConfiguration('searchClass')) ? $this->getConfiguration('searchClass') : NULL),
                'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array()),
                'where' => '(' . implode(' AND ', $whereClause) . ')'
            );
            //===
        }

        return array();
        //===
    }

}