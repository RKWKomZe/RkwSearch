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
 * Class FilterItemInterface
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

interface FilterItemInterface {


    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @return mixed|array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getConfiguration($key = NULL);


    /**
     * Get TypoScript configuration for subfilter
     *
     * @param string $key The item-key to return
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     */
    public function getConfigurationFilter($key = NULL);


}