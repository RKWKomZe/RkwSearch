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
 * Class NewsArticle
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class NewsArticle extends News  {

    /**
     * Returns conditions for the filter
     *
     * @return array
     */
    public function getFilter() {

        // check if there is at least one match
        if (
            ($searchField = $this->getConfiguration('searchField'))
            && ($searchFieldTwo = $this->getConfiguration('searchFieldTwo'))
            && ($searchFieldThree = $this->getConfiguration('searchFieldThree'))
        ) {

            return array (
                'selectFields' => (($this->getConfiguration('selectFieldsAddition')) ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ()),
                'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array()),
                'where' => '(NOT (' . $searchField  . ' = 1 OR ' . $searchFieldTwo  . ' = 1) AND ' . $searchFieldThree . ' > 0)'
            );
            //===
        }

        return array();
        //===
    }


}