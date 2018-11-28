<?php
namespace RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge;

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
 * Class SelectOut
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class SelectOut extends \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge {


    /**
     * Sets the $rid to look for
     *
     * @param string $edgeClass
     */
    public function __construct($edgeClass) {

        parent::__construct();
        $this->setToken('EdgeClass', $edgeClass);

    }


    /**
     * Returns the SQL schema
     *
     * @return string
     */
    protected function getSchema() {

        return "SELECT expand(outE(:EdgeClass)) FROM :Target :Where :OrderBy :Skip :Limit";
        //===
    }


    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters() {

        return array_merge(parent::getTokenFormatters(), array(
            'EdgeClass' => "Doctrine\OrientDB\Query\Formatter\Query\Target",
        ));
        //===
    }
}
