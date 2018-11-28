<?php
namespace RKW\RkwSearch\OrientDb\Domain\Repository;

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
 * Interface RepositoryInterface
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 * @see \TYPO3\CMS\Extbase\Persistence\OrientDb\Repository
 */

interface RepositoryInterface extends \TYPO3\CMS\Extbase\Persistence\RepositoryInterface {


    /**
     * Sets all relations (edges) of given object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param array $relationFields Array of fields to relate
     * @param array $relationFieldsSuccess Array of fields that have been related successfully
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @throws \RKW\RkwSearch\StorageRelationException
     * @api
     */
    public function relateAll($object, $relationFields = array (), &$relationFieldsSuccess = array ());


    /**
     * Sets relation (edge) from one to another object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The source object to relate from
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $objectTwo The target object to relate to
     * @param string $field Field to relate with
     * @param string $class OrientDbClass to relate with
     * @param float $weight Weight of Edge
     * @param boolean $invert Inverts edge-direction
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @throws \RKW\RkwSearch\StorageException
     * @api
     */
    public function relate($object, $objectTwo, $field = NULL, $class = NULL, $weight = 0.0, $invert = FALSE);


    /**
     * Unset all existing edges of given field
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param string $field Name of field
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @throws \RKW\RkwSearch\StorageException
     * @api
     */
    public function unrelateAllByField($object, $field);


    /**
     * Unset all existing edges
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param string $edgeClass EdgeClass to look for
     * @param string $targetVertexClass VertexClass to look for
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @throws \RKW\RkwSearch\StorageException
     * @api
     */
    public function unrelateAll ($object, $edgeClass, $targetVertexClass);


    /**
     * Replaces an existing object with the same identifier by the given object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $modifiedObject The object to add
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function update($modifiedObject);


    /**
     * Removes an object from this repository.
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function remove($object);


    /**
     * Removes all objects of this repository as if remove() was called for
     * all of them.
     *
     * @return void
     * @api
     */
    public function removeAll();


    /**
     * Adds an object to this repository.
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param boolean $forceInsert Forces insertion
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function add($object, $forceInsert = FALSE);


    /**
     * Removes the entries from the import tables
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to reset
     * @return boolean
     * @api
     */
    public function resetImport($object);




    /**
     * Returns all objects of this repository.
     *
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @api
     */
    public function findAll();


    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface|NULL
     * @api
     */
    public function findByUid($uid);


    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid Page uid
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function findByQueryFactory($uid = 0);


    /**
     * Finds related documents based on uid and QueryFactory
     *
     * @param integer|\RKW\RkwBasics\Domain\Model\Department $identifier Page uid or \RKW\RkwBasics\Domain\Model\Department
     * @param float $tolerance Tolerance factor for keywords that should be taken into account. Everything below this value will be ignored
     * @param float $itemsPerHundredSigns Sets the limit according to a calculation of items per hundred signs of the loaded text
     * @param integer $minItems Minimum number of items
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @api
     */
    public function findRelatedByQueryFactory($identifier, $tolerance, $itemsPerHundredSigns = 0, $minItems = 0);


    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface|NULL
     * @api
     */
    public function findByRid($uid);


    /**
     * Checks if relation-field is set
     *
     * @param string $field
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     */
    public function hasRelationField($field);


    /**
     * Checks if edge-class is defined
     *
     * @param string $edgeClass
     * @return boolean
     */
    public function hasRelationEdgeClass($edgeClass);


    /**
     * Returns all relation fields of current class
     *
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRelationFields();


    /**
     * Returns mm-table of given field for relation
     *
     * @param string $field
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRelationMmTable($field);


    /**
     * Returns foreign-table of given field for relation
     *
     * @param string $field
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRelationForeignTable($field);


    /**
     * Returns edge-class of given field for relation
     *
     * @param string $field
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRelationEdgeClass($field);


    /**
     * Returns vertex-class of given field for relation
     *
     * @param string $field
     * @return string
     */
    public function getRelationVertexClass($field);


    /**
     * Returns the rootline pages of the current object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRootlineRelationUids($object);


    /**
     * Returns the OrientDB class name
     *
     * @return string
     */
    public function getOrientDbClass();



    /**
     * Returns the TYPO3 table name
     *
     * @return string
     */
    public function getTypo3Table();


    /**
     * Returns the model name
     *
     * @return string
     */
    public function getObjectType();


    /**
     * Get the corresponding mapping record from the mapping table
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object
     * @return $this
     * @throws \RKW\RkwSearch\Exception
     */
    public function getMappingRecordByObject($object);


    /**
     * Returns mapping table repository
     *
     * @return \RKW\RkwSearch\Domain\Repository\RidMappingRepository
     */
    public function getMappingTableRepository();


    /**
     * Returns mapping table
     *
     * @return \RKW\RkwSearch\Domain\Model\RidMapping
     */
    public function getMappingTableModel();


    /**
     * Returns the query factory
     *
     * @return \RKW\RkwSearch\Search\QueryFactory
     * @throws \RKW\RkwSearch\Exception
     */
    public function getQueryFactory();


    /**
     * Sets the query factory
     *
     * @param \RKW\RkwSearch\Search\QueryFactory $queryFactory
     */
    public function setQueryFactory(\RKW\RkwSearch\Search\QueryFactory $queryFactory);

    /**
     * Returns persistence manager
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    public function getPersistenceManager();


    /**
     * Returns the cache object
     *
     * @return \RKW\RkwSearch\OrientDb\Cache\RepositoryCache
     */
    public function getCache();


    /**
     * Gets the TYPO3 database object.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public function getTypo3Database();

    /**
     * Returns the OrientDb database object
     *
     * @return \RKW\RkwSearch\OrientDb\Storage\Database\DatabaseInterface
     */
    public function getOrientDbDatabase();


    /**
     * Get TypoScript configuration
     *
     * @return array
     */
    public function getTypoScriptConfiguration();


    /**
     * Function to return the current TYPO3_MODE.
     * This function can be mocked in unit tests to be able to test frontend behaviour.
     *
     * @return string
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    public function getEnvironmentMode();


    /**
     * Set the cache option for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setClearCache($value);

    /**
     * Set the delete mode for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setDeleteHard($value);

    /**
     * Set the debug mode for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setDebugMode($value);
}
?>