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
 * Class Query
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class Query extends \Doctrine\OrientDB\Query\Query implements QueryInterface {


    /**
     * Custom Commands
     */
    protected $customCommands = array(
        'select'                => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Select',
        'createEdge'            => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\Create',
        'updateEdge'            => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\Update',
        'deleteEdgeAll'         => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\DeleteAll',
        'deleteEdgeAllFromId'   => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\DeleteAllFromId',
        'selectInEdge'          => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\SelectIn',
        'selectOutEdge'         => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\SelectOut',
        'createVertex'          => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Create',
        'updateVertex'          => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Update',
        'updateVertexSingle'    => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\UpdateSingle',
        'deleteVertex'          => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Delete',
        'deleteVertexSingle'    => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\DeleteSingle',
        'selectSingle'          => 'RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\SelectSingle',
    );


    /**
     * Builds a query with the given $command on the given $target.
     *
     * @param array $target
     * @param array $commands
     */
    public function __construct(array $target = array(), array $commands = array()) {
        $this->setCommands($this->customCommands);
        parent::__construct($target, $commands);

    }


    /**
     * Converts the query into an CREATE EDGE
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge\Create
     */
    public function createEdge($class) {

        $commandClass = $this->getCommandClass('createEdge');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }

    /**
     * Converts the query into an CREATE EDGE
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge\Update
     */
    public function updateEdge($class) {

        $commandClass = $this->getCommandClass('updateEdge');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }

    /**
     * Deletes all edges of class
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge\Create
     */
    public function deleteEdgeAllFromId($class) {

        $commandClass = $this->getCommandClass('deleteEdgeAllFromId');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }


    /**
     * Deletes all edges between vertexes
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge\Create
     */
    public function deleteEdgeAll($class) {

        $commandClass = $this->getCommandClass('deleteEdgeAll');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }




    /**
     * Selects all in edges of given class
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge\SelectIn
     */
    public function selectInEdge($class) {

        $commandClass = $this->getCommandClass('selectInEdge');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }

    /**
     * Selects all in edges of given class
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Edge\SelectOut
     */
    public function selectOutEdge($class) {

        $commandClass = $this->getCommandClass('selectOutEdge');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }


    /**
     * Converts the query into an CREATE VERTEX.
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\Create
     */
    public function createVertex($class) {

        $commandClass = $this->getCommandClass('createVertex');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }


    /**
     * Converts the query into an UPDATE VERTEX
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\Update
     */
    public function updateVertex($class) {

        $commandClass = $this->getCommandClass('updateVertex');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }


    /**
     * Converts the query into an UPDATE VERTEX
     *
     * @param string $rid
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\Update
     */
    public function updateVertexSingle($rid) {

        $commandClass = $this->getCommandClass('updateVertexSingle');
        $this->command = new $commandClass($rid);

        return $this->command;
        //===
    }


    /**
     * Converts the query into an DELETE VERTEX
     *
     * @param string $class
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\Delete
     */
    public function deleteVertex($class) {

        $commandClass = $this->getCommandClass('deleteVertex');
        $this->command = new $commandClass($class);

        return $this->command;
        //===
    }


    /**
     * Converts the query into an DELETE VERTEX
     *
     * @param string $rid
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\DeleteSingle
     */
    public function deleteVertexSingle($rid) {

        $commandClass = $this->getCommandClass('deleteVertexSingle');
        $this->command = new $commandClass($rid);

        return $this->command;
        //===
    }


    /**
     * Selects one element by rid
     *
     * @param string $rid
     * @return \RKW\RkwSearch\OrientDb\Storage\Query\Command\Vertex\SelectSingle
     */
    public function selectSingle($rid) {

        $commandClass = $this->getCommandClass('selectSingle');
        $this->command = new $commandClass($rid);

        return $this->command;
        //===
    }



    /**
     * Adds a from clause to the query and uses a subquery for that.
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     */
    public function fromQuery($query) {

        return $this->command->fromQuery($query);
        //===
    }

    /**
     * Resets Subquery-Token
     *
     */
    public function resetFromQuery() {

        return $this->command->resetFromQuery();
        //===
    }



    /**
     * Groups the query.
     *
     * @param array   $order
     * @param boolean $append
     * @param boolean $first
     */
    public function groupBy($order, $append = true, $first = false) {

        return $this->command->groupBy($order, $append, $first);
        //====
    }


    /**
     * Returns the raw SQL query statement with replaced eszett
     *
     * @return string
     */
    public function getRaw() {

        /** @toDo: Fix for current OrientDb-Branch, see https://github.com/orientechnologies/orientdb/issues/3345  */
        $query = str_replace('ß', 'ss', $this->command->getRaw());
        return $query;
        //===

        /**  @toDo: Problems with new lines, see https://github.com/orientechnologies/orientdb/issues/4837 */
        // return preg_replace("/\r\n|\r|\n/", '\r\n', $query);
        //===
    }


}
