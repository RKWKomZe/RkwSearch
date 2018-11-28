<?php
namespace RKW\RkwSearch\Keywords;

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
 * Class Analyser
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Analyser {


    /**
     * @var array Contains the word-list to be analysed
     */
    protected $data;


    /**
     * @var array Contains the results from counting
     */
    protected $results;


    /**
     * Weights the matches
     *
     * @param integer $textLength
     * @return $this
     */
    public function weightMatches ($textLength = 1) {

        if (is_array($this->results)) {

            foreach ($this->results as $key => $dataArray) {

                if (is_array ($this->results[$key])) {

                    // If the weights have already been calculated we break here.
                    // This is to increase performance
                    if ($this->results[$key]['weight'] !== NULL)
                        break;
                        //===

                    // if the given item is not to be weighted, we set weight to zero
                    if ($this->results[$key]['noWeight'] == TRUE) {
                        $this->results[$key]['weight'] = 0;
                        continue;
                        //===
                    }

                    /*
                     * We calculate the weight of the keywords
                     * by the WDF (Within-Document-Frequency)
                     * Through logarithm to base 2, the maximum value of WDF is 1.
                     * We have to add 1, because log(0,2) = INF
                     * @see http://de.wikipedia.org/wiki/Within-document_Frequency
                     *
                     * Formula:
                     * WDF =  log( (MATCH COUNT + 1), 2) / log (NUMBER OF RELEVANT WORDS, 2)
                     *
                     * Since we don't want to include fill-words, we start with the overall found matches
                     * from our filtered and tagged words and do not take all words of the document.
                     */
                    $wdf = log( ($dataArray['count'] + 1), 2) / log ((count($this->results) +1), 2);

                    /*
                     * Now we need a special weighting for longer text (TLW = Text Length Weight)
                     * With logarithm to base 2 we can handle this
                     *
                     * Formula:
                     * TLW = log( (LENGTH +1), 2)
                     */
                    $textLengthWeight = log( ($textLength + 1), 2);

                    /*
                     * Now we need a special weighting for longer keywords (KLW = Keyword Length Weight)
                     * Again with logarithm to base 2 we can handle this
                     * log( (1 + 1), 2) --> 1
                     * log( (2 + 1), 2) --> 1,584
                     * log( (3 + 1), 2) --> 2
                     *
                     * Formula:
                     * KLW = log( (LENGTH +1), 2)
                     */
                     $keyWordLengthWeight = log( ($dataArray['length'] + 1), 2);

                    /*
                     * Last but not least we need a distance weight, since word combinations with more distance
                     * between them are less relevant combinations (DW = Distance Weight)
                     *
                     * We combine this with KLW
                     *
                     * Words with length of 1 get no special up-weighting
                     *
                     * Words with distance of 1 are treated like words with distance of 0, since they are words
                     * that have nothing between them
                     *
                     * Formula:
                     * DW = (log( (MATCH COUNT +1), 2) * KLW) / log (DISTANCE, 2)
                     */
                    $distanceWeight = 1;
                    if ($dataArray['length'] > 1) {
                        $distanceWeight = log( ($dataArray['count'] +1), 2) * $keyWordLengthWeight;

                        if (
                            ($dataArray['distance'] > 1) &&
                            ($dataArray['length'])
                        ) {
                            $distance = $dataArray['distance'] / $dataArray['length'];
                            $distanceWeight = (log( ($dataArray['count'] +1), 2) * $keyWordLengthWeight) / log ($distance +1, 2);
                        }
                    }

                    // now we calculate the final weight by WDF and DistanceWeight
                    $this->results[$key]['weight'] = ($wdf * $distanceWeight);
                }
            }
        }

        return $this;
        //===
    }


    /**
     * Do the counting of the word-matches
     *
     * @return $this
     */
    public function countMatches () {

        // go through each element and process it
        if (! $this->results) {

            foreach ($this->data as $object) {

                if (! $object instanceof \RKW\RkwSearch\TreeTagger\Filter\FilterItem\FilterItemAbstract)
                    continue;
                    //===

                foreach ($object->getKeywords() as $combinedKeyword => $keywordData) {

                    // if keyword does not exists, create it
                    if (! $this->results[$combinedKeyword]) {
                        $this->results[$combinedKeyword] = $keywordData;
                        $this->results[$combinedKeyword]['position'] = array ($this->results[$combinedKeyword]['position']);


                    // if keywords exists, do some mathematics and merging
                    // we count each match here and fetch all variations together
                    // since the match can be multiple times, we have to divide the distance by count
                    } else {

                        // check if we have the same position here. If so, we do not count it
                        if (! in_array($keywordData['position'], $this->results[$combinedKeyword]['position'])) {
                            $this->results[$combinedKeyword]['position'][] = $keywordData['position'];
                            $this->results[$combinedKeyword]['count']++;
                            $this->results[$combinedKeyword]['distance'] = floor(($this->results[$combinedKeyword]['distance'] + $keywordData['distance']) / $this->results[$combinedKeyword]['count']);
                        }

                        // merge variations
                        $this->results[$combinedKeyword]['variations'] = array_merge($this->results[$combinedKeyword]['variations'], $keywordData['variations']);
                    }
                }
            }
        }

        return $this;
        //===

    }


    /**
     * Get results
     *
     * @param string $sortBy
     * @return \RKW\RkwSearch\Collection\AnalysedKeywords
     */
    public function getResults($sortBy = NULL) {

        if (is_array($this->results)) {
            if ($sortBy == 'weight')
                uasort($this->results, "self::sortByWeight");

            if ($sortBy == 'length')
                uasort($this->results, "self::sortByLength");
        }

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\Collection\\AnalysedKeywords', $this->results);
        //===
    }


    /**
     * Get result summary
     *
     * @param integer $limit
     * @param string $sortBy
     * @return string
     */
    public function getResultsSummary($limit = 100, $sortBy = 'weight') {

        $counter = 0;
        $resultDataText = '';
        $analyseResultTwo = $this->getResults($sortBy);
        foreach ($analyseResultTwo as $keyword => $data) {

            // only take the 100 best
            if ($counter >= $limit)
                break;
                //===

            // get the normalized keyword and it's weight
            $resultDataText .= $keyword . ' => ' . $data['weight'] . ' (' . $data['tags']  . ($data['type'] ? ' - ' .$data['type'] : '') . ')' . "\n";

            // get it's variations, too
            foreach ($data['variations'] as $variation)
                $resultDataText .=  $variation . "\n";
            $resultDataText .= "\n";
            $counter++;
        }

        return $resultDataText;
        //===
    }


    /**
     * Get top keywords
     *
     * @param integer $limit
     * @param string $sortBy
     * @return array
     */
    public function getTopKeywords($limit = 100, $sortBy = 'weight') {

        $counter = 0;
        $resultData = array ();
        $analyseResultTwo = $this->getResults($sortBy);
        foreach ($analyseResultTwo as $keyword => $data) {

            // only take the 100 best
            if ($counter >= $limit)
                break;
                //===

            // get the keyword variations
            foreach ($data['variations'] as $variation)
                $resultData[$variation] =  $data['weight'];

            $counter++;
        }

        return $resultData;
        //===
    }



    /**
     * Set data
     *
     * @param \RKW\RkwSearch\TreeTagger\Collection\FilteredRecords $data
     * @return $this
     */
    public function setData($data) {

        // set data and reset results to start processing again
        if ($data instanceof \RKW\RkwSearch\TreeTagger\Collection\FilteredRecords) {
            $this->data = $data;
            $this->results = NULL;
        }

        return $this;
        //===
    }


    /**
     * Sorts the array by number of matches
     *
     * @param integer $a
     * @param integer $b
     *
     * @return integer
     */
    protected static function sortByWeight($a, $b) {

        if ($a['weight'] == $b['weight'])
            return 0;
            //===

        return ($a['weight'] > $b['weight']) ? -1 : 1;
        //===
    }

    /**
     * Sorts the array by length of matches
     *
     * @param integer $a
     * @param integer $b
     *
     * @return integer
     */
    protected static function sortByLength($a, $b) {

        if ($a['length'] == $b['length'])
            return 0;
            //===

        return ($a['length'] > $b['length']) ? 1 : -1;
        //===
    }

}