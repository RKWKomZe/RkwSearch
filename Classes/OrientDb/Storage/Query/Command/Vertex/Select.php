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
 * Class Select
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class Select extends \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex  {


    /**
     * Sets the $target to select from
     *
     * @param array $projections
     * @param boolean $append
     */
    public function __construct(array $projections, $append = true) {

        parent::__construct();
        $this->setTokenValues('Projections', $projections, $append);
    }


    /**
     * Returns the SQL schema
     *
     * @return string
     */
    protected function getSchema() {

        return "SELECT :Projections FROM :Target :Subquery :Where :Between :GroupBy :OrderBy :Skip :Limit";
        //===
    }

    /**
     * Converts the "normal" select into an index one.
     * Index selects can query with the BETWEEN operator:
     * <code>select from index:name where x between 10.3 and 10.7</code>
     *
     * @param string $key
     * @param string $left
     * @param string $right
     * @return $this
     */
    public function between($key, $left, $right) {
        $this->resetWhere();
        $this->where($key);
        $this->setTokenValues('Between', array($left, $right));

        return $this;
        //===
    }

    /**
     * Sets the fields to select.
     *
     * @param array   $projections
     * @param boolean $append
     * @return $this
     */
    public function select(array $projections, $append = true) {

        $this->setTokenValues('Projections', $projections, $append);
        return $this;
    }


    /**
     * Deletes all the WHERE and BETWEEN conditions in the current SELECT.
     *
     * @return true
     */
    public function resetWhere() {
        parent::resetWhere();

        $this->clearToken('Between');

        return true;
        //===
    }


    /**
     * Adds a from clause to the query and uses a subquery for that.
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return $this
     */
    public function fromQuery($query) {

        // overrides target!
        $this->setToken('Subquery', $query);
        $this->clearToken('Target');

        return $this;
        //===
    }

    /**
     * Resets Subquery-Token
     *
     * @return $this
     */
    public function resetFromQuery() {

        $this->clearToken('Subquery');
        return $this;
        //===
    }

    /**
     * Sets the token for the from clause. You can $append your values.
     *
     * @param array   $target
     * @param boolean $append
     * @return $this
     */
    public function from(array $target, $append = true) {

        // only if target is not already set!
        if(! $this->getTokenValue('Subquery'))
            $this->setTokenvalues('Target', $target, $append);

        return $this;
        //===
    }

    /**
     * Groups the query.
     *
     * @param array   $order
     * @param boolean $append
     * @param boolean $first
     * @return $this
     */
    public function groupBy($order, $append = true, $first = false) {

        $this->setToken('GroupBy', $order, $append, $first);
        return $this;
        //===
    }

    /**
     * Orders the query.
     *
     * @param array   $order
     * @param boolean $append
     * @param boolean $first
     * @return $this
     */
    public function orderBy($order, $append = true, $first = false) {

        $this->setToken('OrderBy', $order, $append, $first);
        return $this;
        //===
    }

    /**
     * Sets a limit to the SELECT.
     *
     * @param integer $limit
     * @return $this
     */
    public function limit($limit){

        $this->setToken('Limit', (int) $limit);
        return $this;
        //===

    }

    /**
     * Sets the number of records to skip.
     *
     * @param integer $records
     * @return $this
     */
    public function skip($records){

        $this->setToken('Skip', (int) $records);
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
            'Projections' => "\RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\Select",
            'Subquery'    => "\RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\SubQuery",
            'GroupBy'     => "\RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\GroupBy",
            'OrderBy'     => "Doctrine\OrientDB\Query\Formatter\Query\OrderBy",
            'Limit'       => "Doctrine\OrientDB\Query\Formatter\Query\Limit",
            'Skip'        => "Doctrine\OrientDB\Query\Formatter\Query\Skip",
            'Between'     => "Doctrine\OrientDB\Query\Formatter\Query\Between",
        ));
        //===
    }
}
