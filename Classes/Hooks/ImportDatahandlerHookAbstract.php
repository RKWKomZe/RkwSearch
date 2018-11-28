<?php
namespace RKW\RkwSearch\Hooks;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwBasics\Helper\QueryTypo3;
use RKW\RkwSearch\OrientDb\Helper\Tca\Typo3Fields;
use RKW\RkwSearch\OrientDb\Helper\Tca\OrientDbFields;
use RKW\RkwSearch\Helper\Text;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Class ImportDatahandlerHookAbstract
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */


abstract class ImportDatahandlerHookAbstract extends ImportHookAbstract implements ImportDatahandlerHookInterface {

    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_CLEAR_PAGE_VARNISH = 'afterImportClearVarnishCachePage';

    /**
     * @var string The action of the hook
     */
    protected $hookAction;

    /**
     * @var string The table the given data belongs to
     */
    protected $hookTable;

    /**
     * @var integer|string The id the given data has - may be a string also, if the data set is new
     */
    protected $hookUid;

    /**
     * @var integer The uid of the old page when page or content is moved
     */
    protected $hookOldUid;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler The datahandler object
     */
    protected $hookDataHandlerObject;

    /**
     * @var array The full record loaded from database
     */
    protected $hookRecord;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface The object to be imported
     */
    protected $hookObject;

    /**
     * @var array
     */
    protected $subpagesToUpdate = array();


    /**
     * Imports data and sends backend messages accordingly
     *
     * @param mixed $data
     * @return void
     */
    public function import($data = NULL) {

        $importedData = array ();
        try {
            try {
                try {
                    try {
                        try {

                            // check if repository and model exist
                            if (
                                ($this->hasRepository())
                                && ($this->hasModel())
                                && ($this->hasObject())
                                && (
                                    ($this->hasRecord())
                                    || ($this->getAction() == 'delete')
                                )
                            ) {

                                if ($this->isInRootline()) {

                                    // Import translations and old page, when content was moved
                                    foreach ($this->prepareImportContent() as $item) {
                                        $this->importSub($item);
                                        $importedData[] = $item->getPropertiesChanged();
                                    }

                                    // Import current data
                                    $import = $this->prepareImport();

                                    // Display success message if there was a success
                                    if ($this->importSub($import, TRUE) == 1) {
                                        $importedData[] = $import->getPropertiesChanged();

                                        // clear cache of defined pages
                                        if (
                                            ($this->getTableBase() == 'pages')
                                            || ($this->getTableBase() == 'tt_content')
                                        )  {
                                            $config = $this->getConfiguration();
                                            if (
                                                ($config['varnish'])
                                                && ($config['varnish']['clearPageList'])
                                                && ($pidList = GeneralUtility::trimExplode(',', $config['varnish']['clearPageList'], TRUE))
                                            ) {
                                                foreach ($pidList as $pid) {
                                                    // clear Varnish-Cache and FE-Cache of current page since the successfully tagging
                                                    // may result in a change in plugins (e.g. related items)
                                                    GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\CacheService')->clearPageCache($pid);
                                                    GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->dispatch(__CLASS__, self::SIGNAL_CLEAR_PAGE_VARNISH, array($pid));
                                                }
                                            }
                                        }

                                        // send success message
                                        $this->setBackendMessage('tx_rkwsearch.backend.message.header', 'tx_rkwsearch.backend.message.successfully_saved_1399294437', 'OK');
                                    }

                                } else {
                                    // $this->setBackendMessage('tx_rkwsearch.backend.warning.header', 'tx_rkwsearch.backend.warning.not_in_rootline_1495445181', 'NOTICE');
                                }
                            }

                        } catch (\RKW\RkwSearch\StorageRelationException $e) {
                            $this->setBackendMessage('tx_rkwsearch.backend.warning.header', 'tx_rkwsearch.backend.warning.relation_error_1401886050', 'WARNING', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
                        }

                    } catch (\RKW\RkwSearch\StorageWorkspaceException $e) {
                        $this->setBackendMessage('tx_rkwsearch.backend.warning.header', 'tx_rkwsearch.backend.warning.workspace_error_1401889803', 'WARNING', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
                    }

                } catch (\RKW\RkwSearch\StorageException $e) {
                    $this->setBackendMessage('tx_rkwsearch.backend.error.header', 'tx_rkwsearch.backend.error.fatal_error_1399293930', 'ERROR', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
                }

            } catch (\RKW\RkwSearch\Exception $e) {
                $this->setBackendMessage('tx_rkwsearch.backend.warning.header', 'tx_rkwsearch.backend.warning.little_problem_1399294227', 'WARNING', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
            }
        } catch (\Exception $e) {
            $this->setBackendMessage('tx_rkwsearch.backend.error.header', 'tx_rkwsearch.backend.error.fatal_error_1399293930', 'ERROR', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
        }

        $this->debug('import', $importedData);

    }



    /**
     * Imports data into OrientDb
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface
     * @param boolean $final Set this to true only for the last call of this method
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     */
    public function importSub($model, $final = FALSE) {

        // load model and repository if they exist
        $result = 0;
        if (
            ($model instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
            && (count($model->getPropertiesChanged()) > 0)
            && ($repository = $this->getRepository())
        ) {

            // ===================================================
            // try to insert
            if ($this->getAction() == 'new') {

                if (! $result = $repository->add($model))
                    throw new \RKW\RkwSearch\Exception(sprintf('Could not insert data for id=%d from "%s" into OrientDB.', $model->getUid(), ($this->getTableOverlay() && $model->getLanguageUid() > 0 ? $this->getTableOverlay() : $this->getTable())),  1398769399);
                    //===

                // set relations
                // but only if there was an insert/update at all!
                // if we have tt_content we do not change relations!
                if (
                    ($result > 0)
                    && ($final)
                    && ($this->getTableRaw() != 'tt_content')
                ) {
                    $resultRelate = $repository->relateAll($model);
                    if ($result == 2)
                        $result = $resultRelate;
                }

            // ===================================================
            // delete
            } else if ($this->getAction() == 'delete') {

                // to prevent from deleting pages when the user only deleted a tt_content element!!!
                // in case of tt_content we simply update the page
                if ($this->getTableRaw() == 'tt_content') {

                    if (! $result = $repository->update($model))
                        throw new \RKW\RkwSearch\Exception(sprintf('Could not update data id=%d from "%s" in OrientDB.', $model->getUid(), ($this->getTableOverlay() && $model->getLanguageUid() > 0 ? $this->getTableOverlay() : $this->getTable())), 1398775432);
                        //===

                } else {

                    if (! $result = $repository->remove($model))
                        throw new \RKW\RkwSearch\Exception(sprintf('Could not delete data id=%d from "%s" in OrientDB.', $model->getUid(), ($this->getTableOverlay() && $model->getLanguageUid() > 0 ? $this->getTableOverlay() : $this->getTable())), 1398775432);
                        //===

                    // in case of pages we have to delete the subpages, too!
                    if ($this->getTableBase() == 'pages') {

                        // get all subpages and delete them
                        $subpagesArray = $this->getAllSubpages($model->getUid(), array(), array(),'', TRUE);
                        foreach ($subpagesArray as $page) {
                            try {

                                // load model and remove page
                                $tempModel = $this->getModel();
                                $tempModel->setUid($page['uid']);
                                $repository->remove($tempModel);

                            } catch (\Exception $e) {
                                // do nothing since the page may simply not exist in OrientDb
                            }
                        }
                    }
                }

            // ===================================================
            // update or move
            }  else {

                if (! $result = $repository->update($model)) {

                    // if it is not tt_content we try to insert instead
                    if (! $result = $repository->add($model, TRUE))
                        throw new \RKW\RkwSearch\Exception(sprintf('Could not insert data id=%d from "%s" in OrientDB.', $model->getUid(),($this->getTableOverlay() && $model->getLanguageUid() > 0 ? $this->getTableOverlay() : $this->getTable())), 1398769399);
                        //===
                }

                // set relations and update subpages
                // but only if there was an update at all!
                // if we have tt_content we do not change relations or subpages!
                if (
                    ($result > 0)
                    && ($final)
                    && ($this->getTableRaw() != 'tt_content')
                ) {

                    $relationFieldsSuccess = array ();
                    $errorsSubRelations = array();
                    $errorsSubUpdates = array ();
                    $resultRelate = 0;

                    // check if we just handle a subpage of a import.
                    // it is so, we do NOTHING to the relations at all!!!
                    if (
                        (
                            (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('bm_pdf2content'))
                            && (! $model->getPdfImportSub())
                        )
                        || (
                            (! \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('bm_pdf2content'))
                        )
                    )
                        $resultRelate = $repository->relateAll($model, array(), $relationFieldsSuccess);

                    // set relations for subpages - but only if there has something changed
                    // load subpages that match the relations that have been updated!
                    // but ONLY if they are imported main-pages
                    // and ONLY for pages table here!!!!
                    if (
                        ($this->getTableBase() == 'pages')
                        && (($resultRelate == 1))
                        && (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('bm_pdf2content'))
                        && ($model->getPdfImport())
                        && (! $model->getPdfImportSub())
                    ){

                        $subpagesArray = $this->getAllSubpagesByFieldList(
                            $model->getUid(),
                            array(),
                            array_keys($relationFieldsSuccess),
                            ' AND tx_bmpdf2content_is_import_sub = 1'
                        );

                        // now set pages for relation-update
                        foreach ($subpagesArray as $page) {

                            // if the page has no uid, continue to the next
                            if (intval($page['uid']) < 1)
                                continue;
                                //===

                            try {

                                // load model and set relations
                                $objectOne = $this->getModel();
                                $objectOne->setUid($page['uid']);

                                foreach ($relationFieldsSuccess as $field => $objectArray) {
                                    $repository->unrelateAllByField($objectOne, $field);
                                    foreach ($objectArray as $objectTwo) {
                                        $repository->relate($objectOne, $objectTwo, $field);
                                    }
                                }

                                // set special relation for imported main pages
                                if ($pdfEdge = OrientDbFields::getCtrlField(Common::getOrientClassNameFromTableName($this->getTableBase()), 'pdfImportParentEdge')) {
                                    $repository->unrelateAll($objectOne, $pdfEdge, $repository->getOrientDbClass());
                                    $repository->relate($objectOne, $model, NULL, $pdfEdge);
                                }


                            } catch (\Exception $e) {
                                $errorsSubRelations[] = intval($page['uid']);
                            }
                        }
                    }

                    // Now to the updates of the subpages!
                    // But only if there has been an update on the main record!!!
                    if ($result == 1) {

                        foreach ($this->subpagesToUpdate as $tempModel) {
                            try {
                                $repository->update($tempModel);
                            } catch (\Exception $e) {
                                $errorsSubUpdates[] = $tempModel->getUid();
                            }
                        }
                    }

                    if (count($errorsSubRelations))
                        throw new \RKW\RkwSearch\StorageRelationException(sprintf('Could not inherit the relations of page id=%d for the following subpages in OrientDB: %s.', $model->getUid(), implode(',', $errorsSubRelations)), 1448450422);
                        //===

                    if (count($errorsSubUpdates))
                        throw new \RKW\RkwSearch\Exception(sprintf('Could not inherit the attributes of page id=%d for the following subpages in OrientDB: %s.', $model->getUid(), implode(',', $errorsSubUpdates)), 1448450416);
                        //===

                    // set return value
                    if ($result == 2)
                        $result = $resultRelate;

                }
            }
        }

        return $result;
        //===
    }



    /**
     * Prepares the import data for current record
     *
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface|NULL
     */
    public function prepareImport() {

        // for the pages we load all tt_content - elements
        if ($this->getTableBase() == 'pages') {

            if ($this->hasObject()) {
                $model = $this->getObject();
                $model->unsContent();

                // get uid - or use the pid of the overlay page
                // since tt_content always takes the "normal" pid as reference
                $id = $model->getUid();
                if ($this->isTableOverlay())
                    $id = $model->getLanguageOverlayUid();

                if ($id)
                    if ($content = Text::stripHtml(Text::mergeToString($this->getMappedRecordsByPid($id, 'tt_content', $model->getLanguageUid()))))
                        $model->setContent($content);

                $this->getRootlineData($model);

                //=============================================
                // if we deal with a imported parent (!) page here, we also load all
                // contents of all the sub-pages here!
                if (
                    (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('bm_pdf2content'))
                    && ($model->getPdfImport())
                    && (! $model->getPdfImportSub())
                ) {

                    // get all subpages - and use the "normal" uid (non-language-overlay)
                    $subpagesArray = $this->getAllSubpages(
                        $id,
                        array(),
                        array_flip(array('title', 'subtitle')),
                        ' AND tx_bmpdf2content_is_import_sub = 1'
                    );

                    $subPageContent = NULL;
                    foreach ($subpagesArray as $page) {

                        // if the page has no uid, continue to the next
                        if (intval($page['uid']) < 1)
                            continue;
                            //===

                        // load model and set relations
                        $subPageObject = $this->getModel();
                        $subPageObject->setProperties($page);

                        // get uid of page and load it's content
                        // take language of parent page
                        $subPageUid = $subPageObject->getUid();
                        if ($subPageUid)
                            if ($tempContent = Text::stripHtml(Text::mergeToString($this->getMappedRecordsByPid($subPageUid, 'tt_content', $model->getLanguageUid()))))
                                $subPageContent .= $page['title'] . '. ' . $page['subtitle'] . '. ' . $tempContent . '. ';

                    }

                    // add subpage- content to existing one
                    if ($subPageContent) {

                        if ($model->getContent()) {
                            $model->setContent(Text::stripHtml(Text::stripTrailingLineBreaks($model->getContent() . '. ' . $subPageContent)));
                        } else {
                            $model->setContent(Text::stripHtml(Text::stripTrailingLineBreaks($subPageContent)));
                        }
                    }
                }
            }
        }

        if ($this->hasObject())
            return $this->getObject();
            //===

        return NULL;
        //===

    }


    /**
     * Prepares the import data for tt_content.
     * This is needed, because content-elements can be copied into other languages
     *
     * @return array
     */
    public function prepareImportContent() {

        $importData = array ();
        if ($this->getTableRaw() == 'tt_content') {

            // get all localized pages
            $localizedRecords = $this->getMappedLanguageOverlayRecordsByUid($this->getObject()->getUid());

            // if there is an old pid, the content is moved
            if (
                ($this->getAction() == 'move')
                && ($this->getOldUid())
            ) {

                // then we fetch all localized versions of the current and the old page
                $localizedRecords = array_merge (
                    $this->getMappedLanguageOverlayRecordsByUid($this->getOldUid()),
                    $this->getMappedLanguageOverlayRecordsByUid($this->getObject()->getUid())
                );

                // get data for old page in default language
                if ($oldRecord = $this->getMappedRecordByUid($this->getOldUid(), $this->getTable())) {
                    if ($modelOldRecord = $this->getModel($oldRecord))   {

                        // load tt_content record from old page - only in default language!
                        $modelOldRecord->unsContent();
                        if ($content = Text::stripHtml(Text::mergeToString($this->getMappedRecordsByPid($this->getOldUid(), 'tt_content', 0))))
                            $modelOldRecord->setContent($content);

                        $this->getRootlineData($modelOldRecord);
                        $importData[] = $modelOldRecord;
                    }
                }
            }


            // check if there are some localized records
            if (
                (is_array($localizedRecords))
                && (! empty($localizedRecords))
            ){

                // now get the contents of each (localized) page
                foreach ($localizedRecords as $localizedRecord) {

                    // load data to model
                    $localizedModel = $this->getModel($localizedRecord);

                    // get content
                    $localizedModel->unsContent();
                    if ($pid = $localizedModel->getLanguageOverlayUid())
                        if ($content = Text::stripHtml(Text::mergeToString($this->getMappedRecordsByPid($pid, 'tt_content', $localizedModel->getLanguageUid()))))
                            $localizedModel->setContent($content);

                    $this->getRootlineData($localizedModel);
                    $importData[] = $localizedModel;
                    unset($localizedModel);
                }
            }

        }

        return $importData;
        //===
    }


    /**
     * Gets records of table and map fieldnames to OrientDB
     *
     * @param int $pid Pid to fetch content for
     * @param string $table The table to fetch contents from
     * @param integer $languageUid The languageUid of the content
     * @param array $fields List of fields to fetch
     * @return array | NULL
     * @throws \RKW\RkwSearch\StorageException
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getMappedRecordsByPid($pid, $table, $languageUid = 0, $fields = array ('header','subheader','bodytext') ) {

        if (intval($pid) < 1)
            throw new \RKW\RkwSearch\Exception('No valid pid given.', 1399301976);
            //===

        if (empty($table))
            throw new \RKW\RkwSearch\Exception('No valid table given.', 1399301778);
            //===

        if (! is_array($fields))
            throw new \RKW\RkwSearch\Exception('No valid field array given.', 1399301734);
            //===

        $result = NULL;
        if ($this->debugMode) {

            // return debug data
            if (
                ($this->debugArray['getMappedRecordsByPid'])
                && ($this->debugArray['getMappedRecordsByPid'][$table . '_' . $pid . '_' . $languageUid])
            )
                return $this->debugArray['getMappedRecordsByPid'][$table . '_' . $pid . '_' . $languageUid];
                //===

        } else {

            // $this->getTypo3Database()->store_lastBuiltQuery = 1;

            try {

                // get all content-elements from that page in correct order
                $result = $this->getTypo3Database()->exec_SELECTgetRows(
                    implode(',', $fields),
                    $table,
                    'pid = ' . intval($pid) .
                        QueryTypo3::getWhereClauseForLanguageFields($table, $languageUid) .
                        QueryTypo3::getWhereClauseForEnableFields($table) .
                        QueryTypo3::getWhereClauseForVersioning($table),
                    '',
                    ($GLOBALS['TCA'][$table]['ctrl']['sortby']) ? $GLOBALS['TCA'][$table]['ctrl']['sortby'] . ' ASC' : ''
                );

            } catch (\Exception $e) {
                throw new \RKW\RkwSearch\StorageException ($e->getMessage(), 1398770103);
                //===
            }

            // var_dump($this->getTypo3Database()->debug_lastBuiltQuery);

        }

        return $result;
        //===
    }


    /**
     * Gets record from given table and map fieldnames to OrientDB
     *
     * @param int $uid Uid to fetch content for
     * @param string $table The table to fetch data from
     * @throws \RKW\RkwSearch\StorageException
     * @throws \RKW\RkwSearch\Exception
     * @return array | NULL
     */
    protected function getMappedRecordByUid($uid, $table) {

        if (intval($uid) < 1)
            throw new \RKW\RkwSearch\Exception('No valid uid given.', 1423665439);
            //===

        // $this->getTypo3Database()->store_lastBuiltQuery = 1;
        $result = NULL;
        $idField = 'uid';

        if ($this->debugMode) {

            // return debug data
            if (
                ($this->debugArray['getMappedRecordByUid'])
                && ($this->debugArray['getMappedRecordByUid'][$table . '_' . $uid])
            )
                return $this->debugArray['getMappedRecordByUid'][$table . '_' . $uid];
                //===

        } else {

            try {

                // $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

                // get all content-elements from that page in correct order
                $result = $this->getTypo3Database()->exec_SELECTgetSingleRow(
                    implode(',', $this->getQueryFields($table)),
                    $table,
                    $idField . ' = ' . intval($uid) .
                    QueryTypo3::getWhereClauseForVersioning($table)

                    /* @toDo: if not commented out, we can not hide or delete records!
                     * QueryTypo3::getWhereClauseForEnableFields($table) .
                     *
                     * @toDo: commented out since we are searching for uids!
                     * QueryTypo3::getWhereClauseForLanguageFields($table, $lid) .
                     */

                );

                //$GLOBALS['TYPO3_DB']->debug_lastBuiltQuery;


            } catch (\Exception $e) {
                throw new \RKW\RkwSearch\StorageException ($e->getMessage(), 1398770103);
                //===
            }

        }

        return $result;
        //===
    }

    /**
     * Gets all language overlay records of given id
     *
     * @param int $id Pid to fetch content for
     * @throws \RKW\RkwSearch\StorageException
     * @throws \RKW\RkwSearch\Exception
     * @return array
     */
    protected function getMappedLanguageOverlayRecordsByUid($id) {

        if (intval($id) < 1)
            throw new \RKW\RkwSearch\Exception('No valid id given.', 1399301929);
            //===


        // if the table is localized by an overlay table
        // try to load all localized records of the given uid
        $result = array ();
        if (
            ($table = $this->getTableOverlay())
            && (Typo3Fields::hasLanguageOriginalPointerField($table))
        ) {
            $idField = Typo3Fields::getLanguageOriginalPointerField($table);

            if ($this->debugMode) {

                // return debug data
                if (
                    ($this->debugArray['getMappedLanguageOverlayRecordsByUid'])
                    && ($this->debugArray['getMappedLanguageOverlayRecordsByUid'][$table . '_' . $id])
                )
                    return $this->debugArray['getMappedLanguageOverlayRecordsByUid'][$table . '_' . $id];
                    //===

            } else {

                try {

                    // $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

                    // get all content-elements from that page in correct order
                    $result = $this->getTypo3Database()->exec_SELECTgetRows(
                        implode(',', $this->getQueryFields($table)),
                        $table,
                        $idField . ' = ' . intval($id) .
                        QueryTypo3::getWhereClauseForEnableFields($table) .
                        QueryTypo3::getWhereClauseForVersioning($table)
                    );

                    // $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery;

                } catch (\Exception $e) {
                    throw new \RKW\RkwSearch\StorageException ($e->getMessage(), 1415200276);
                    //===
                }
            }

        }

        return $result;
        //===
    }


    /**

     * Gets the rootline fields of the given model
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface $model
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getRootlineData ($model) {

        if (! $model instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
            throw new \RKW\RkwSearch\Exception('No valid model given.', 1424095474);
            //===

        if (! $this->debugMode) {

            // only for non-language-overlay pages!
            if (
                ($this->getTable() == 'pages')
                && ($this->isTableOverlay() == FALSE)

            ) {

                //============================================
                // get rootline fields from configuration
                $rootlineFieldArrayMapped = Typo3Fields::getRootlineFieldsForOrientClass();

                // get PageRepository
                $repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');

                try {

                    //============================================
                    // get all relevant subpages, that are missing the relevant values!
                    // we only load pages that have missing values for the rootline fields!
                    $subpagesArray = $this->getAllSubpagesByFieldList(
                        $model->getUid(),
                        $rootlineFieldArrayMapped,
                        array_keys($rootlineFieldArrayMapped)
                    );

                    // get all rootline pages from here on
                    // IMPORTANT: This method does NOT respect enable-fields or language overlay!!!!
                    $rootlinePageArray = $repository->getRootLine($model->getUid());

                    //============================================
                    // go through all fields
                    foreach($rootlineFieldArrayMapped as $field => $mappedField) {

                        // build getter and setter
                        $get = 'get' . ucfirst(Common::camelize($mappedField));
                        $set = 'set' . ucfirst(Common::camelize($mappedField));
                        $has = 'has' . ucfirst(Common::camelize($mappedField));

                        // check if property exists
                        if (! $model->$has())
                            continue;
                            //===

                        // Performance: Check if the field has been changed at all!
                        // But only if we have the corresponding array (not available on delete or move)
                        if ($this->hookDataHandlerObject->checkValue_currentRecord)
                            if ($this->hookDataHandlerObject->checkValue_currentRecord[$field] == $model->$get())
                                continue;
                                //===


                        //============================================
                        // 1) go through all pages up the rootline and get the relevant properties for the current page
                        // but only if there are no values set in the model!
                        foreach ($rootlinePageArray as $page) {

                            // if value is set in model, we don't need to inherit this field at all!
                            if ($model->$get())
                                break;
                                //===

                            // if parent value is not empty, inherit!
                            if (! empty ($page[$field]))
                                $model->$set($page[$field]);

                        }

                        //============================================
                        // 2) now go through all subpages and set their values accordingly
                        // updates on disable or delete field are to be made no matter what happens!
                        foreach ($subpagesArray as $page) {

                            // if the page has no uid, continue to the next
                            if (intval($page['uid']) < 1)
                                continue;
                                //===

                            // if one of the pages HAS a value, we can continue
                            if (! empty ($page[$field]))
                                continue;
                                //===

                            // load old model or set new model
                            $tempModel = NULL;
                            if ($this->subpagesToUpdate[$page['uid']]) {
                                $tempModel = $this->subpagesToUpdate[$page['uid']];

                            } else {
                                $tempModel = $this->getModel();
                                $tempModel->setUid($page['uid']);
                            }

                            // set data
                            $tempModel->$set($model->$get());

                            // save data again
                            $this->subpagesToUpdate[$page['uid']] = $tempModel;
                        }
                    }

                } catch (\Exception $e) {

                    throw new \RKW\RkwSearch\Exception($e->getMessage(), 1430226476);
                    //===
                }

                return TRUE;
                //===

            }
        }

        return FALSE;
        //===

    }


    /**
     * Gets the subpages of a given page
     *
     * @param integer $pid Page ID to select subpages from.
     * @param array $inList List of page uids, this is added to and outputted in the end
     * @param array $fieldArray List of fields to load
     * @param string $where Additional where condition
     * @param boolean $includeDeleted Includes deleted pages
     * @return array List of subpages
     */
    protected function getAllSubpages($pid, $inList = array(), $fieldArray = array (), $where = '', $includeDeleted = FALSE) {

        $fieldList = 'uid';
        if (
            (is_array($fieldArray))
            && (count($fieldArray))
        )
            $fieldList = 'uid,' .  implode(',', array_keys($fieldArray));

        if (intval($pid) >= 0) {

            //$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

            $mres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                $fieldList,
                $this->getTableBase(),
                'pid=' . intval($pid) . QueryTypo3::getWhereClauseForVersioning($this->getTableBase()) . ($includeDeleted ? '' : QueryTypo3::getWhereClauseForDeleteFields($this->getTableBase())) . $where,
                '',
                'sorting'
            );

            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($mres)) {

                //var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);

                // save result
                $inList[] = $row;

                // Follow the subpages recursively...
                $inList = $this->getAllSubpages($row['uid'], $inList, $fieldArray, $where);
            }

            $GLOBALS['TYPO3_DB']->sql_free_result($mres);


        }

        return $inList;
        //===
    }

    /**
     * Gets the subpages of a given page
     *
     * @param integer $pid Page ID to select subpages from.
     * @param array $fieldArray List of fields to load
     * @param array $fieldList Relation fields to filter by
     * @param string $where Additional where condition
     * @return array List of subpages
     */
    protected function getAllSubpagesByFieldList($pid, $fieldArray = array (), $fieldList = array (), $where = '') {

        // Build query to reduce queries!
        $relationFieldsCondition = array ();
        foreach ($fieldList as $field) {

            // Inheritance of relations is only possible for 1:n relations
            if (
                ($this->getRepository()->getRelationMmTable($field))
                || ($this->getRepository()->getRelationForeignTable($field) == 'sys_file_metadata')
            )
                continue;
                //===

            $relationFieldsCondition[] = '(' . $field . ' = 0 OR ' . $field . ' = "" OR ' . $field . ' IS NULL)';
        }

        // Get subpages!
        return $this->getAllSubpages($pid, array(), $fieldArray, ($relationFieldsCondition ? ' AND (' . implode(' OR ', $relationFieldsCondition) .')' : '') . $where);
        //===

    }


    /**
     * Gets uid of element from given hook object
     *
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     */
    public function getUid () {

        //  if data is new, we have to take it from the substNEWwithIDs- array
        $object = $this->getDatabaseHandler();
        if (! is_numeric($this->hookUid))
            $this->hookUid = intval($object->substNEWwithIDs[$this->hookUid]);

        if ($this->hookUid < 1)
            throw new \RKW\RkwSearch\Exception('No valid uid available.', 1398765794);
            //===

        return $this->hookUid;
        //===
    }



    /**
     * Gets the table of the given data
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getOldUid() {

        return $this->hookOldUid;
        //===
    }



    /**
     * Gets the action of the given data
     *
     * @param boolean $switch If set to FALSE we return the value "as is"
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getAction ($switch = TRUE) {

        if (empty($this->hookAction))
            throw new \RKW\RkwSearch\Exception('No valid action given.', 1399301778);
            //===

        // inserting new tt_content elements only leads to an update of the page
        if ($switch)
            if ($this->getTableRaw() == 'tt_content')
                if (
                    ($this->hookAction == 'new')
                    || ($this->hookAction == 'swap')
                )
                    return 'update';
                    //===

        return $this->hookAction;
        //===
    }


    /**
     * Gets the table of the given data
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getTable() {

        if (empty($this->hookTable))
            throw new \RKW\RkwSearch\Exception('No valid table given.', 1399301778);
            //===

        // special treatment for tt_content
        if ($this->hookTable == 'tt_content')
            return 'pages';
            //===

        return $this->hookTable;
        //===
    }


    /**
     * Gets the table of the given data without "cleanup"
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getTableRaw() {

        if (empty($this->hookTable))
            throw new \RKW\RkwSearch\Exception('No valid table given.', 1399301778);
            //===

        return $this->hookTable;
        //===
    }


    /**
     * Get the language overlay table if one exists
     *
     * @return string|NULL
     */
    public function getTableOverlay() {

        // always take the non-overlayed-table
        $table = $this->getTableBase();

        // then check for overlay table
        if (
            (Typo3Fields::hasLanguageForeignTable($table))
            && ($overlayTable = Typo3Fields::getLanguageForeignTable($table))
        )
            return $overlayTable;
            //===

        return NULL;
        //===
    }

    /**
     * Get the base table if the language overlay table is used for translation
     *
     * @return string
     */
    public function getTableBase() {

        // always take the non-overlayed-table
        $table = $this->getTable();
        if (Typo3Fields::hasLanguageOriginalPointerTable($table))
            return Typo3Fields::getLanguageOriginalPointerTable($table);
            //===

        return $table;
        //===
    }




    /**
     * Checks if the given table is an language overlay table
     *
     * @return boolean
     */
    public function isTableOverlay() {

        if (
            (Typo3Fields::hasLanguageOriginalPointerTable($this->hookTable))
            && (Typo3Fields::hasLanguageOriginalPointerField($this->hookTable))
        )
            return TRUE;
            //===

        return FALSE;
        //===

    }

    /**
     * Checks if page is in defined rootline
     *
     * @return bool
     */

    public function isInRootline () {

        $configuration = $this->getConfiguration();
        if (
            ($this->hasObject())
            && ($model = $this->getObject())
            && ($rootPages = GeneralUtility::trimExplode(',', $configuration['rootPages']))
            && (! empty($rootPages))
        ) {

            // special treatment for pages!
            $pid = $model->getPid();
            if ($this->getTable() == 'pages')
                $pid = $model->getUid();

            // get rootline of page
            $rootLine = BackendUtility::BEgetRootLine($pid);

            // check
            if (is_array($rootLine)) {
                foreach ($rootLine as $level => $data) {
                    if (in_array($data['uid'], $rootPages)) {
                        return true;
                        //===
                    }
                }
            }

            return false;
            //===

        }

        return true;
        //===

    }


    /**
     * Gets the data record
     *
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRecord() {

        if (! $this->hookRecord)
            throw new \RKW\RkwSearch\Exception('No valid record record loaded.', 1424086061);
            //===

        return $this->hookRecord;
        //===
    }


    /**
     * Check if there is a record
     *
     * @return boolean
     */
    public function hasRecord() {

        return ! empty($this->hookRecord);
        //===
    }


    /**
     * Gets the data record
     *
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getObject() {

        if (! $this->hookObject instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
            throw new \RKW\RkwSearch\Exception('No valid record model loaded.', 1423659256);
            //===

        return $this->hookObject;
        //===
    }


    /**
     * Check if there is a record
     *
     * @return boolean
     */
    public function hasObject() {

        if ($this->hookObject instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
            return TRUE;
            //===

        return FALSE;
        //===
    }


    /**
     * Gets the DataHandler object
     *
     * @return \TYPO3\CMS\Core\DataHandling\DataHandler
     * @throws \RKW\RkwSearch\Exception
     */
    public function getDatabaseHandler() {

        if (! $this->hookDataHandlerObject instanceof \TYPO3\CMS\Core\DataHandling\DataHandler)
            throw new \RKW\RkwSearch\Exception('No valid object given.', 1399301510);
            //===

        return $this->hookDataHandlerObject;
        //===
    }


    /**
     * Returns repository for given table
     *
     * @return \RKW\RkwSearch\OrientDb\Domain\Repository\RepositoryInterface | NULL
     */
    public function getRepository() {

        // check if classes exist and load it
        if ($repositoryName = $this->hasRepository($this->getTableBase())) {
            $repository= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($repositoryName);

            // set debug mode and delete mode for repository
            $repository->setDebugMode($this->debugMode);
            if ($this->debugMode)
                $repository->setDeleteHard(TRUE);

            return $repository;
            //===
        }

        return NULL;
        //===

    }

    /**
     * Checks if repository for given table exists and returns its name
     *
     * @return string | NULL
     */
    public function hasRepository() {

        // get names of class
        $repositoryName = Common::getOrientRepositoryFromTableName($this->getTableBase());

        // check if classes exist
        if (class_exists($repositoryName))
            return $repositoryName;
            //===

        return NULL;
        //===

    }

    /**
     * Returns model for given table
     *
     * @param array $data Data to be loaded into object
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface | NULL
     */
    public function getModel($data = array()) {

        // check if classes exist
        if ($modelName = $this->hasModel())
            return GeneralUtility::makeInstance($modelName, $data);
            //===

        return NULL;
        //===

    }

    /**
     * Checks if model for given table exists and returns its name
     *
     * @return string | NULL
     */
    public function hasModel() {

        // get names of class
        $modelName = Common::getOrientModelFromTableName($this->getTableBase());

        // check if classes exist
        if (class_exists($modelName))
            return $modelName;
            //===

        return NULL;
        //===

    }


    /**
     * Gets an array of mapped fields based on the given table
     *
     * @param string $table The table to fetch contents from
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getQueryFields ($table) {

        if (empty($table))
            throw new \RKW\RkwSearch\Exception('No valid table given.', 1399301778);
            //===

        // define fields to select
        // special treatment for delete-action, since on deleted items there is not rootline-data
        // and thus we are to override previously inherited values!
        $fields = array_merge (
            Typo3Fields::getDefaultFieldsForOrientClass($table, $this->getAction()),
            Typo3Fields::getVersionFields($table),
            (($this->getAction() == 'delete') ? array () : Typo3Fields::getFieldsForOrientClass($table))
        );

        return $fields;
        //===
    }




    /**
     * Sets the init data of the object
     *
     * @param string $hookAction Status of the current operation, 'new', 'swap', 'update' or 'delete'
     * @param string $hookTable The table currently processing data for
     * @param integer $hookUid The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param object $hookDataHandlerObject (reference) \TYPO3\CMS\Core\DataHandling\DataHandler
     * @param integer $hookOldUid Id of page record an element has been moved from, only available when record is moved
     * @return void
     * @throws \RKW\RkwSearch\Exception
     */
    public function init ($hookAction, $hookTable, $hookUid, $hookDataHandlerObject, $hookOldUid = NULL) {

        $this->hookUid = $hookUid;
        $this->hookAction = $hookAction;
        $this->hookTable = $hookTable;
        $this->hookOldUid = $hookOldUid;
        $this->hookDataHandlerObject = $hookDataHandlerObject;

        // load full data of element from database
        // and create model
        if (
            ($this->hasModel())
            && ($model = $this->getModel())
        ) {

            // load basic data - hookRecord may be empty in the case that the entry was deleted completely from database
            // for this case we at least set the uid of the model
            $uid = $this->getUid();
            $data = $this->hookRecord = $this->getMappedRecordByUid($uid, $this->getTableRaw());
            $model->setUid($uid);

            // now load data into object
            if ($this->hasRecord()) {

                // special treatment for tt_content
                // load page from pid of tt_content
                if ($this->getTableRaw() == 'tt_content') {
                    $uid = $this->hookRecord['pid'];
                    if ($uid)
                        $data = $this->getMappedRecordByUid($uid, $this->getTable());
                }

                // set data
                if ($data) {
                    $model->setProperties($data);
                }
            }

            // set hookObject
            $this->hookObject = $model;
        }

    }



}
?>