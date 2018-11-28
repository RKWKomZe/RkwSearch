<?php
namespace RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex;

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
 * Class Update
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */


class Update extends \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex implements \RKW\RkwSearch\OrientDb\Storage\Query\Command\VertexInterface {

    /**
     * Builds a new statement, setting the $class.
     *
     * @param string $rid
     */
    public function __construct($rid)  {

        parent::__construct();
        $this->setToken('Class', $rid);
        $this->setToken('ActionOne', 'SET');
        $this->setToken('ActionTwo', '');
    }


    /**
     * Returns the SQL schema
     *
     * @return string
     */
    protected function getSchema() {
        /**
         * @toDo: Not yet supported by OrientDb, but planed to integrate
         */
        // return "UPDATE VERTEX :Class SET :Updates :Where";
        return "UPDATE :Class :ActionOne :UpdatesOne :ActionTwo :UpdatesTwo :Where";
        //===
    }


    /**
     * Set the $values of the updates to be done.
     * You can $append the values.
     *
     * @param array $values
     * @param boolean $append
     * @return $this
     */
    public function set(array $values, $append = true) {

        $this->setTokenValues('UpdatesOne', $values, $append);
        return $this;
        //===
    }


    /**
     * Set the $values of the updates to be done.
     * You can $append the values.
     *
     * @param array $values
     * @param boolean $append
     * @return $this
     */
    public function increment(array $values, $append = true) {

        $this->setToken('ActionTwo', 'INCREMENT');
        $this->setTokenValues('UpdatesTwo', $values, $append);
        return $this;
        //===
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters() {
        return array_merge(parent::getTokenFormatters(), array(
            'UpdatesOne'  => "Doctrine\OrientDB\Query\Formatter\Query\Updates",
            'ActionOne' => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'UpdatesTwo'  => "Doctrine\OrientDB\Query\Formatter\Query\Updates",
            'ActionTwo' => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
        ));
    }
}
