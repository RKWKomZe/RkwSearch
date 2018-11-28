<?php
namespace RKW\RkwSearch\Search\Filters;
use RKW\RkwSearch\Helper\Text;

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
 * Class  Geolocation
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class GeoLocation extends FiltersAbstract {

    /**
     * Returns conditions for the filter
     *
     * @return array
     */
    public function getFilter() {

        if ($searchClass = $this->getConfiguration('searchClass')) {

            // check if there is at least one match
            if (
                ($data = $this->getDataPrepared())
                && ($searchFieldLatitude = $this->getConfiguration('searchFieldLatitude'))
                && ($searchFieldLongitude = $this->getConfiguration('searchFieldLongitude'))
            ) {

                $selectFields = $this->getConfiguration('selectFieldsAddition') ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ();
                return array(
                    'selectFields' => array_merge($selectFields, array('distance(' . $searchFieldLatitude . ', ' . $searchFieldLongitude . ', ' . $data['latitude'] . ', ' . $data['longitude'] . ') AS distance' )),
                    'searchClass' => $searchClass,
                    'where' => '(' . $searchFieldLatitude . ' > 0 AND ' . $searchFieldLongitude . ' > 0)',
                    'orderBy' => (($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array()),
                );
                //===
            }

            return array(
                'searchClass' => $searchClass,
            );
            //===
        }

        return array();
        //===
    }


    /**
     * Gets the prepared data to run through
     *
     * @returns array
     */
    public function getDataPrepared () {

        // prepare string
        $searchString = trim(Text::sanitizeStringOrientDb(Text::sanitizeString($this->getData(), $this->getLanguageUid())));

        /** @var \RKW\RkwGeolocation\Service\Geolocation $geoLocation */
        $geoLocation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwGeolocation\\Service\\Geolocation');

        // check if we have an address or an zip
        if (is_numeric($searchString)) {
            $geoLocation->setPostalCode($searchString);
        } else {
            $geoLocation->setAddress($searchString);
        }

        /** @var \RKW\RkwGeolocation\Domain\Model\Geolocation $geoData */
        $geoData = $geoLocation->determineGeoData();
        if ($geoData)
            return array (
                'longitude' => floatval($geoData->getLongitude()),
                'latitude' => floatval($geoData->getLatitude())
            );
            //===

        return array ();
        //==
    }

}