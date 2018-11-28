<?php
namespace RKW\RkwSearch\Search\Filters;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;

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
 * Class Author
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Author extends FiltersAbstract {

    /**
     * Returns filter data
     *
     * @returns array
     */
    public function getFilter () {

        if (
            ($edgeClass = $this->getConfiguration('edgeClass'))
            && ($firstNameField = $this->getConfiguration('searchField'))
            && ($lastNameField = $this->getConfiguration('searchFieldTwo'))
        ) {

            // check if there is at least one match
            if (
                ($persons = $this->getDataPrepared())
                && (count($persons['wordsArray']) > 0)
            ) {

                $edgeDirection = 'in';
                if ($this->getConfiguration('edgeDirection') == 'out')
                    $edgeDirection = 'out';

                $whereClause = array();

                foreach ($persons['wordsArray'] as $cnt => $person) {

                    // remove any abbreviation and explode
                    $name =  explode(self::STRING_DELIMITER, preg_replace('/[a-zA-Z]+\.[ ]?/', '', $person));
                    $firstNameValues = array();
                    $lastNameValues = array();

                    //=========================================================
                    // if we only have one part of the name we suppose it's the last name
                    if (count($name) == 1) {
                        $lastNameValues[] = $name[0];

                    // if we have two parts of the name we suppose it's first and last name
                    } else if (count($name) == 2) {
                        $firstNameValues[] = $name[0];
                        $lastNameValues[] = $name[1];

                    // if we have three or more parts we take the first part as first name and the rest as possible last name!
                    } else if (count($name) > 2) {

                        foreach ($name as $key => $value) {

                            if ($key == 0) {
                                $firstNameValues[] = $value;

                            } else {
                                $lastNameValues[] = $value;
                            }
                        }
                    }

                    //=========================================================
                    $firstNameWhere = array ();
                    if (count($firstNameValues)) {

                        $nameTemp = array();
                        foreach ($firstNameValues as $value) {
                            $nameTemp[] = addslashes($value);
                            $firstNameWhere[] = '(' . $firstNameField . ' = "' . implode(self::STRING_DELIMITER, $nameTemp) . '")';
                        }
                    }

                    $lastNameWhere = array ();
                    if (count($lastNameValues)) {

                        $nameTemp = array();
                        foreach ($lastNameValues as $value) {
                            $nameTemp[] = addslashes($value);
                            $lastNameWhere[] = '(' . $lastNameField . ' = "' . implode(self::STRING_DELIMITER, $nameTemp) . '")';
                        }
                    }

                    if (
                        (count($firstNameWhere))
                        && (count($lastNameWhere))
                    ){
                        $whereClause[] = '(' . implode (' OR ', $firstNameWhere) . ' AND ' . implode (' OR ', $lastNameWhere) .')';

                    } else {

                        $whereClause[] = implode (' OR ', $lastNameWhere);
                    }
                }

                return array (
                    'selectFields' => (($this->getConfiguration('selectFieldsAddition')) ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ()),
                    'searchClass' => (($this->getConfiguration('searchClass')) ? $this->getConfiguration('searchClass') : NULL),
                    'where' =>  '(' . $edgeDirection . '(\'' . addslashes($edgeClass) . '\') contains ('. implode(' OR ', $whereClause) . '))',
                    'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array()),
                );
                //===

            }
        }

        return array();
        //===
    }
}