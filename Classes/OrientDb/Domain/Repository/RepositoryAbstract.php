<?php
namespace RKW\RkwSearch\OrientDb\Domain\Repository;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\OrientDb\Helper\Tca\Typo3Fields;
use RKW\RkwSearch\OrientDb\Helper\Tca\OrientDbFields;
use RKW\RkwSearch\OrientDb\Helper\Query AS QueryHelper;
use RKW\RkwBasics\Helper\QueryTypo3 AS QueryTypo3Helper;
use \RKW\RkwSearch\OrientDb\Storage\Query\Query;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;
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
 * Class RepositoryAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 * @see \TYPO3\CMS\Extbase\Persistence\Repository
 */

abstract class RepositoryAbstract implements RepositoryInterface, \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \RKW\RkwSearch\OrientDb\Cache\RepositoryCache
     * @inject
     */
    protected $cache;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     * @inject
     */
    protected $logger;

    /**
     * @var \RKW\RkwSearch\Domain\Repository\RidMappingRepository
     * @inject
     */
    protected $mappingTableRepository;

    /**
     * @var \RKW\RkwSearch\Domain\Model\RidMapping
     * @inject
     */
    protected $mappingTableModel;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Database\DatabaseInterface
     */
    protected $orientDbDatabase;

    /**
     * @var \RKW\RkwSearch\Search\QueryFactory
     */
    protected $queryFactory;

    /**
     * @var array Contains orderings
     */
    protected $defaultOrderings;

    /**
     * @var integer Default limit
     */
    protected $defaultLimit;

    /**
     * @var string Name of the corresponding OrientDb class
     */
    protected $orientDbClass;

    /**
     * @var string Contains the object-type
     */
    protected $objectType;

    /**
     * @var array Contains the TypoScript settings
     */
    protected $settings;

    /**
     * @var array|integer Contains the last query result
     */
    protected $lastQueryResult;

    /**
     * @var boolean If set to TRUE the cache is deleted after saving to database
     */
    protected $clearCache = TRUE;

    /**
     * @var boolean If set to TRUE the objects are deleted instead of set to deleted=1
     */
    protected $deleteHard = FALSE;

    /**
     * @var boolean If set to TRUE the objects are marked with a special param
     */
    protected $debugMode = FALSE;




    /*********************************************
     * Functions
     *********************************************/

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
    public function relateAll($object, $relationFields = array (), &$relationFieldsSuccess = array ()) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1399543523);
            //===

        // load the relations of the current object
        $errors = 0;
        $count = 0;

        // check if we have a mapping model - relateAll is not possible without!
        // if we have a relation we can set automatically, we also have a mapping record!
        if (
            ($mappingRecord = $this->getMappingRecordByObject($object))
            && ($rid = $mappingRecord->getRid())
        ) {

            try {

                // be aware of language overlays via foreign tables!
                // if we have an table with an language overlay table
                // we have to take the value of it's pointer-field for our query and reset the languageUid
                $uid = $mappingRecord->getT3id();
                $localTable = $mappingRecord->getT3table();
                $languageUid = $mappingRecord->getT3lid();
                $checksums = array ();
                if ($mappingRecord->getRelationChecksums())
                    $checksums = unserialize($mappingRecord->getRelationChecksums());


                if (
                    ($languageUid > 0)
                    && (Typo3Fields::hasLanguageOriginalPointerTable($localTable))
                ){
                    $getter = 'get' . ucfirst(Common::camelize(Typo3Fields::getLanguageOriginalPointerField($localTable)));
                    $uid = $object->$getter();
                    $languageUid = 0;
                    $localTable = Typo3Fields::getLanguageOriginalPointerTable($localTable);
                }

                // check uid
                if (! $uid)
                    throw new \RKW\RkwSearch\StorageRelationException (sprintf('No valid uid given. Can not set edges for class "%s".',  $this->getOrientDbClass()), 1424702550);
                    //===

                // $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

                // now we go through all defined relation-fields and set the relations accordingly
                foreach ($this->getRelationFields($relationFields) as $field => $mapping) {

                    if (! $this->getRelationForeignTable($field))
                        throw new \RKW\RkwSearch\StorageRelationException (sprintf('Could not set edges for class "%s". No relation-mm-table or relation-foreign-table set for given field "%s".', $this->getOrientDbClass(), $field), 1418195424);
                        //===

                    $resource = NULL;
                    $record = NULL;
                    $recordArray = NULL;

                    //=======================================
                    // try to get m:n-relation
                    if ($mmTable = $this->getRelationMmTable($field)) {

                        // variant: mm-reference-table
                        if ($this->getRelationMmMatchFields($field)) {

                            // set tables
                            $foreignTable = $this->getRelationForeignTable($field);

                            // set fields to select
                            $fields = $foreignTable . '.' .
                                implode (', ' . $foreignTable . '.',
                                    array_merge(
                                        Typo3Fields::getDefaultFieldsForOrientClass($foreignTable),
                                        Typo3Fields::getVersionFields($foreignTable)
                                    )
                                );

                            // orderBy-statement
                            $orderBy = ($GLOBALS['TCA'][$mmTable]['ctrl']['sortby']) ? $mmTable . '.' . $GLOBALS['TCA'][$mmTable]['ctrl']['sortby'] . ' ASC' : '';

                            // important fields!
                            $where = QueryTypo3Helper::getWhereClauseForEnableFields($foreignTable);
                            $where .= QueryTypo3Helper::getWhereClauseForVersioning($foreignTable);
                            $where .= QueryTypo3Helper::getWhereClauseForLanguageFields($foreignTable, $languageUid);

                            // add match-fields
                            foreach ($this->getRelationMmMatchFields($field) as $mmFieldName => $mmFieldValue) {
                                if ($where)
                                    $where .= ' AND ';

                               $where .= $mmTable . '.' . $mmFieldName . ' = "' . $mmFieldValue . '"';
                            }

                            // Now build the query
                            // Foreign and localTable are exchanged here!
                            $resource = $this->getTypo3Database()->exec_SELECT_mm_query(
                                $fields,
                                $foreignTable,
                                $mmTable,
                                $localTable,
                                'AND ' . $localTable . '.uid =' . intval($uid) . $where,
                                '',
                                $orderBy
                            );

                        } else {

                            // set tables
                            $foreignTable = $this->getRelationForeignTable($field);

                            // set fields to select
                            $fields = $foreignTable . '.' .
                                implode (', ' . $foreignTable . '.',
                                    array_merge(
                                        Typo3Fields::getDefaultFieldsForOrientClass($foreignTable),
                                        Typo3Fields::getVersionFields($foreignTable)
                                    )
                                );

                            // orderBy-statement
                            $orderBy = ($GLOBALS['TCA'][$mmTable]['ctrl']['sortby']) ? $mmTable . '.' . $GLOBALS['TCA'][$mmTable]['ctrl']['sortby'] . ' ASC' : '';

                            // important fields!
                            $where = QueryTypo3Helper::getWhereClauseForEnableFields($foreignTable);
                            $where .= QueryTypo3Helper::getWhereClauseForVersioning($foreignTable);
                            $where .= QueryTypo3Helper::getWhereClauseForLanguageFields($foreignTable, $languageUid);

                            // Now build the query
                            $resource = $this->getTypo3Database()->exec_SELECT_mm_query(
                                $fields,
                                $localTable,
                                $mmTable,
                                $foreignTable,
                                'AND ' . $localTable . '.uid =' . intval($uid) . $where,
                                '',
                                $orderBy
                            );

                        }

                    //=======================================
                    // else for resources
                    // Here we only connect to meta-data of resources since the resources themselves
                    // do not have any value for the search
                    /*
                     ** @deprecated
                    } else if ($this->getRelationForeignTable($field) == 'sys_file_metadata') {

                        // set tables
                        $uidField = $this->getRelationLocalField($field);
                        $foreignTable = $this->getRelationForeignTable($field);
                        $table = 'sys_file_reference';
                        $metaDataTable = $this->getRelationForeignTable($field);

                        // fields to select
                        $fields = $foreignTable . '.' .
                            implode (', ' . $foreignTable . '.',
                                array_merge(
                                    Typo3Fields::getDefaultFieldsForOrientClass($foreignTable),
                                    Typo3Fields::getVersionFields($foreignTable)
                                )
                            );

                        // where-clause-specials of sys_file_reference-table!
                        $where = $this->getRelationForeignField($field) . '=' . intval($uid) . ' AND ' . $metaDataTable . '.file = ' . $table . '.' . $uidField;
                        if ($this->getRelationForeignTableField($field))
                            $where .= ' AND ' . $table . '.' . $this->getRelationForeignTableField($field) . '= "' . $localTable . '"';

                        foreach ($this->getRelationForeignMatchFields($field) as $fieldName => $fieldValue)
                            $where .= ' AND ' . $table . '.' . $fieldName . '= "' . $fieldValue . '"';

                        // important fields!
                        $where .= QueryTypo3Helper::getWhereClauseForEnableFields($table);
                        $where .= QueryTypo3Helper::getWhereClauseForVersioning($table);
                        $where .= QueryTypo3Helper::getWhereClauseForLanguageFields($table, $languageUid);

                        $where .= QueryTypo3Helper::getWhereClauseForEnableFields($metaDataTable);
                        $where .= QueryTypo3Helper::getWhereClauseForVersioning($metaDataTable);
                        $where .= QueryTypo3Helper::getWhereClauseForLanguageFields($metaDataTable, $languageUid);

                        // order by
                        $orderBy = NULL;
                        if ($this->getRelationForeignSortBy($field))
                            $orderBy = $table . '.' . $this->getRelationForeignSortBy($field) . ' ASC';

                        // now get records
                        $recordArray = $this->getTypo3Database()->exec_SELECTgetRows(
                            $fields,
                            $table . ',' . $metaDataTable,
                            $where,
                            '',
                            $orderBy,
                            '',
                            ''
                        );
                    */

                    //=======================================
                    // else get n:1- or 1:1-relation
                    } else {

                        // set table
                        $foreignTable = $this->getRelationForeignTable($field);

                        // fields to select
                        $fields = $foreignTable . '.' .
                            implode (', ' . $foreignTable . '.',
                                array_merge(
                                    Typo3Fields::getDefaultFieldsForOrientClass($foreignTable),
                                    Typo3Fields::getVersionFields($foreignTable)
                                )
                            );

                        // important fields!
                        $where = QueryTypo3Helper::getWhereClauseForEnableFields($foreignTable);
                        $where .= QueryTypo3Helper::getWhereClauseForVersioning($foreignTable);
                        $where .= QueryTypo3Helper::getWhereClauseForLanguageFields($foreignTable, $languageUid);

                        // orderBy
                        $orderBy = ($GLOBALS['TCA'][$foreignTable]['ctrl']['sortby']) ? $foreignTable . '.' . $GLOBALS['TCA'][$foreignTable]['ctrl']['sortby'] . ' ASC' : '';

                        $recordArray = $this->getTypo3Database()->exec_SELECTgetRows (
                            $fields,
                            $foreignTable . ',' . $localTable,
                            'FIND_IN_SET (' . $foreignTable . '.uid, ' . $localTable . '.' . $field . ') AND ' . $localTable . '.uid=' . intval($uid) . $where,
                            '',
                            $orderBy
                        );

                    }

                    // var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);

                    //=============================================
                    // check if there are some results
                    $finalRelations = array ();
                    if (
                        ($resource)
                        || ($record)
                        || ($recordArray)
                    ) {

                        // set relations for MySQL-resources
                        if ($resource) {
                            while ($record = $this->getTypo3Database()->sql_fetch_assoc($resource)) {
                                $finalRelations[] = $record;
                            }

                            // free memory of query
                            $this->getTypo3Database()->sql_free_result($resource);

                        // set relations for array-data
                        } else  {
                            $finalRelations = $recordArray;
                        }


                    }

                    //=============================================
                    // check if there has been any change
                    $checksumNew = sha1((string) serialize($finalRelations));

                    if (
                        (! $checksums[$field])
                        || ($checksums[$field] !== $checksumNew)
                    ){

                        // unset existing relations per field
                        $this->unrelateAllByField($object, $field);

                        // build array for results
                        $relationFieldsSuccess[$field] = array();

                        // set new relations per field
                        foreach ($finalRelations as $relationRecord) {
                            $objectTwo = $this->relateAllSub($object, $relationRecord, $field, $count, $errors);
                            if ($objectTwo) {

                                // return updated field
                                $relationFieldsSuccess[$field][] = $objectTwo;
                            }
                        }

                        // set new checksum for field
                        $checksums[$field] = $checksumNew;

                    } else {
                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Edges for field "%s" on %s not re-set, since nothing has changed (class=%s, table=%s, id=%s).', $field, $rid, $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid()), $object->getPropertiesChanged());
                    }
                }

                // set new checksums
                $mappingRecord->setRelationChecksums(serialize($checksums));

                // save change of checksum to mapping table
                // but without resetting import for keywords since we only changed relations here!
                $repository = $this->getMappingTableRepository();
                $repository->update($mappingRecord);
                $this->getPersistenceManager()->persistAll();

            } catch (\Exception $e) {
                throw new \RKW\RkwSearch\StorageRelationException ($e->getMessage() . ' Line: ' . $e->getLine(), 1399542765);
                //===
            }

            if ($errors > 0)
                throw new \RKW\RkwSearch\StorageRelationException (sprintf('Could not set all edges for class "%s."', $this->getOrientDbClass()), 1399883014);
                //===

            //
            if ($count > 0)
                return 1;
                //===

            // if there were no errors and no changes, return 2
            return 2;
            //===
        }

        // if there was no mapping record, return 0
        return 0;
        //===

    }

    /**
     * Sets relation (edge) from given object to given record
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param array $record Record data to relate with
     * @param string $field Field name
     * @param integer &$count Counter for records
     * @param integer &$errors Counter for errors
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface|NULL
     * @throws \RKW\RkwSearch\Exception
     * @throws \RKW\RkwSearch\StorageRelationException
     */
    protected function relateAllSub($object, $record, $field, &$count, &$errors) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1430727581);
            //===

        if (
            (! is_array ($record))
            || (intval($record['uid'] < 1))
        )
            throw new \RKW\RkwSearch\Exception ('Invalid data array given. At least uid must be given.', 1430727701);
            //===

        if (! $this->getRelationForeignTable($field))
            throw new \RKW\RkwSearch\StorageRelationException (sprintf('Could not set edges for class "%s". No relation-mm-table or relation-foreign-table set for given field.', $this->getOrientDbClass()), 1430727752);
            //===


        // if we have a translated object, we try to load the translated one!
        /* !!! DOESN'T SEEM TO BE NECESSARY HERE !!!
        if (
            ($language = $object->getLanguageUid() > 0)
            && ($languageField = Typo3Fields::getLanguageField($this->getRelationForeignTable($field)))
            && ($languagePointerField = Typo3Fields::getLanguageOriginalPointerField($this->getRelationForeignTable($field)))
        ) {

            // $this->getTypo3Database()->store_lastBuiltQuery = 1;

            // set table
            $foreignTable = $this->getRelationForeignTable($field);

            // fields to select
            $fields = $foreignTable . '.' .
                implode (', ' . $foreignTable . '.',
                    array_merge(
                        Typo3Fields::getDefaultFieldsForOrientClass($foreignTable),
                        Typo3Fields::getVersionFields($foreignTable)
                    )
                );

            // important fields!
            $where = QueryTypo3Helper::getWhereClauseForEnableFields($foreignTable);
            $where .= QueryTypo3Helper::getWhereClauseForVersioning($foreignTable);
            $where .= QueryTypo3Helper::getWhereClauseForLanguageFields($foreignTable, $object->getLanguageUid());

            // get all content-elements from that page in correct order
            $record = $this->getTypo3Database()->exec_SELECTgetSingleRow(
                $fields,
                $foreignTable,
                (Typo3Fields::hasLanguageOriginalPointerTable($foreignTable) ? $languagePointerField . ' = ' . intval($record['uid']) . ' AND ' : '') .
                '1=1 ' . $where
            );

            // var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }
        */

        if ($record) {

            // create target object
            /** @var \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $objectTwo */
            $objectTwo = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Common::getOrientModelFromTableName($this->getRelationForeignTable($field)), $record, TRUE);

            // set relations to orientDb as edges
            if (! $this->relate($object, $objectTwo, $field)) {
                $errors++;
                return NULL;
                //===
            }

            $count++;
            return $objectTwo;
            //===
        }

        $errors++;
        return NULL;
        //===
    }


    /**
     * Sets relation (edge) from one to another object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The source object to relate from
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $objectTwo The target object to relate to
     * @param string $field Field to relate with
     * @param string $class OrientDbClass to relate with
     * @param float $weight Weight of edge
     * @param boolean $invert Inverts edge-direction
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @throws \RKW\RkwSearch\StorageException
     * @api
     */
    public function relate($object, $objectTwo, $field = NULL, $class = NULL, $weight = 0.0, $invert = FALSE) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1399543523);
            //===


        if (!$objectTwo instanceof \RKW\RkwSearch\OrientDb\Domain\Model\VertexAbstract)
            throw new \RKW\RkwSearch\Exception ('The object given to ' . __METHOD__ . ' was not of type \RKW\RkwSearch\OrientDb\Domain\Model\VertexAbstract.', 1421139130);
            //===

        // at least one of the two params must be set!
        if (
            (empty($field))
            && (empty($class))
        )
            throw new \RKW\RkwSearch\Exception ('Neither field nor OrientDB-class for setting the edge is given. Please define at least one of the two parameters.', 1418196547);
            //===

        // check if a given field is valid
        if (
            (! empty($field))
            && (! $this->hasRelationField($field))
        )
            throw new \RKW\RkwSearch\Exception (sprintf('The given field "%s" is no valid relation field for this repository.', $field), 1401286478);
            //===

        // check if given class is valid
        if (
            (! empty($class))
            && (! $this->hasRelationEdgeClass($class))
        )
            throw new \RKW\RkwSearch\Exception (sprintf('The given edge-class "%s" is no valid class for this repository.', $class), 1418198037);
            //===


        // set edge-class
        $edgeClass = $class;
        if ($field)
            $edgeClass = $this->getRelationEdgeClass($field);


        // check if edge-model exists!
        if (
            (! $edgeModelName = Common::getOrientModelFromClassName($edgeClass))
            || (! class_exists($edgeModelName))
            || (! $edgeModel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Common::getOrientModelFromClassName($edgeClass)))
            || (! $edgeModel instanceof \RKW\RkwSearch\OrientDb\Domain\Model\EdgeInterface)
        )
            throw new \RKW\RkwSearch\Exception (sprintf('The model "%s" for edge-class "%s" does not exist or does not implement \RKW\RkwSearch\OrientDb\Domain\Model\EdgeInterface.', $edgeModelName, $edgeClass), 1418725036);
            //===

        try {

            if (
                (
                    ($rid = $object->getRid())
                    || (
                        ($record = $this->getMappingRecordByObject($object))
                        && ($rid = $record->getRid())
                    )
                )
                && (
                    ($ridTwo = $objectTwo->getRid())
                    || (
                        ($recordTwo = $this->getMappingRecordByObject($objectTwo))
                        && ($ridTwo = $recordTwo->getRid())
                    )
                )
            ) {


                if ($rid == $ridTwo)
                    throw new \RKW\RkwSearch\Exception (sprintf('Object %s can not relate to itself.', $rid), 1421146670);
                    //===

                try {

                    // set default properties
                    $edgeModel->setTstamp(time());
                    $edgeModel->setWeight(floatval($weight));

                    // set counter
                    $edgeModel->setCounter(1);

                    // mark objects in debug mode
                    $edgeModel->setDebug($this->debugMode);

                     // set relations between vertexes as edges
                    $query = new Query;
                    if ($invert) {
                        $query->createEdge($edgeClass)
                            ->fromVertex($ridTwo)
                            ->toVertex($rid)
                            ->set($edgeModel->getPropertiesChanged());

                    } else {
                        $query->createEdge($edgeClass)
                            ->fromVertex($rid)
                            ->toVertex($ridTwo)
                            ->set($edgeModel->getPropertiesChanged());
                    }

                    if ($this->lastQueryResult = $this->getOrientDbDatabase()->createEdge($query)) {

                        // clear cache
                        if ($this->clearCache) {
                            $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_edges');
                            $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related_' . Common::underscore($this->getOrientDbClass()));
                        }

                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Set edge of class "%s" from %s to %s', $edgeClass, $rid,  $ridTwo));
                        return TRUE;
                        //===
                    }


                } catch (\Exception $e) {

                    // if we get an duplicate error, we try to update the counter
                    if (strpos($e->getMessage(), 'com.orientechnologies.orient.core.storage.ORecordDuplicatedException') !== FALSE){

                        // only if the current edge supports it
                        if (
                            ($counterField = OrientDbFields::getCtrlField($edgeClass, 'counter'))
                            || ($weight)
                        ){

                            // set default properties
                            $edgeModel->setTstamp(time());

                            // unset counter - it will be incremented
                            if ($counterField)
                                $edgeModel->unsCounter();

                            // set weight
                            if ($weight)
                                $edgeModel->setWeight(floatval($weight));

                            // mark objects in debug mode
                            $edgeModel->setDebug($this->debugMode);

                            $query = new Query;
                            if ($counterField) {
                                $query->updateEdge($edgeClass)
                                    ->increment(array($counterField => 1))
                                    ->set($edgeModel->getPropertiesChanged());

                            } else {
                                $query->updateEdge($edgeClass)
                                    ->set($edgeModel->getPropertiesChanged());
                            }

                            if ($invert) {
                                $query->andWhere('out = ?', $ridTwo);
                                $query->andWhere('in = ?', $rid);
                            } else {
                                $query->andWhere('out = ?', $rid);
                                $query->andWhere('in = ?', $ridTwo);
                            }

                            if ($this->lastQueryResult = $this->getOrientDbDatabase()->updateEdge($query)) {

                                // clear cache
                                if ($this->clearCache) {
                                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_edges');
                                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related' . Common::underscore($this->getOrientDbClass()));
                                }

                                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Updated counter for edge of class "%s" from %s to %s', $edgeClass, $rid,  $ridTwo));
                                return TRUE;
                                //===
                            }
                        }

                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Could not set edge of class "%s" from %s to %s', $edgeClass, $rid, $ridTwo));
                        return FALSE;
                        //===
                    }

                    throw new \RKW\RkwSearch\StorageRelationException ($e->getMessage(), 1399542765);
                    //===
                }
            }

            throw new \RKW\RkwSearch\StorageRelationException (sprintf('For at least one of the given objects no RID is available in database to set edge of class "%s".', $edgeClass));
            //===

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Error while trying to set edge of class "%s" from %s to %s', $edgeClass, ($object->getRid() ? $object->getRid() : $object->getUid()), ($objectTwo->getRid() ? $objectTwo->getRid() : $objectTwo->getUid())));
            throw new \RKW\RkwSearch\StorageRelationException ($e->getMessage(). ' Line: ' . $e->getLine(), 1399542765);
            //===
        }

    }


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
    public function unrelateAllByField($object, $field) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1399543523);
            //===

        if (! $this->hasRelationField($field))
            throw new \RKW\RkwSearch\Exception (sprintf('The given field "%s" is no valid relation field for this repository.', $field), 1401286478);
            //===


        return $this->unrelateAll($object, $this->getRelationEdgeClass($field), Common::getOrientClassNameFromTableName($this->getRelationForeignTable($field)));
        //===
    }


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
    public function unrelateAll ($object, $edgeClass, $targetVertexClass) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1399543523);
            //===

        if (! $edgeClass)
            throw new \RKW\RkwSearch\Exception ('No valid edgeClass given.', 1424710285);
            //===

        if (! $targetVertexClass)
            throw new \RKW\RkwSearch\Exception ('No valid targetVertexClass given.', 1424710301);
            //===

        try {

            // load rid from mapping table
            if (
                ($rid = $object->getRid())
                || (
                    ($record = $this->getMappingRecordByObject($object))
                    && ($rid = $record->getRid())
                )
            ) {

                // delete all existing edges of the given type
                $query = new Query;
                $query->deleteEdgeAllFromId($edgeClass)
                    ->fromVertex($rid)
                    ->toVertex($targetVertexClass);

                if ($this->lastQueryResult = $this->getOrientDbDatabase()->deleteEdge($query)) {

                    // clear cache
                    if ($this->clearCache) {
                        $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_edges');
                        $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related_' . Common::underscore($this->getOrientDbClass()));
                    }

                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Deleted all edges from %s (class=%s, table=%s, id=%s, lid=%s) to vertexClass "%s" related with edgeClass "%s".', $rid, $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid(), $object->getLanguageUid(), $targetVertexClass, $edgeClass));
                    return TRUE;
                    //===
                }
            }

            return FALSE;
            //===

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Could not delete all edges of given object (class=%s, table=%s, id=%s, lid=%s) to vertexClass "%s" related with edgeClass "%s".',  $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid(), $object->getLanguageUid(), $targetVertexClass, $edgeClass));
            throw new \RKW\RkwSearch\StorageRelationException ($e->getMessage() . ' Line: ' . $e->getLine(), 1399542765);
            //===
        }

    }


    /**
     * Replaces an existing object with the same identifier by the given object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $modifiedObject The object to add
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function update($modifiedObject) {

        $objectType = $this->getObjectType();
        if (!$modifiedObject instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1249479625);
            //===


        // load rid from object or from mapping table
        $record = NULL;
        $checksumNew = NULL;
        if (
            ($rid = $modifiedObject->getRid())
            || (
                ($record = $this->getMappingRecordByObject($modifiedObject))
                && ($rid = $record->getRid())
            )
        ) {


            // unset counter field - if none is configured it is simply not unset
            $modifiedObject->unsCounter();

            // set default properties
            $modifiedObject->setTstamp(time());

            // mark objects in debug mode
            $modifiedObject->setDebug($this->debugMode);

            // set languageUid in FE-Mode
            if (
                ($this->getEnvironmentMode() == 'FE')
                && ($modifiedObject->getLanguageUid() == NULL)
            )
                $modifiedObject->setLanguageUid(intval($GLOBALS['TSFE']->sys_language_uid));

            // create query
            $propertiesChanged = $modifiedObject->getPropertiesChanged('update');
            $query = new Query();
            $query->updateVertexSingle($rid)
                ->set($propertiesChanged);

            // check if we have a counter field - so we need to increment
            // this me
            if (
                ($counterField = OrientDbFields::getCtrlField($this->getOrientDbClass(), 'counter'))
                && (! in_array($counterField, $propertiesChanged))
            ) {
                $query = new Query();
                $query->updateVertexSingle($rid)
                    ->increment(array ($counterField => 1))
                    ->set($propertiesChanged);
            }

            // we only update if there has happened any change beyond the tstamp!
            // this way we do not bother the database for bullshit
            // but we only can check this if we have a mapping record!
            if ($record) {

                $checksumNew = sha1((string) serialize($modifiedObject->getPropertiesChanged('checksum')));
                if ($record->getChecksum() == $checksumNew) {

                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Update for %s not needed, since nothing has changed (class=%s, table=%s, id=%s, lid=%s).', $rid, $this->getOrientDbClass(), $this->getTypo3Table(), $modifiedObject->getUid(), $modifiedObject->getLanguageUid()), $modifiedObject->getPropertiesChanged());
                    return 2;
                    //===
                }
            }

            // execute query
            if ($this->lastQueryResult = $this->getOrientDbDatabase()->updateVertex($query)) {

                // set checksum and store edit timestamp in mapping table
                if ($record) {

                    $record->setChecksum($checksumNew);
                    $record->setDebug($this->debugMode);
                    $record->setNoSearch($modifiedObject->getNoSearch());
                    $record->setT3pid($modifiedObject->getPid());
                    $record->setImportTstamp(time());

                    $repository = $this->getMappingTableRepository();
                    $repository->update($record);
                    $repository->resetImport($record);
                    $this->getPersistenceManager()->persistAll();
                }

                // clear cache of class
                if ($this->clearCache) {
                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_vertex_' . Common::underscore($this->getOrientDbClass()));
                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related_' . Common::underscore($this->getOrientDbClass()));
                }

                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Updated %s (class=%s, table=%s, id=%s, lid=%s).', $rid, $this->getOrientDbClass(), $this->getTypo3Table(), $modifiedObject->getUid(), $modifiedObject->getLanguageUid()), $modifiedObject->getPropertiesChanged());
                return 1;
                //===
            }
        }

        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Could not update record, because rid could not be found (class=%s, table=%s, id=%s, lid=%s).', $this->getOrientDbClass(), $this->getTypo3Table(), $modifiedObject->getUid(), $modifiedObject->getLanguageUid()), $modifiedObject->getPropertiesChanged());
        return 0;
        //===
    }


    /**
     * Removes an object from this repository.
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function remove($object) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1249479625);
            //===

        // try to delete via delete-field
        if (
            ($this->deleteHard != TRUE)
            && ($deleteField = OrientDbFields::getCtrlField($this->getOrientDbClass(), 'delete'))
        ){
            $setter = 'set' . ucFirst($deleteField);
            $object->$setter(1);
            return $this->update($object);
            //===
        }

        // if no delete field exists, we really delete the record!
        // load rid from object or from mapping table
        $record = NULL;
        if (
            ($rid = $object->getRid())
            || (
                ($record = $this->getMappingRecordByObject($object))
                && ($rid = $record->getRid())
            )
        ) {

            $query = new Query();
            $query->deleteVertexSingle($rid);

            if ($this->lastQueryResult = $this->getOrientDbDatabase()->deleteVertex($query)) {

                // set checksum and remove object from mapping table
                if ($record) {

                    $record->setChecksum(sha1((string) serialize($object->getPropertiesChanged('checksum'))));
                    $record->setImportTstamp(time());

                    $repository = $this->getMappingTableRepository();
                    $repository->remove($record);
                    $this->getPersistenceManager()->persistAll();
                }

                // clear cache of class
                if ($this->clearCache) {
                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_vertex_' . Common::underscore($this->getOrientDbClass()));
                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related_' . Common::underscore($this->getOrientDbClass()));
                }

                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::WARNING, sprintf('Deleted %s (class=%s, table=%s, id=%s, lid=%s).', $rid, $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid(), $object->getLanguageUid()));
                return 1;
                //===
            }

        }

        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Could not delete record (class=%s, table=%s, id=%s, lid=%s).', $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid(), $object->getLanguageUid()));
        return 0;
        //===


    }


    /**
     * Removes all objects of this repository as if remove() was called for
     * all of them.
     *
     * @return void
     * @api
     */
    public function removeAll() {

        // delete all debug entries
        if ($this->debugMode) {

            // delete edges
            $query = new Query();
            $query->deleteEdgeAll('EdgeAbstract');
            $query->where('debug = ?', TRUE);

            if ($numberOfDeletedItems = $this->getOrientDbDatabase()->delete($query))
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Deleted %s debug entries from EdgeAbstract.', $numberOfDeletedItems));

            // delete from mapping table
            foreach($this->getMappingTableRepository()->findAllByDebug() AS $item) {
                $item->setImportTstamp(time());
                $this->getMappingTableRepository()->remove($item);
                $this->getPersistenceManager()->persistAll();
            }


            // delete vertexes
            $query = new Query();
            $query->deleteVertex('VertexAbstract');
            $query->where('debug = ?', TRUE);

            if ($numberOfDeletedItems = $this->getOrientDbDatabase()->delete($query))
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Deleted %s debug entries from VertexAbstract.', $numberOfDeletedItems));


        // Normal deleting
        } else {

            /** @var \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object */
            foreach ($this->findAll() AS $object) {
                $this->remove($object);
            }
        }
    }



    /**
     * Removes the entries from the import tables
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to reset
     * @return boolean
     * @api
     */
    public function resetImport($object) {

        if (
            ($record = $this->getMappingRecordByObject($object))
            && ($uid = $record->getUid())
        ) {

            return $this->getMappingTableRepository()->resetImport($record);
            //===
        }

        return FALSE;
        //===
    }


    /**
     * Adds an object to this repository
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param boolean $forceInsert Forces insertion
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function add($object, $forceInsert = FALSE) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to add() was not of the type (' . $objectType . ') this repository manages.', 1396968749);
            //===


        try {
            $mappingTableRepository = $this->getMappingTableRepository();

            // check if object already exists
            if (
                ($record = $this->getMappingRecordByObject($object))
                && ($rid = $record->getRid())
            ) {

                // if insertion is forced, we remove existing entries in TYPO3 database
                if ($forceInsert) {
                    $mappingTableRepository->remove($record);
                    $this->getPersistenceManager()->persistAll();
                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::WARNING, sprintf('Removing record from mapping table because of forced insert. (rid=%s, class=%s, table=%s, id=%s).', $record->getRid(), $record->getClass(), $record->getT3table(), $record->getT3id()));

                } else {
                    return $this->update($object);
                    //===
                }
            }

            // set properties
            // some are overwritten here
            $object->setTstamp(time());

            // set counter field - if none is configured it is not set
            $object->setCounter(1);

            // set debug-mode
            $object->setDebug($this->debugMode);

            // set languageUid in FE-Mode
            if (
                ($this->getEnvironmentMode() == 'FE')
                && ($object->getLanguageUid() == NULL)
            )
                $object->setLanguageUid(intval($GLOBALS['TSFE']->sys_language_uid));

            if (! $object->getCrdate())
                $object->setCrdate(time());

            $query = new Query();
            $query->createVertex($this->getOrientDbClass())
                ->set($object->getPropertiesChanged());


            if ($newRid = $this->lastQueryResult = $this->getOrientDbDatabase()->createVertex($query)) {

                // set Rid to object
                $object->setRid($newRid);

                // save rid, checksum and some other data to mapping table
                if ($typo3table = $this->getObjectTypo3TableWithLanguageOverlay($object)) {

                    $mappingModel = $this->getMappingTableModel();
                    $mappingModel->setClass($object->getClass());
                    $mappingModel->setRid($newRid);
                    $mappingModel->setT3table($this->getObjectTypo3TableWithLanguageOverlay($object));
                    $mappingModel->setT3id(intval($object->getUid()));
                    $mappingModel->setT3pid(intval($object->getPid()));
                    $mappingModel->setT3lid(intval($object->getLanguageUid()));
                    $mappingModel->setDebug($this->debugMode);
                    $mappingModel->setNoSearch($object->getNoSearch());
                    $mappingModel->setChecksum(sha1((string) serialize($object->getPropertiesChanged('checksum'))));
                    $mappingModel->setImportTstamp(time());

                    try {

                        // check if entry already exists in TYPO3-database and clean it up
                        // this is because obviously OrientDb has re-given the rid again
                        // and something isn't synchronous any more
                        /** @var \RKW\RKWSearch\Domain\Model\RidMapping $oldMappingModel */
                        if ($oldMappingModel = $mappingTableRepository->findOneByRid($newRid)) {
                            $mappingTableRepository->remove($oldMappingModel);
                            $this->getPersistenceManager()->persistAll();
                            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::WARNING, sprintf('Removing record from mapping table because of duplicate entry. (rid=%s, class=%s, table=%s, id=%s).', $oldMappingModel->getRid(), $oldMappingModel->getClass(), $oldMappingModel->getT3table(), $oldMappingModel->getT3id()));
                        }

                        $mappingTableRepository->add($mappingModel);
                        $this->getPersistenceManager()->persistAll();

                    // if something goes wrong here we have to delete the vertex again
                    // this is to keep the mapping consistent!
                    } catch (\Exception $e) {

                        $query = new Query();
                        $query->deleteVertexSingle($newRid);
                        $this->getOrientDbDatabase()->deleteVertex($query);
                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Could not insert record (class=%s, table=%s, id=%s). Vertex was created and deleted again because of error in mapping table.', $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid()), $object->getPropertiesChanged());
                        return 0;
                        //===

                    }

                }

                // clear cache of class
                if ($this->clearCache) {
                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_vertex_' . Common::underscore($this->getOrientDbClass()));
                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related_' . Common::underscore($this->getOrientDbClass()));
                }

                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Inserted %s (class=%s, table=%s, id=%s).', $newRid, $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid()), $object->getPropertiesChanged());
                return 1;
                //===
            }

        } catch (\Exception $e) {

            // if we get an duplicate error, we try to update the counter
            if (strpos($e->getMessage(), 'com.orientechnologies.orient.core.storage.ORecordDuplicatedException') !== FALSE) {

                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Record already exists, nothing inserted (class=%s, table=%s, id=%s).', $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid()), $object->getPropertiesChanged());
                return 2;
                //===
            }
        }

        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Could not insert record (class=%s, table=%s, id=%s).', $this->getOrientDbClass(), $this->getTypo3Table(), $object->getUid()), $object->getPropertiesChanged());
        return 0;
        //===
    }


    /**
     * Returns the total number objects of this repository.
     *
     * @return integer The object count
     * @api
     */
    public function countAll() {

        $query = new Query();
        $query->select(array('count(@rid)'));
        $query->from(array($this->getOrientDbClass()), false);
        $query->resetWhere();

        // set filter for special fields
        if ($this->getEnvironmentMode() == 'FE')
            $this->getWhereClauseForLanguageFields($query, $GLOBALS['TSFE']->sys_language_uid);

        // try to load data from cache
        if (! $result = $this->getCache()->getContent($query)) {

            // set enabled fields now since the timestamps would kill the cache
            $this->getWhereClauseForEnableFields($query);

            // get results
            $result = $this->getOrientDbDatabase()->count($query);

            // save results in cache
            $this->getCache()->setContent(
                $result,
                array(
                    'orientdb_count_all',
                    'orientdb_vertexes',
                    'orientdb_vertex_' . Common::underscore($this->getOrientDbClass())
                )
            );
        }

        return $result;
        //===
    }


    /**
     * Returns all objects of this repository.
     *
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @api
     */
    public function findAll() {

        $query = new Query();
        $query->select(array('*'));
        $query->from(array($this->getOrientDbClass()), FALSE);
        $query->resetWhere();

        // set filter for special fields
        if ($this->getEnvironmentMode() == 'FE')
            $this->getWhereClauseForLanguageFields($query, $GLOBALS['TSFE']->sys_language_uid);

        if ($this->debugMode)
            $query->andWhere('debug = ?', 1);

        // try to load data from cache
        if (! $result = $this->getCache()->getContent($query)) {

            // set enabled fields now since the timestamps would kill the cache
            $this->getWhereClauseForEnableFields($query);

            // get results
            $result = $this->getOrientDbDatabase()->execute($query);

            // save results in cache
            $this->getCache()->setContent(
                $result,
                array(
                    'orientdb_find_all',
                    'orientdb_vertexes',
                    'orientdb_vertex_' . Common::underscore($this->getOrientDbClass())
                )
            );
        }

        return $result;
        //===

    }


    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface|NULL
     * @api
     */
    public function findByUid($uid) {

        $query = new Query();
        $query->select(array('*'));
        $query->from(array($this->getOrientDbClass()), FALSE);
        $query->resetWhere();

        // set filter for special fields
        if ($this->getEnvironmentMode() == 'FE')
            $this->getWhereClauseForLanguageFields($query, $GLOBALS['TSFE']->sys_language_uid);

        if ($this->debugMode)
            $query->andWhere('debug = ?', 1);

        // set enabled fields
        $this->getWhereClauseForEnableFields($query);

        // set and-where
        $query->andWhere( 'uid = ?', $uid);

        // set limit
        $query->limit(1);

        // get results
        $result = $this->getOrientDbDatabase()->execute($query);
        return $result->first();
        //===
    }


    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid Page uid
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function findByQueryFactory($uid = 0) {

        // get query from factory
        $query = $this->getQueryFactory()->getQuery();

        if ($query) {

            if ($uid) {
                $query->andWhere('(NOT (uid = ? AND @class = \'DocumentPages\'))', intval($uid));

                // handle import pages
                $this->getWhereClauseForImportPages($query, $uid);
            }

            // get results
            return $this->getOrientDbDatabase()->execute($query);
            //===
        }

        return NULL;
        //===
    }


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
    public function findRelatedByQueryFactory($identifier, $tolerance, $itemsPerHundredSigns = 0.0, $minItems = 0) {

        $result = NULL;
        $identifierId = intval($identifier);

        if ($identifier instanceof \RKW\RkwBasics\Domain\Model\Department)
            $identifierId = $identifier->getUid();

        $cacheIdentifier = 'related_' . $this->getOrientDbClass() . '_' . intval($identifierId);
        if ($identifier instanceof \RKW\RkwBasics\Domain\Model\Department)
            $cacheIdentifier .= '_department';

        $queryStringArray = array ();
        $limit = $minItems;

        // try to load from cache
        if (! $result = $this->getCache()->getContent($cacheIdentifier)) {

            /** @var \RKW\RkwSearch\Search\QueryFactory $queryFactory */
            $queryFactory = $this->getQueryFactory();

            // no fuzzy search here
            $queryFactory->setFuzzySearchLucene(FALSE);

            // no boosting for pdfs, perfect match and length here
            $queryFactory->setPublicationBoostSearchLucene(FALSE);
            $queryFactory->setPerfectMatchBoostSearchLucene(FALSE);
            $queryFactory->setLengthBoostSearchLucene(FALSE);


            //======================================================
            // for project based query
            if ($identifier instanceof \RKW\RkwBasics\Domain\Model\Department) {

                $queryFactory->setFilter('department', $identifier->getName());
                $queryFactory->setOrdering(array('pubDate' => 'DESC'));


            //======================================================
            // for keyword based query
            } else {

                /** @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentAbstract $document */
                if ($document = $this->findByUid(intval($identifierId))){

                    if ($keywords = $document->getTopKeywords()) {

                        // go through keywords
                        $cnt = 0;
                        foreach ($keywords as $keyword => $score) {

                            // include at least one keyword!
                            if (
                                (count($queryStringArray) > 0)
                                && ($score < $tolerance)
                            )
                                break;
                                //===

                            $queryStringArray[] = '(' . Text::sanitizeStringLucene($keyword) . ')';
                            $cnt++;
                        }

                        if (count($queryStringArray) > 0) {

                            // calculate maximum number of results based on text length
                            if ($itemsPerHundredSigns) {

                                $limit = floor(strlen($document->getSearchContent()) / 100 * $itemsPerHundredSigns);
                                if (
                                    ($limit < $minItems)
                                    && ($minItems)
                                )
                                    $limit = $minItems;

                                // for imported main-pages
                                if (
                                    ($document->getPdfImport())
                                    && (!$document->getPdfImportSub())
                                )
                                    $limit = $minItems;

                            }
                        }
                    }
                }

            }

            // is there anything for searching?
            if (
                (count($queryStringArray) > 0)
                || (count($queryFactory->getFilters()) > 0)
            ) {

                 // set search string and limit
                if (count($queryStringArray) > 0)
                    $queryFactory->setSearchString(implode(' OR ', $queryStringArray));
                $queryFactory->setLimit($limit);

                if ($query = $queryFactory->getQuery(TRUE)) {

                    if (! $identifier instanceof \RKW\RkwBasics\Domain\Model\Department) {

                        // exclude current uid
                        $query->andWhere('(NOT (uid = ? AND @class = \'DocumentPages\'))', intval($identifierId));

                        // handle import pages
                        $this->getWhereClauseForImportPages($query, $identifierId);

                    }

                    // do query
                    $result = $this->getOrientDbDatabase()->execute($query);

                    // save results in cache
                    $this->getCache()->setContent(
                        $result,
                        array(
                            'orientdb_related',
                            'orientdb_related_' . Common::underscore($this->getOrientDbClass())
                        )
                    );
                }
            }
        }

        return $result;
        //===

    }




    /**
     * Adds some special elements to the WHERE clause for import-sub-pages
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @param integer $uid
     * @return $this
     */
    protected function  getWhereClauseForImportPages (\RKW\RkwSearch\OrientDb\Storage\Query\Query $query, $uid) {

        // in order to exclude sub-chapters of a publication, we need to check the rootline!
        // get PageRepository and rootline
        $repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
        $rootlinePages = $repository->getRootLine(intval($uid));

        // go through all pages and take the one that has a match in the corresponsing field
        // but only if the current page IS an import page!
        $importParentPid = NULL;
        if (
            (isset($rootlinePages[count($rootlinePages)-1]))
            && (isset($rootlinePages[count($rootlinePages)-1]['tx_bmpdf2content_is_import']))
            && ($rootlinePages[count($rootlinePages)-1]['tx_bmpdf2content_is_import'] == 1)
        ){

            foreach ($rootlinePages as $page => $values) {
                if (
                    ($values['tx_bmpdf2content_is_import'] == 1)
                    && ($values['tx_bmpdf2content_is_import_sub'] == 0)
                ) {
                    $importParentPid = intval($values['uid']);
                    break;
                    //===
                }
            }
        }

        // exclude import parents and co-subpages
        if ($importParentPid) {
            $query->andWhere('(NOT (first(outE(\'EdgeImportParent\')).inV(\'DocumentPages\').uid = ? AND @class = \'DocumentPages\'))', intval($importParentPid));
            $query->andWhere('(NOT (uid = ? AND @class = \'DocumentPages\'))', intval($importParentPid));
        }
    }


    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface|NULL
     * @api
     */
    public function findByRid($uid) {

        $query = new Query();
        $query->selectSingle($uid);
        $query->resetWhere();

        // set enabled fields
        $this->getWhereClauseForEnableFields($query);

        if ($this->debugMode)
            $query->andWhere('debug = ?', 1);

        // get results
        $result = $this->getOrientDbDatabase()->execute($query);

        return $result;
        //===
    }



    /**
     * Returns the last query result
     *
     * @return array|integer
     */
    public function getLastQueryResult() {

        return $this->lastQueryResult;
        //===
    }



    /**
     * Returns the TYPO3 table name with language overlay
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object
     * @return string
     */
    public function getObjectTypo3TableWithLanguageOverlay($object) {

        // get TYPO3 table from class
        // then check if there is an language overlay table at work
        $typo3Table = Common::getTypo3TableFromOrientClass($object);
        if (
            ($object->getLanguageUid() > 0)
            && (Typo3Fields::hasLanguageForeignTable($typo3Table))
        )
            return Typo3Fields::getLanguageForeignTable($typo3Table);
            //===

        return $typo3Table;
        //===
    }



    /**
     * Checks if relation-field is set
     *
     * @param string $field
     * @return boolean
     */
    public function hasRelationField($field) {

        return OrientDbFields::hasRelationField($this->getOrientDbClass(), $field);
        //===
    }

    /**
     * Checks if edge-class is defined
     *
     * @param string $edgeClass
     * @return boolean
     */
    public function hasRelationEdgeClass($edgeClass) {

        return OrientDbFields::hasRelationEdgeClass($edgeClass);
        //===
    }


    /**
     * Returns all relation fields of current class
     *
     * @param array $filterList Filters the relation fields by the given list
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRelationFields($filterList = array()) {

        $fields = OrientDbFields::getRelationFields($this->getOrientDbClass(), $filterList);
        if (is_array($fields))
            return $fields;
            //===

        return array ();
        //===

    }

    /**
     * Returns mm-table of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationMmTable($field) {

        if (OrientDbFields::getRelationMmTable($this->getOrientDbClass(), $field))
            return OrientDbFields::getRelationMmTable($this->getOrientDbClass(), $field);
            //===

        return Typo3Fields::getMmTable($this, $field);
        //===

    }

    /**
     * Returns mm-table of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationMmMatchFields($field) {

        return Typo3Fields::getMmMatchFields($this, $field);
        //===

    }



    /**
     * Returns foreign-table of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationForeignTable($field) {

        if (OrientDbFields::getRelationForeignTable($this->getOrientDbClass(), $field))
            return OrientDbFields::getRelationForeignTable($this->getOrientDbClass(), $field);
            //===

        return Typo3Fields::getForeignTable($this, $field);
        //===
    }

    /**
     * Returns foreign-table of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationForeignField($field) {

        return Typo3Fields::getForeignField($this, $field);
        //===
    }

    /**
     * Returns foreign-table of given field for relation
     * Only relevant for MM-Relations!
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationForeignTableField($field) {

        return Typo3Fields::getForeignTableField($this, $field);
        //===
    }

    /**
     * Returns foreign-sortby of given field for relation
     * Only relevant for MM-Relations!
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationForeignSortBy($field) {

        return Typo3Fields::getForeignSortBy($this, $field);
        //===
    }


    /**
     * Returns foreign-sortby of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationForeignMatchFields($field) {

        return Typo3Fields::getForeignMatchFields($this, $field);
        //===
    }

    /**
     * Returns local-field-name of given field for relation
     *
     * @param string $field
     * @return NULL|string
     * @deprecated
     */
    public function getRelationLocalField($field) {

        return Typo3Fields::getLocalField($this, $field);
        //===
    }


    /**
     * Returns edge-class of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationEdgeClass($field) {

        return OrientDbFields::getRelationEdgeClass($this->getOrientDbClass(), $field);
        //===

    }


    /**
     * Returns vertex-class of given field for relation
     *
     * @param string $field
     * @return NULL|string
     */
    public function getRelationVertexClass($field) {

        return Common::getOrientClassNameFromTableName($this->getRelationForeignTable($field));
        //===
    }



    /**
     * Returns the rootline pages of the current object
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRootlineRelationUids($object) {

        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1431335018);
            //===

        // only for pages!!!!
        if ($this->getTypo3Table() == 'pages') {

            // get PageRepository
            $repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');

            // get all rootline pages from here on
            // IMPORTANT: This method does NOT respect enable-fields or language overlay!!!!
            $rootlineDataArray  = $repository->getRootLine($object->getUid());

            $resultData = array();
            foreach ($this->getRelationFields() as $field => $mapping) {
                foreach ($rootlineDataArray as $page) {

                    // break if already set
                    if (! (empty($resultData[$field])))
                        break;
                        //===

                    // set if there is something to set
                    // since all relations are unset before newly set, we don't have to care for unsetting here!
                    if (
                        ($page[$field])
                        && (! empty($page[$field]))
                    )
                        $resultData[$field] = $page[$field];
                }
            }

            return $resultData;
            //===

        }

        return array();
        //===
    }


    /**
     * Returns the OrientDB class name
     *
     * @return string
     */
    public function getOrientDbClass() {

        if (! $this->orientDbClass)
            $this->orientDbClass = Common::getShortName($this, TRUE);
        return $this->orientDbClass;
        //===
    }



    /**
     * Returns the TYPO3 table name
     *
     * @return string
     */
    public function getTypo3Table() {

        return Common::getTypo3TableFromOrientClass($this->getOrientDbClass());
        //===
    }


    /**
     * Returns the model name
     *
     * @return string
     */
    public function getObjectType() {

        if (! $this->objectType)
            $this->objectType = Common::getOrientModelFromClassName(Common::getShortName($this, TRUE));
        return $this->objectType;
        //===
    }



    /**
     * Returns the class name of this class.
     *
     * @return string Class name of the repository.
     */
    public function getRepositoryClassName() {
        return get_class($this);
        //===
    }




    /**
     * Get the corresponding mapping record from the mapping table
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object
     * @return \RKW\RkwSearch\Domain\Model\RidMapping
     * @throws \RKW\RkwSearch\Exception
     */
    public function getMappingRecordByObject($object) {

        if (!$object instanceof \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface)
            throw new \RKW\RkwSearch\Exception('No valid object given.', 1415123434);
            //===

        // $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

        // set filter and ignore storagePageUid
        $query = $this->getMappingTableRepository()->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);

        $filterArr = array(
            $query->equals('t3table', $this->getObjectTypo3TableWithLanguageOverlay($object)),
            $query->equals('t3id', intval($object->getUid())),
            $query->equals('t3lid', intval($object->getLanguageUid()))
        );

        // var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);

        // we need to clone here since f***ing extbase only references to the object it loaded the first time during one process
        if ($record = $query->matching($query->logicalAnd($filterArr))->execute()->getFirst())
            return $record;
            //===

        return NULL;
        //===
    }


    /**
     * Returns mapping table repository
     *
     * @return \RKW\RkwSearch\Domain\Repository\RidMappingRepository
     */
    public function getMappingTableRepository() {

        if (! $this->mappingTableRepository instanceof \RKW\RkwSearch\Domain\Repository\RidMappingRepository) {
            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $this->mappingTableRepository = $objectManager->get('RKW\RkwSearch\Domain\Repository\RidMappingRepository');
        }

        return $this->mappingTableRepository;
        //===
    }


    /**
     * Returns mapping table
     *
     * @return \RKW\RkwSearch\Domain\Model\RidMapping
     */
    public function getMappingTableModel() {

        // @toDo: For multiple inserts we need always a new model! Otherwise the old one is overridden and only the last record is saved!
        //if (! $this->mappingTableModel instanceof \RKW\RkwSearch\Domain\Model\RidMapping) {
            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $this->mappingTableModel = $objectManager->get('RKW\RkwSearch\Domain\Model\RidMapping');
        //}

        return $this->mappingTableModel;
        //===
    }


    /**
     * Returns the query factory
     *
     * @return \RKW\RkwSearch\Search\QueryFactory
     * @throws \RKW\RkwSearch\Exception
     */
    public function getQueryFactory() {

        if (! $this->queryFactory instanceof \RKW\RkwSearch\Search\QueryFactory)
            throw new \RKW\RkwSearch\Exception('No queryFactory available.', 1425561196);
            //===

        // set debug mode accordingly
        $this->queryFactory->setDebugMode($this->debugMode);

        return $this->queryFactory;
        //===
    }

    /**
     * RSets the query factory
     *
     * @param \RKW\RkwSearch\Search\QueryFactory $queryFactory
     */
    public function setQueryFactory(\RKW\RkwSearch\Search\QueryFactory $queryFactory) {

        $this->queryFactory = $queryFactory;
    }

    /**
     * Returns persistence manager
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    public function getPersistenceManager() {

        if (! $this->persistenceManager instanceof \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager) {
            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $this->persistenceManager = $objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager');
        }

        return $this->persistenceManager;
        //===
    }


    /**
     * Returns the cache object
     *
     * @return \RKW\RkwSearch\OrientDb\Cache\RepositoryCache
     */
    public function getCache() {

        if (! $this->cache instanceof \RKW\RkwSearch\OrientDb\Cache\RepositoryCache)
            $this->cache = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Cache\\RepositoryCache');
        return $this->cache;
        //===
    }


    /**
     * Gets the TYPO3 database object.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public function getTypo3Database() {
        return $GLOBALS['TYPO3_DB'];
        //===
    }

    /**
     * Returns the OrientDb database object
     *
     * @return \RKW\RkwSearch\OrientDb\Storage\Database\DatabaseInterface
     */
    public function getOrientDbDatabase() {

        if (! $this->orientDbDatabase instanceof \RKW\RkwSearch\OrientDb\Storage\Database\DatabaseInterface) {

            // check if there is some configuration setting in the globals array
            $driver = 'rest';
            if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['driver'])) {
                $driver = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['driver'];

            // else we check the TypoScript
            }  else {

                if (
                    ($settings = $this->getTypoScriptConfiguration())
                    && ($settings['persistence']['driver'])
                )
                    $driver = $settings['persistence']['driver'];
            }

            $this->orientDbDatabase = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Storage\\DatabaseLoader')->getHandle($driver);
        }

        return $this->orientDbDatabase;
        //===
    }


    /**
     * Get TypoScript configuration
     *
     * @return array
     */
    public function getTypoScriptConfiguration() {

        if (! $this->settings) {
            $settings = Common::getTyposcriptConfiguration();
            if ($settings['orientDb']['repository'])
                $this->settings = $settings['orientDb']['repository'];
        }

        return $this->settings;
        //===
    }



    /**
     * Function to return the current TYPO3_MODE.
     * This function can be mocked in unit tests to be able to test frontend behaviour.
     *
     * @return string
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    public function getEnvironmentMode() {
        return TYPO3_MODE;
        //===
    }


    /**
     * Set the cache option for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setClearCache($value) {
        $this->clearCache = (boolean) $value;
        return $this;
        //===
    }

    /**
     * Set the delete mode for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setDeleteHard($value) {
        $this->deleteHard = (boolean) $value;
        return $this;
        //===
    }


    /**
     * Set the debug mode for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setDebugMode($value) {
        $this->debugMode= (boolean) $value;
        return $this;
        //===
    }


    /**
     * Get the WHERE clause for the enabled fields of this TCA table
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @param boolean $includeNoSearch
     * @return $this
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getWhereClauseForEnableFields(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query, $includeNoSearch = FALSE) {

        QueryHelper::getWhereClauseForEnableFields($query, $this->getOrientDbClass(), $includeNoSearch);

        return $this;
        //===
    }

    /**
     * Get the WHERE clause for some language fields
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @param integer $languageId
     * @return $this
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getWhereClauseForLanguageFields(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query, $languageId) {

        QueryHelper::getWhereClauseForLanguageFields($query, $languageId, $this->getOrientDbClass());

        return $this;
        //===
    }

    /**
     * get the WHERE clause for the versioning
     * depending on the context
     *
     * @param \RKW\RkwSearch\OrientDb\Storage\Query\Query $query
     * @return $this
     * @see TYPO3\CMS\Core\Resource\AbstractRepository
     */
    protected function getWhereClauseForVersioning(\RKW\RkwSearch\OrientDb\Storage\Query\Query $query) {

        QueryHelper::getWhereClauseForVersioning($query, $this->getOrientDbClass());

        return $this;
        //===
    }



    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger() {

        if (! $this->logger instanceof \TYPO3\CMS\Core\Log\Logger)
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);

        return $this->logger;
        //===
    }



    /**
     * Dispatches magic methods (findBy[Property]())
     *
     * @param string $methodName The name of the magic method
     * @param string $arguments The arguments of the magic method
     * @throws \RKW\RkwSearch\Exception
     * @return mixed
     * @api
     */
    public function __call($methodName, $arguments) {

        mb_internal_encoding('UTF-8');

        $query = new Query();
        $cacheName = NULL;

        // findBy
        if (substr($methodName, 0, 6) === 'findBy' && strlen($methodName) > 7) {
            $propertyName = lcfirst(substr($methodName, 6));
            $cacheName = 'find_by_' . $propertyName;

            if (
                ($arguments[2]['select'])
                && is_array($arguments[2]['select'])
            ){
                $query->select($arguments[2]['select']);
            } else {
                $query->select(array('*'));
            }
            $query->from(array($this->getOrientDbClass()), FALSE);
            $query->resetWhere();

            // set and-where
            // check for fuzzySearch
            if (
                ($arguments[2]['fuzzySearch'] == TRUE)
                && ($fuzzyAppendix = OrientDbFields::getCtrlField($this->getOrientDbClass(), 'fuzzyAppendix'))
            ) {
                $query->andWhere('(' . $propertyName . ' = ?', $arguments[0]);
                $query->orWhere( $propertyName . ucFirst($fuzzyAppendix) . ' = ?)', ColognePhonetic::encode($arguments[0]));
            } else {
                $query->andWhere( $propertyName . ' = ?', $arguments[0]);
            }

        // findLeftBy
        } elseif (substr($methodName, 0, 10) === 'findLeftBy' && strlen($methodName) > 10) {
            $propertyName = lcfirst(substr($methodName, 10));
            $cacheName = 'find_left_by' . $propertyName;

            if (
                ($arguments[2]['select'])
                && is_array($arguments[2]['select'])
            ){
                $query->select($arguments[2]['select']);
            } else {
                $query->select(array('*'));
            }
            $query->from(array($this->getOrientDbClass()), FALSE);
            $query->resetWhere();

            // set and-where
            // check for fuzzySearch
            if (
                ($arguments[2]['fuzzySearch'] == TRUE)
                && ($fuzzyAppendix = OrientDbFields::getCtrlField($this->getOrientDbClass(), 'fuzzyAppendix'))
            ) {
                $query->andWhere('(' . $propertyName . '.left(' . mb_strlen($arguments[0]) . ') = ?', $arguments[0]);
                $query->orWhere( $propertyName . ucFirst($fuzzyAppendix) . ' = ?)', ColognePhonetic::encode($arguments[0]));
            } else {
                $query->andWhere( $propertyName . '.left(' . mb_strlen($arguments[0]) . ') = ?', $arguments[0]);
            }

        // findOneBy
        } elseif (substr($methodName, 0, 9) === 'findOneBy' && strlen($methodName) > 10) {

            $propertyName = lcfirst(substr($methodName, 9));
            $cacheName = 'find_one_by_' . $propertyName;

            if (
                ($arguments[2]['select'])
                && is_array($arguments[2]['select'])
            ){

                $query->select($arguments[2]['select']);
            } else {
                $query->select(array('*'));
            }

            $query->from(array($this->getOrientDbClass()), FALSE);
            $query->resetWhere();
            $query->limit(1);

            // set and-where
            // check for fuzzySearch
            if (
                ($arguments[2]['fuzzySearch'] == TRUE)
                && ($fuzzyAppendix = OrientDbFields::getCtrlField($this->getOrientDbClass(), 'fuzzyAppendix'))
            ) {
                $query->andWhere('(' . $propertyName . ' = ?', $arguments[0]);
                $query->orWhere( $propertyName . ucFirst($fuzzyAppendix) . ' = ? )', ColognePhonetic::encode($arguments[0]));
            } else {
                $query->andWhere( $propertyName . ' = ?', $arguments[0]);
            }

        // countBy
        } elseif (substr($methodName, 0, 7) === 'countBy' && strlen($methodName) > 8) {
            $propertyName = lcfirst(substr($methodName, 7));
            $cacheName = 'count_by_' . $propertyName;

            $query->select(array ('count(@rid)'));
            $query->from(array($this->getOrientDbClass()), FALSE);
            $query->resetWhere();

            // set and-where
            $query->andWhere( $propertyName . ' = ?', $arguments[0]);
        }

        // set filter for language field
        // but only if given or if in FE-mode
        // we check for ! is_null because of the default language!
        if (
            ($this->getEnvironmentMode() == 'FE')
            || (! is_null($arguments[1]))
        ){
            $languageUid = $GLOBALS['TSFE']->sys_language_uid;
            if (! is_null($arguments[1]))
                $languageUid = intval($arguments[1]);
            $this->getWhereClauseForLanguageFields($query, $languageUid);
        }

        // set debug mode if needed
        if ($this->debugMode)
            $query->andWhere('debug = ?', 1);


        // set orderBy
        $orderBy = $this->defaultOrderings;
        if (
            ($arguments[2]['orderBy'])
            && is_array($arguments[2]['orderBy'])
        )
            $orderBy = $arguments[2]['orderBy'];

        if (
            ($orderBy)
            && is_array($orderBy)
        )
            foreach ($orderBy as $ordering => $direction)
                $query->orderBy($ordering . ' ' . $direction);

        // set limit
        $limit = $this->defaultLimit;
        if ($arguments[2]['limit'])
            $limit = $arguments[2]['limit'];

        if ($limit)
            $query->limit(intval($arguments[2]['limit']));

        // check if one of the actions is called
        if ($cacheName) {

            // try to load data from cache
            if (! $result = $this->getCache()->getContent($query)) {

                // set enabled fields now since the timestamps would kill the cache
                $this->getWhereClauseForEnableFields($query);

                // get results
                $result = NULL;
                if ((substr($methodName, 0, 7) === 'countBy')) {
                    $result = $this->getOrientDbDatabase()->count($query);
                } else {
                    $result = $this->getOrientDbDatabase()->execute($query);
                }

                // save results in cache
                $this->getCache()->setContent(
                    $result,
                    array(
                        'orientdb_' . $cacheName,
                        'orientdb_vertexes',
                        'orientdb_vertex_' . Common::underscore($this->getOrientDbClass())
                    )
                );
            }

            if (substr($methodName, 0, 9) === 'findOneBy')
                return $result->first();
                //===

            return $result;
            //===
        }

        throw new \RKW\RkwSearch\Exception ('The method "' . $methodName . '" is not supported by the repository.', 1417707496);
        //===
    }


    /**
     * Sets the property names to order the result by per default.
     * Expected like this:
     * array(
     * 'foo' => \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_ASCENDING,
     * 'bar' => \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING
     * )
     *
     * @param array $defaultOrderings The property names to order by
     * @return void
     * @api
     */
    public function setDefaultOrderings(array $defaultOrderings) {
        $this->defaultOrderings = $defaultOrderings;
    }



    /**
     * Sets the default limit.
     *
     * @param integer $limit
     * @return void
     * @api
     */
    public function setDefaultLimit($limit) {
        $this->defaultLimit = $limit;
    }


    /**
     *  Constructor
     *
     * @param \RKW\RkwSearch\Search\QueryFactory $queryFactory
     */
    public function __construct ($queryFactory = NULL) {

        if ($queryFactory)
            $this->setQueryFactory($queryFactory);
    }



    #==============================================================================================
    /**
     * @toDo: EDIT FROM HERE BELOW!
     */



    /**
     * Finds an object matching the given identifier.
     *
     * @param mixed $identifier The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findByIdentifier($identifier) {
        /**
         * @todo: This method must be changed again in 6.2 + 1
         * This is marked @deprecated to be found in cleanup sessions.
         *
         * The repository should directly talk to the backend which
         * does not respect query settings of the repository as
         * findByIdentifier is strictly defined by finding an
         * undeleted object by its identifier regardless if it
         * is hidden/visible or a versioning/translation overlay.
         *
         * As a consequence users will be forced to overwrite this method
         * and mimic this behaviour to be able to find objects by identifier
         * respecting their query settings from 6.1 + 1 on.
         */
        if ($this->session->hasIdentifier($identifier, $this->objectType)) {
            $object = $this->session->getObjectByIdentifier($identifier, $this->objectType);
        } else {
            $query = $this->createQuery();
            $query->getQuerySettings()->setRespectStoragePage(FALSE);
            $query->getQuerySettings()->setRespectSysLanguage(FALSE);
            $object = $query->matching($query->equals('uid', $identifier))->execute()->getFirst();
        }

        return $object;
    }



    /**
     * Sets the default query settings to be used in this repository
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $defaultQuerySettings The query settings to be used by default
     * @return void
     * @api
     */
    public function setDefaultQuerySettings(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $defaultQuerySettings) {
        $this->defaultQuerySettings = $defaultQuerySettings;
    }

    /**
     * Returns a query for objects of this repository
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     * @api
     */
    public function createQuery() {
        $query = $this->persistenceManager->createQueryForType($this->objectType);
        if ($this->defaultOrderings !== array()) {
            $query->setOrderings($this->defaultOrderings);
        }
        if ($this->defaultQuerySettings !== NULL) {
            $query->setQuerySettings(clone $this->defaultQuerySettings);
        }
        return $query;
    }



}
?>