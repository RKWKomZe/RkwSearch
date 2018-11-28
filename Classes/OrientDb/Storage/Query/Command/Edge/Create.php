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
 * Class Create
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class Create extends \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge implements \RKW\RkwSearch\OrientDb\Storage\Query\Command\EdgeInterface {


    /**
     * Sets the $class use
     *
     * @param string $class
     */
    public function __construct($class) {

        parent::__construct();

        $this->setToken('Class', $class);

    }


    /**
     * Returns the SQL schema
     *
     * @return string
     */
    protected function getSchema()  {

        return "CREATE EDGE :Class from :sId TO :tId :Inserts";
        //===

    }


    /**
     * Sets the outpoint of the edge
     *
     * @param string $target
     * @return $this
     */
    public function fromVertex($target) {

        $this->setToken('sId', $target);

        return $this;
        //===
    }


    /**
     * Sets the inpoint of the edge
     *
     * @param string $target
     * @return $this
     */
    public function toVertex($target) {

        $this->setToken('tId', $target);

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
    public function set(array $values, $append = true) {

        $this->setTokenValues('Inserts', $values, $append);
        return $this;
        //===
    }



    /**
     * Returns the formatters for this query's tokens
     *
     * @return array
     */
    protected function getTokenFormatters(){

        return array_merge(
            parent::getTokenFormatters(),
            array(
                'sId' => "Doctrine\OrientDB\Query\Formatter\Query\Rid",
                'tId' => "Doctrine\OrientDB\Query\Formatter\Query\Rid",
                'Inserts' => "RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\Set",
            )
        );
        //===

    }



}
