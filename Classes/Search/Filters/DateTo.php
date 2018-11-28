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
 * Class DateTo
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DateTo extends DateAbstract  {

    /**
     * Returns conditions for the filter
     *
     * @return array
     */
    public function getFilter() {

        // check if there is at least one match
        if (
            ($fromFields = $this->getConfiguration('searchFields'))
            && ($timestamp = $this->getDataPrepared(TRUE))
            && (is_numeric($timestamp))
            && (is_array($fromFields))
        ) {

            // build where condition
            $whereClause = array ();
            foreach ($fromFields as $field) {
                $whereClause[] = '(' . $field . ' < ' . intval($timestamp) . ' AND ' . $field . ' > 0)';
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


    /**
     * Adds conditions to current query
     *
     * @return boolean
     */
    public function setFilter () {

        if (
            ($fromFields = $this->getConfiguration('toFields'))
            && (is_array($fromFields))
        ) {

            $whereClause = array ();
            if ($date = $this->getDataPrepared(TRUE)) {

                // ===========================================
                // add fields to select if configured
                if ($this->getConfiguration('selectFieldsAddition'))
                    $this->getQuery()->select(explode(',', $this->getConfiguration('selectFields')), TRUE);


                // ===========================================
                // build where condition
                foreach ($fromFields as $field) {
                    $whereClause[] = '(' . $field . ' <= ? AND ' . $field . ' > 0)';
                }
                $this->getQuery()->andWhere('(' . implode(' AND ', $whereClause) . ')', $date);

                // set feedback
                $this->getQueryFactory()->setActiveFilter(strtolower(Common::getShortName(get_class($this))), $date);

                // ===========================================
                // set ordering if there is something to order
                if (
                    ($orderBy = $this->getConfiguration('sorting'))
                    && (is_array($orderBy))
                )
                    foreach ($orderBy as $value)
                        $this->getQuery()->orderBy($value);

                return TRUE;
                //===
            }
        }

        return FALSE;
        //===
    }
}