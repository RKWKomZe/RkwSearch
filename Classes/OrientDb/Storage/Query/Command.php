<?php
namespace RKW\RkwSearch\OrientDb\Storage\Query;

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
 * Class Command
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

abstract class Command extends \Doctrine\OrientDB\Query\Command {


    /**
     * Adds a from clause to the query and uses a subquery for that.
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     */
    public function fromQuery($query) {

        return $this->fromQuery($query);
        //===
    }


    /**
     * Resets Subquery-Token
     *
     */
    public function resetFromQuery() {

        return $this->resetFromQuery();
        //===
    }

}