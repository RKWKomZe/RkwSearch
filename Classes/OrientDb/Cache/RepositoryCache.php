<?php
namespace RKW\RkwSearch\OrientDb\Cache;

/**
 * @toDo: We need this dirty hack here since otherwise the cache crashes with "__PHP_Incomplete_Class" for OrientDbRecord
 */
// require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('rkw_search') . 'Classes/Libs/OrientDB-PHP/OrientDB/OrientDBRecord.php');
// require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('rkw_search') . 'Classes/Libs/OrientDB-PHP/OrientDB/OrientDB.php');

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
 * Class RepositoryCache
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class RepositoryCache extends \RKW\RkwSearch\OrientDb\Cache\CacheAbstract {


    /**
     * sets cache identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier) {

        if ($identifier instanceof \RKW\RkwSearch\OrientDb\Storage\Query\Query)
            $identifier = $identifier->getRaw();

        return parent::setIdentifier($identifier);
        //===
    }



}