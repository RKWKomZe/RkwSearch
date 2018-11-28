<?php
namespace RKW\RkwSearch\TreeTagger\Filter\FilterItem;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Class FilterItemAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

abstract class FilterItemAbstract implements FilterItemInterface {


    /**
     * @var array Contains the TypoScript settings for the filter
     */
    protected $settings;


    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @return mixed|array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getConfiguration($key = NULL) {

        if (! $this->settings)
            throw new \RKW\RkwSearch\Exception('No valid configuration found.', 1436198295);
            //===

        if ($key) {

            if ($this->settings[$key]) {

                // just generate an array from it if it is not numeric or boolean
                $result = $this->settings[$key];
                if (
                    (! is_numeric($this->settings[$key]))
                    && (! is_bool($this->settings[$key]))
                )
                    $result = GeneralUtility::trimExplode(',', $this->settings[$key], TRUE);

                return $result;
                //===
            }

            return array();
            //===

        }

        return $this->settings;
        //===

    }


    /**
     * Get TypoScript configuration for subfilter
     *
     * @param string $key The item-key to return
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     */
    public function getConfigurationFilter($key = NULL) {

        if (! $this->settings['filter'])

            throw new \RKW\RkwSearch\Exception('No valid configuration found.', 1436200027);
            //===

        if ($key) {

            if ($this->settings['filter'][$key]) {

                // just generate an array from it if it is not numeric or boolean
                $result = $this->settings['filter'][$key];
                if (
                    (! is_numeric($this->settings['filter'][$key]))
                    && (! is_bool($this->settings['filter'][$key]))
                )
                    $result = GeneralUtility::trimExplode(',', $this->settings['filter'][$key], TRUE);


                return $result;
                //===
            }

            return NULL;
            //===

        }

        return $this->settings['filter'];
        //===

    }



    /**
     * Initialize of instance
     *
     * @param array $globalConfiguration Configuration of distance filter
     * @param array $filterConfiguration Configuration for item filter
     */
    public function __construct($globalConfiguration, $filterConfiguration) {

        // set given filter
        $this->settings = $globalConfiguration;

        // set subfilter
        if ($this->settings)
            $this->settings['filter'] = $filterConfiguration;

    }



}