<?php

namespace RKW\RkwSearch\OrientDb\Storage\Query\Formatter;

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
 * Class Query
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class Query extends \Doctrine\OrientDB\Query\Formatter\Query {


    /**
     * Implodes and array using a comma.
     *
     * @param   array $array
     * @return  string
     */
    protected static function implode(array $array) {

        $result = array ();
        foreach ($array as $value) {
            if (! in_array($value, $result))
                $result[] = $value;
        }
        return implode(', ', $result);
        //===
    }

    /**
     * Implodes the $values in a string regularly formatted.
     *
     * @param array $values
     * @param mixed $nonFilter
     * @return  string
     */
    protected static function implodeRegular(array $values, $nonFilter = null) {

        $values = self::stripNonSQLCharactersArray($values, $nonFilter);
        $nonEmptyValues = array();

        foreach ($values as $value) {
            if ($value !== '') {
                foreach (explode(',',$value) as $subValue) {
                    if (! in_array(trim($subValue), $nonEmptyValues))
                        $nonEmptyValues[] = trim($subValue);

                }
            }
        }

        return self::implode($nonEmptyValues);
        //===
    }
}
