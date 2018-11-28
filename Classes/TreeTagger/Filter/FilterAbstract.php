<?php
namespace RKW\RkwSearch\TreeTagger\Filter;
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
 * Class FilterAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

abstract class FilterAbstract implements FilterInterface {

    /**
     * @var \RKW\RkwSearch\TreeTagger\Collection\Records Contains the results from TreeTagger
     */
    protected $data;


    /**
     * @var array The filtered data as array
     */
    protected $results;


    /**
     * @var array Contains the TypoScript settings for the filter
     */
    protected $settings;



    /**
     * Returns the filtered data
     *
     * @return \RKW\RkwSearch\TreeTagger\Collection\FilteredRecords
     */
    public function execute() {

        $this->executeSub();
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\TreeTagger\\Collection\\FilteredRecords', $this->results);
        //===
    }



    /**
     * Filters the data by defined tag-combinations
     *
     * @return void
     */
    protected function executeSub() {
        // does nothing here - has to be extended
    }


    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     */
    public function getConfiguration($key = NULL) {

        $className = strtolower(Common::getShortName($this));
        if (
            (! $this->settings)
            || (! $this->settings[$className])
        )
            throw new \RKW\RkwSearch\Exception('No valid configuration found.', 1422347731);
            //===

        if ($key) {

            if ($this->settings[$className][$key])
                return $this->settings[$className][$key];
                //===

            return NULL;
            //===

        }

        return $this->settings[$className];
        //===

    }


    /**
     * Initialize of instance
     *
     * @param \RKW\RkwSearch\TreeTagger\Collection\Records $data Contains the results from TreeTagger
     * @param array $configuration Configuration for filter
     */
    public function __construct( \RKW\RkwSearch\TreeTagger\Collection\Records $data, $configuration) {

        $this->data = $data;

        // set given filter
        $this->settings = $configuration;

        // load settings if not already set
        $this->getConfiguration();

    }



}