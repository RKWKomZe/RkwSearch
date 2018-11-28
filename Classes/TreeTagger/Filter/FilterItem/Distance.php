<?php
namespace RKW\RkwSearch\TreeTagger\Filter\FilterItem;

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

class Distance extends \RKW\RkwSearch\TreeTagger\Filter\FilterItem\FilterItemAbstract {


    /**
     * @var array Current item where all starts from
     */
    protected $cur;

    /**
     * @var array Previous items
     */
    protected $prev;

    /**
     * @var array Next items
     */
    protected $next;

    /**
     * @var array All used bases of object
     */
    protected $bases;


    /**
     * Adds element as current item
     *
     * @param integer $key
     * @param \RKW\RkwSearch\TreeTagger\TreeTaggerRecord
     */
    public function setCur($key, \RKW\RkwSearch\TreeTagger\TreeTaggerRecord $item) {

        if ($this->checkData($item, 'cur', $key)) {
            $this->cur[$key] = $item;
            $this->bases[] = $item->getBase();
        }
    }



    /**
     * Gets current element
     *
     * @returns array
     */
    public function getCur() {

        if (
            ($this->cur)
            && (is_array($this->cur))
        )
            return $this->cur;
            //===

        return array();
        //===
    }



    /**
     * Adds element as previous item
     *
     * @param integer $key
     * @param \RKW\RkwSearch\TreeTagger\TreeTaggerRecord  $item
     */
    public function setPrev($key, \RKW\RkwSearch\TreeTagger\TreeTaggerRecord $item) {

        if ($this->checkData($item, 'prev', $key)) {
            $this->prev[$key] = $item;
            $this->bases[] = $item->getBase();
        }
    }



    /**
     * Gets previous items
     *
     * @return array
     */
    public function getPrev() {

        if (
            ($this->prev)
            && (is_array($this->prev))
        ) {
            ksort($this->prev);
            return $this->prev;
            //===
        }

        return array();
        //===
    }


    /**
     * Adds element as next item
     *
     * @param integer $key
     * @param \RKW\RkwSearch\TreeTagger\TreeTaggerRecord  $item
     */
    public function setNext($key, \RKW\RkwSearch\TreeTagger\TreeTaggerRecord $item) {

        if ($this->checkData($item, 'next', $key)) {
            $this->next[$key] = $item;
            $this->bases[] = $item->getBase();
        }
    }



    /**
     * Gets previous items
     *
     * @return array
     */
    public function getNext() {

        if (
            ($this->next)
            && (is_array($this->next))
        ){

            ksort($this->next);
            return $this->next;
            //===
        }

        return array();
        //===
    }




    /**
     * Get all used bases of object
     *
     * @returns array
     */
    public function getBases() {

        if (
            (! $this->bases)
            || (! is_array($this->bases))
        )
            return array();
            //===

        return $this->bases;
        //===
    }



    /**
     * Get last base that was added
     *
     * @returns string
     */
    public function getLastBase() {

        if (
            ($bases = $this->getBases())
            && (is_array($bases))
        )
            return $bases[intval(count($bases)-1)];
            //===


        return NULL;
        //===

    }


    /**
     * Checks if defined filters (prev and/or next) have a match
     *
     * @returns boolean
     */
    public function hasMatch () {

        $hasMatch = TRUE;

        // if match all is active we have to do some additional checks!
        if ($this->getConfigurationFilter('matchAll')) {

            // prev defined and set?
            if (
                ($this->getConfigurationFilter('prev'))
                && (count($this->getPrev()) < 1)
            )
                $hasMatch = FALSE;

            // next defined and set?
            if (
                ($this->getConfigurationFilter('next'))
                && (count($this->getNext()) < 1)
            )
                $hasMatch = FALSE;
        }

        // current set?
        if (count($this->getCur()) < 1)
            $hasMatch = FALSE;

        return $hasMatch;
        //===

    }


    /**
     * Checks data against filter rules
     *
     * @param \RKW\RkwSearch\TreeTagger\TreeTaggerRecord
     * @param string $type May be "cur", "prev" or "next"
     * @param integer $key
     * @returns boolean
     */
    public function checkData(\RKW\RkwSearch\TreeTagger\TreeTaggerRecord $item, $type, $key = NULL) {

        mb_internal_encoding('UTF-8');

        $isFiller = FALSE;
        if (! (in_array($type, array ('cur', 'prev', 'next'))))
            $type = 'cur';

        // if we get a punctuation here, we delete the elements from here
        if (
            ($key !== NULL)
            && (strpos($item->getTag(), '$') === 0)
        ) {

            // we simply unset all entries higher than $key
            // and then we go further
            $getter = 'get' . ucfirst(strtolower($type));
            foreach ($this->$getter() as  $nextKey => $nextValue) {
                if ($nextKey >= $key) {

                    $array = $this->$type;

                    // check if TreeTaggerRecord
                    if ($array[$nextKey] instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {

                        // check if in bases and delete it from there
                        if (($basesKey = array_search($array[$nextKey]->getBase(), $this->bases)) !== FALSE)
                            unset($this->bases[$basesKey]);

                        // delete it from array and set array new
                        unset($array[$nextKey]);
                        $this->$type = $array;
                    }
                }
            }

        }


        if (
            // check if configuration exists
                ($this->getConfigurationFilter($type))

                // check if tag matches tag-list or at least filler-list
                && (
                    (in_array($item->getTag(), $this->getConfigurationFilter($type)))
                    || (
                        ($this->getConfigurationFilter($type . 'Filler'))
                        && (is_array($this->getConfigurationFilter($type . 'Filler')))
                        && ($isFiller = in_array($item->getTag(), $this->getConfigurationFilter($type . 'Filler')))
                    )
                )

                // check word length, but not for fillers
                && (
                    ($isFiller)
                    || (mb_strlen($item->getWord()) >= intval($this->getConfiguration('minWordLength')))
                )

                // check ignore words
                && (
                    (! $this->getConfiguration('ignoreWords'))
                    || (
                        ($this->getConfiguration('ignoreWords'))
                        && (! in_array($item->getWord(), $this->getConfiguration('ignoreWords')))
                    )
                )

                // check ignore base words
                && (
                    (! $this->getConfiguration('ignoreBaseWords'))
                    || (
                        ($this->getConfiguration('ignoreBaseWords'))
                        && (! in_array($item->getBase(), $this->getConfiguration('ignoreBaseWords')))
                    )
                )

                // check doubles
                // for combined keywords doubles are allowed!
                && (
                    (! $this->getConfiguration('ignoreDoubles'))
                    || (
                        ($this->getConfiguration('ignoreDoubles'))
                        && (
                            (
                                (! $this->getConfigurationFilter('combine'))
                                && (! in_array($item->getBase(), $this->getBases()))
                            )
                            || ($this->getConfigurationFilter('combine'))

                        )
                    )
                )

                // check cardinal numbers
                && (
                    (! $this->getConfiguration('ignoreCardinalNumbers'))
                    || (
                        ($this->getConfiguration('ignoreCardinalNumbers'))
                        && ($item->getTag() != 'CARD')
                        && ($item->getBaseRaw() != '@card@')
                    )
                )
        ) {

            // fillers are only allowed if they are not the first item in the list
            // (the next-array is filled inverted)
            $getter = 'get' . ucfirst(strtolower($type));
            if (
                (
                    ($isFiller)
                    && (in_array($type, array('prev' , 'next')))
                    && (count($this->$getter()) > 0)
                )
                || (! $isFiller)
            )


                return TRUE;
            //===

        }

        return FALSE;
        //===
    }


    /**
     * Get combined bases
     *
     * @param string $separator
     * @returns array
     */
    public function getKeywords ($separator = ' ') {

        mb_internal_encoding('UTF-8');

        $noWeight = ($this->getConfigurationFilter('noWeight') ? TRUE : FALSE);

        // check which kind of keyword building is desired
        if ($this->getConfigurationFilter('combineKeywords')) {

            // add all previous and all next together and add them to the current
            $allCombined = array ();
            $allCombinedTemp = array (
                'base' => '',
                'variation' => '',
                'count' => 1,
                'prevMaxDistance' => 0,
                'curPosition' => 0,
                'distance' => 0,
                'length' => 0,
                'tags' => ''
            );

            // first we add all prevs
            foreach ($this->getPrev() as $prevItemDistance => $prevItem) {
                if ($prevItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {

                    $word = $prevItem->getWord();
                    if (in_array($prevItem->getTag(), $this->getConfiguration('alwaysUseBase')))
                        $word = $prevItem->getBase();

                    // combined prev
                    $allCombinedTemp['base'] = $this->getPreparedString($this->getBaseSingle($prevItem), $allCombinedTemp['base'],  TRUE, $separator);
                    $allCombinedTemp['variation'] = $this->getPreparedString($word, $allCombinedTemp['variation'], FALSE, $separator);
                    $allCombinedTemp['tags'] = $this->getPreparedString($prevItem->getTag(), $allCombinedTemp['tags'], FALSE, $separator);
                    $allCombinedTemp['distance'] += $prevItemDistance;
                    $allCombinedTemp['length']++;

                    if ($prevItemDistance > $allCombinedTemp['prevMaxDistance'])
                        $allCombinedTemp['prevMaxDistance'] = intval($prevItemDistance);

                }
            }

            // now add all currents (should be only one)
            foreach ($this->getCur() as $curItemDistance => $curItem) {
                if ($curItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {

                    $word = $curItem->getWord();
                    if (in_array($curItem->getTag(), $this->getConfiguration('alwaysUseBase')))
                        $word = $curItem->getBase();

                    // combined cur
                    $allCombinedTemp['base'] = $this->getPreparedString($allCombinedTemp['base'], $this->getBaseSingle($curItem), TRUE, $separator);
                    $allCombinedTemp['variation'] = $this->getPreparedString($allCombinedTemp['variation'], $word, FALSE, $separator);
                    $allCombinedTemp['tags'] = $this->getPreparedString($allCombinedTemp['tags'], $curItem->getTag(), FALSE, $separator);
                    $allCombinedTemp['length']++;

                    if (! $allCombinedTemp['curPosition'])
                        $allCombinedTemp['curPosition'] = intval($curItemDistance);

                }
            }

            // last we add all nexts
            foreach ($this->getNext() as $nextItemDistance => $nextItem) {
                if ($nextItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {

                    $word = $nextItem->getWord();
                    if (in_array($nextItem->getTag(), $this->getConfiguration('alwaysUseBase')))
                        $word = $nextItem->getBase();

                    // combined prev
                    $allCombinedTemp['base'] = $this->getPreparedString($allCombinedTemp['base'], $this->getBaseSingle($nextItem), TRUE, $separator);
                    $allCombinedTemp['variation'] = $this->getPreparedString($allCombinedTemp['variation'], $word, FALSE, $separator);
                    $allCombinedTemp['tags'] = $this->getPreparedString($allCombinedTemp['tags'], $nextItem->getTag(), FALSE, $separator);
                    $allCombinedTemp['distance'] += $nextItemDistance;
                    $allCombinedTemp['length']++;
                }
            }

            // finally build result array
            if ($allCombinedTemp['base'])
                $allCombined[$this->getPreparedString($allCombinedTemp['base'], NULL, TRUE, $separator)] = array (
                    'variations' => array (
                        $this->getPreparedString($allCombinedTemp['variation'], NULL, TRUE, $separator) => $this->getPreparedString($allCombinedTemp['variation'], NULL, FALSE, $separator)
                    ),
                    'count' => 1,
                    'position' => intval($allCombinedTemp['curPosition'] - $allCombinedTemp['prevMaxDistance']),
                    'distance' => $allCombinedTemp['distance'],
                    'length' => $allCombinedTemp['length'],
                    'tags' => $allCombinedTemp['tags'],
                    'type' => 'combined',
                    'noWeight' => $noWeight
                );

            return $allCombined;
            //===

        }

        //=====================================================================

        $currentSingle = array();
        $prevCurrentCombined = array();
        $nextCurrentCombined = array ();
        $prevCurrentNextCombined = array ();

        foreach ($this->getCur() as $curItemPos => $curItem) {
            if ($curItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {
                foreach ($this->getBase($curItem) as $curBase) {


                    // add data for current item
                    $currentSingle[mb_strtolower($curBase)] = array (
                        'variations' => array (
                            $this->getPreparedString($curItem->getWord(), NULL, TRUE, $separator) => $this->getPreparedString($curItem->getWord(), NULL, FALSE, $separator),
                        ),
                        'count' => 1,
                        'position' => $curItemPos,
                        'distance' => 0,
                        'length' => 1,
                        'tags' => $this->getPreparedString($curItem->getTag(), NULL, FALSE, $separator),
                        'type' => 'default',
                        'noWeight' => $noWeight
                    );

                    // now we combine the current with the prev-items
                    foreach ($this->getPrev() as $prevItemDistance => $prevItem) {
                        if ($prevItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {
                            foreach ($this->getBase($prevItem) as $prevBase) {

                                // combined prev
                                $prevCurrentCombined[$this->getPreparedString($prevBase, $curBase, TRUE, $separator)] = array (
                                    'variations' => array (
                                        $this->getPreparedString($prevItem->getWord(), $curItem->getWord(), TRUE, $separator) => $this->getPreparedString($prevItem->getWord(), $curItem->getWord(), FALSE, $separator)
                                    ),
                                    'count' => 1,
                                    'position' => intval($curItemPos - $prevItemDistance),
                                    'distance' => intval($prevItemDistance),
                                    'length' => 2,
                                    'tags' => $this->getPreparedString($prevItem->getTag(), $curItem->getTag()),
                                    'type' => 'default',
                                    'noWeight' => $noWeight
                                );
                            }
                        }
                    }


                    // now we combine the current with the next-items
                    foreach ($this->getNext() as $nextItemDistance => $nextItem) {
                        if ($nextItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {
                            foreach ($this->getBase($nextItem) as $nextBase) {
                                $nextCurrentCombined[$this->getPreparedString($curBase, $nextBase, TRUE, $separator)] = array (
                                    'variations' => array (
                                        $this->getPreparedString($curItem->getWord(), $nextItem->getWord(), TRUE, $separator) => $this->getPreparedString($curItem->getWord(), $nextItem->getWord(), FALSE, $separator)
                                    ),
                                    'count' => 1,
                                    'position' => intval($curItemPos),
                                    'distance' => intval($nextItemDistance),
                                    'length' => 2,
                                    'tags' => $this->getPreparedString($curItem->getTag(), $nextItem->getTag(), FALSE, $separator),
                                    'type' => 'default',
                                    'noWeight' => $noWeight
                                );
                            }
                        }
                    }
                }
            }
        }

        // now do the three word combinations by using the prev-combinations and add the next-items to it
        foreach ($prevCurrentCombined as $combinedBase => $combinedData) {

            foreach ($this->getNext() as $nextItemDistance => $nextItem) {

                if ($nextItem instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {
                    foreach ($this->getBase($nextItem) as $nextBase) {
                        $prevCurrentNextCombined[$this->getPreparedString($combinedBase, $nextBase, TRUE, $separator)] = array (
                            'variations' => array (
                                $this->getPreparedString($combinedData['variations'], $nextItem->getWord(), TRUE, $separator) => $this->getPreparedString($combinedData['variations'], $nextItem->getWord(), FALSE, $separator)
                            ),
                            'count' => 1,
                            'position' => intval($combinedData['position']),
                            'distance' => intval($combinedData['distance'] + $nextItemDistance),
                            'length' => 3,
                            'tags' => $this->getPreparedString($combinedData['tags'], $nextItem->getTag(), FALSE, $separator),
                            'type' => 'default',
                            'noWeight' => $noWeight
                        );
                    }
                }
            }
        }

        return array_merge ($currentSingle, $prevCurrentCombined, $nextCurrentCombined, $prevCurrentNextCombined);
        //===

    }

    /**
     * Returns prepared word
     *
     * @param string|array $wordOne
     * @param string|array $wordTwo
     * @param boolean $strToLower If set to TRUE all strings will be returned as lower-case
     * @param string $separator
     *
     * @return string
     */
    public function getPreparedString ($wordOne, $wordTwo = '', $strToLower = FALSE, $separator = ' ') {

        if (is_array($wordOne))
            $wordOne = $wordOne[key($wordOne)];

        if (is_array($wordTwo))
            $wordTwo = $wordTwo[key($wordTwo)];

        // strToLower if needed
        if ($strToLower) {
            mb_internal_encoding('UTF-8');
            $wordOne = mb_strtolower($wordOne);
            $wordTwo = mb_strtolower($wordTwo);
        }

        // if given, combine basic form and the variation of the prior word with new one
        if (
            (mb_strlen ($wordOne) > 0)
            && (mb_strlen ($wordTwo) > 0)
        )
            return $wordOne . $separator . $wordTwo;
            //====

        if (mb_strlen ($wordTwo) > 0)
            return $wordTwo;
            //===

        return $wordOne;
        //===
    }



    /**
     * Returns basic forms of given word as array
     *
     * @param \RKW\RkwSearch\TreeTagger\TreeTaggerRecord $record
     * @return array
     */
    protected function getBase ($record) {

        // check if there is one or multiple matches for the base name
        $base = $record->getBase();
        if (! is_array($base))
            $base = array ($record->getBase());

        return $base;
        //===

    }

    /**
     * Returns basic forms of given word as string. If base is not unique we return the word
     *
     * @param \RKW\RkwSearch\TreeTagger\TreeTaggerRecord $record
     * @return string
     */
    protected function getBaseSingle ($record) {

        // check if there is one or multiple matches for the base name
        $base = $record->getBase();
        if (is_array($base))
            $base = $record->getWord();

        return $base;
        //===

    }


}