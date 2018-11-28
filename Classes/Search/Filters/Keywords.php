<?php
namespace RKW\RkwSearch\Search\Filters;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;
use RKW\RkwSearch\Helper\Text;

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
 * Class Keywords
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Keywords extends FiltersAbstract {

    /**
     * Returns filter data
     *
     * @returns array
     */
    public function getFilter () {

        if ($searchField = $this->getConfiguration('searchField')) {

            // check if there is at least one match
            if (
                ($data = $this->getDataPrepared())
                && ($data['stringLucene'])
            ) {
                // deactivate fuzzy search if quotes are in search-string
                $result = array (
                    'fulltext' => array (
                        'search' => $data['stringLucene'],
                        'searchFuzzy' => $data['wordsArrayFuzzy'] ? implode(' OR ', $data['wordsArrayFuzzy']) : NULL,
                        'searchField' => $searchField,
                        'searchFieldFuzzy' => (($this->getConfiguration('searchFieldFuzzy')) ? $this->getConfiguration('searchFieldFuzzy') : NULL),
                        'searchFieldBoost' => (($this->getConfiguration('searchFieldBoost') > 0) ? floatval($this->getConfiguration('searchFieldBoost')) : 1),
                        'searchFieldType' => (($this->getConfiguration('searchFieldType')) ? $this->getConfiguration('searchFieldType') : NULL),
                        'searchFieldSize' => (($this->getConfiguration('searchFieldSize')) ? $this->getConfiguration('searchFieldSize') : NULL),
                        'searchFieldTitle' => (($this->getConfiguration('searchFieldTitle')) ? $this->getConfiguration('searchFieldTitle') : NULL),
                        'searchFieldTitleBoost' => (($this->getConfiguration('searchFieldTitleBoost') > 0) ? floatval($this->getConfiguration('searchFieldTitleBoost')) : 1),
                        'selectFields' => (($this->getConfiguration('selectFieldsAddition')) ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ()),
                        'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array())
                    ),
                    'selectFields' => (($this->getConfiguration('selectFieldsAddition')) ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ()),
                    'searchClass' => (($this->getConfiguration('searchClass')) ? $this->getConfiguration('searchClass') : NULL),
                    'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array())
                );

                return $result;
                //===

            }
        }

        return array();
        //===
    }


}