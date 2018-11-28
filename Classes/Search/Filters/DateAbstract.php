<?php
namespace RKW\RkwSearch\Search\Filters;

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

abstract class DateAbstract extends FiltersAbstract  {

    /**
     * @const string Delimiter for explode on strings
     */
    const STRING_DELIMITER = '.';


    /**
     * Returns the month as number if given as string
     *
     * @param string $string
     * @returns integer | NULL
     * @throws \RKW\RkwSearch\Exception
     */
    public function getMonthValues ($string) {

        if (
            ($monthMapping = $this->getConfiguration('monthMapping'))
            && (is_array($monthMapping))
        ) {

            $delimiter = self::STRING_DELIMITER;
            if ($this->getConfiguration('dateDelimiter'))
                $delimiter = $this->getConfiguration('dateDelimiter');

            // replace all month-names
            foreach ($monthMapping as $monthName => $monthValue) {
                $string = preg_replace('/' . $monthName . '/i', $monthValue . $delimiter, $string);
            }

        }

        return $string;
        //===
    }


    /**
     * Returns the processed data
     *
     * @param boolean $additionalTime
     * @returns array|integer
     */
    public function getDataPrepared ($additionalTime = FALSE) {

        $result = array ();

        // get month values
        $dateString = $this->getMonthValues($this->getData());

        // split at delimiter
        $delimiter = self::STRING_DELIMITER;
        if ($this->getConfiguration('dateDelimiter'))
            $delimiter = $this->getConfiguration('dateDelimiter');

        $dateArray = explode($delimiter, $dateString);
        foreach ($dateArray as $item) {

            if (is_numeric($item))
                $result[] = trim($item);

            // break if we reach the maximum
            if (count($result) >= 3)
                break;
                //===
        }


        // case 1: only one result - must be year
        $date = 0;
        if (
            (count($result) == 1)
            && ($result[0])
            && ($result[0] > 1900)
        ) {

            $date = strtotime('1.1.' . ($additionalTime ? $result[0]+1 : $result[0]));


        // case 2: two results - must be month and year
        } elseif (
            (count($result) == 2)
            && ($result[1])
            && ($result[1] > 1900)
        ) {

            $date = strtotime('1.' . ($additionalTime ? $result[0]+1 : $result[0]) . '.' . $result[1]);

        // case 3: three results - must be day, month and year
        } elseif (
            (count($result) == 3)
            && ($result[2])
            && ($result[2] > 1900)
        ) {

            $date = strtotime(($additionalTime ? $result[0]+1 : $result[0]) . '.' . $result[1] . '.' . $result[2]);
        }

        return $date;
        //===

    }


}