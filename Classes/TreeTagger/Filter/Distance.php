<?php
namespace RKW\RkwSearch\TreeTagger\Filter;

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
 * Class Distance
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Distance extends \RKW\RkwSearch\TreeTagger\Filter\FilterAbstract {


    /**
     * Filters the data by defined tag-combinations
     *
     * @return void
     */
    protected function executeSub() {

        $filterList = array ();
        $filterMaxDistancePrev = 5;
        $filterMaxDistanceNext = 5;
        $result = array ();

        if (
            ($configuration = $this->getConfiguration())
            && ($this->getConfiguration('definition'))
            && (is_array($this->getConfiguration('definition')))
        ) {

            // unset definition list for filters
            unset($configuration['definition']);

            // Build filter if-clause
            foreach ($this->getConfiguration('definition') as $filterItem) {

                $filterSingle = array ();

                // we need at least the current entry
                if (
                    (! is_array($filterItem))
                    || (! $filterItem['cur'])
                )
                    continue;
                    //===

                if ($filterItem['prevMaxDistance'])
                    $filterMaxDistancePrev = intval($filterItem['prevMaxDistance']);

                if ($filterItem['nextMaxDistance'])
                    $filterMaxDistanceNext = intval($filterItem['nextMaxDistance']);

                // ======= Previous and Next ==============
                // with this code we try to load maximum X previous or next words
                // and match them against the filter
                foreach (array('prev', 'next') as $type) {

                    if ($filterItem[$type]) {

                        $range = range($filterMaxDistancePrev,1);
                        if ($type == 'next')
                            $range = range($filterMaxDistanceNext, 1);

                        foreach ($range as $key) {
                            $filterSingle[] = '

                                if ($tempItem = $this->data->get' . ucfirst($type) . '(' . $key . ')) {
                                    $setter = \'set\' . ucfirst(strtolower(\'' . $type . '\'));
                                    $filterItemObject->$setter(' . $key . ', $tempItem);
                                }

                            ';
                        }
                    }
                }

                // here we combine the above filters with a logical AND
                // We return the found objects (depending on the filter) and the combined keyword
                if (! empty ($filterSingle))
                    $filterList[] = '

                        $filterItemObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\'RKW\\RkwSearch\\TreeTagger\\Filter\\FilterItem\\Distance\', $configuration, unserialize(\'' . serialize($filterItem) . '\'));
                        if ($filterItemObject->checkData($entry, \'cur\')) {

                            $filterItemObject->setCur($textPos, $entry);

                            ' . implode(' ', $filterSingle) . '

                            if ($filterItemObject->hasMatch())
                               $result[] = $filterItemObject;
                        }
                    ';
            }

            // combine the set filter as a logical OR
            $callback = '';
            if (! empty ($filterList))
                $callback = implode(' ', $filterList);

            // go through all tagged words and do the filtering
            mb_internal_encoding('UTF-8');
            foreach ($this->data as $textPos => $entry) {
                eval ($callback);
            }

            $this->results = $result;
        }

    }



}