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
 * Class FiltersInterface
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

interface FiltersInterface  {

    /**
     * Returns language uid
     *
     * @returns integer
     */
    public function getLanguageUid ();


    /**
     * Returns the data prepared for query
     *
     * @returns array
     */
    public function getDataPrepared ();


    /**
     * Returns the raw data
     *
     * @returns \RKW\RkwSearch\TreeTagger\Collection\Records|array
     */
    public function getData ();


    /**
     * Adds the filter to the given query
     *
     * @returns boolean
     */
    public function getFilter ();


}