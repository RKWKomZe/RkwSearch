<?php
namespace RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\Search;

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
 * Class Keyword
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class Keyword extends \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex implements \RKW\RkwSearch\OrientDb\Storage\Query\Command\VertexInterface {


    /**
     * Sets the basic values
     *
     * @param string $keywordVertex
     * @param string $keywordEdge
     * @param string $keywordOrderBy
     */
    public function __construct($keywordVertex, $keywordEdge, $keywordOrderBy = NULL) {

        parent::__construct();
        $this->setToken('KeywordVertex', $keywordVertex);
        $this->setToken('KeywordEdge', $keywordEdge);

        if ($keywordOrderBy)
            $this->setToken('KeywordOrderBy', $keywordOrderBy);
    }


    /**
     * Returns the SQL schema
     *
     * @return string
     */
    protected function getSchema() {

        return "SELECT :Projections FROM (
            SELECT EXPAND(inV()) FROM (
                SELECT EXPAND(outE(:KeywordEdge)) FROM :KeywordVertex :KeywordWhere :KeywordOrderBy
            )
        ) :Where GROUP BY @rid :OrderBy :Skip :Limit ";
        //===
    }




    /**
     * Deletes all the WHERE and BETWEEN conditions in the current SELECT.
     *
     * @return true
     * @see \Doctrine\OrientDB\Query\Command\Select
     */
    public function resetWhere()
    {
        parent::resetWhere();
        $this->clearToken('Between');
        return TRUE;
        //===
    }

    /**
     * Sets the fields to select.
     *
     * @param array $projections
     * @param boolean $append
     * @return $this
     * @see \Doctrine\OrientDB\Query\Command\Select
     */
    public function select(array $projections, $append = true)
    {
        $this->setTokenValues('Projections', $projections, $append);
        return $this;
        //===
    }


    /**
     * Orders the query.
     *
     * @param array $order
     * @param boolean $append
     * @param boolean $first
     * @return $this
     * @see \Doctrine\OrientDB\Query\Command\Select
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
     * @see \Doctrine\OrientDB\Query\Command\Select
     */
    public function limit($limit) {

        $this->setToken('Limit', (int) $limit);
        return $this;
        //===
    }


    /**
     * Sets the number of records to skip.
     *
     * @param integer $records
     * @return $this
     * @see \Doctrine\OrientDB\Query\Command\Select
     */
    public function skip($records) {

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
            'Projections'       => "Doctrine\OrientDB\Query\Formatter\Query\Select",
            'KeywordEdge'       => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'KeywordVertex'     => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'KeywordOrderBy'    => "Doctrine\OrientDB\Query\Formatter\Query\OrderBy",
            'KeywordWhere'      => "Doctrine\OrientDB\Query\Formatter\Query\Where",
            'OrderBy'           => "Doctrine\OrientDB\Query\Formatter\Query\OrderBy",
            'Limit'             => "Doctrine\OrientDB\Query\Formatter\Query\Limit",
            'Skip'              => "Doctrine\OrientDB\Query\Formatter\Query\Skip",
        ));
        //===
    }
}
