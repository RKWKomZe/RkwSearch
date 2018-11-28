<?php
namespace RKW\RkwSearch\Controller;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \RKW\RkwSearch\OrientDb\Helper\Common;
use \RKW\RkwSearch\OrientDb\Helper\Tca\OrientDbFields;
use \RKW\RkwSearch\FuzzySearch\ColognePhonetic;
use \RKW\RkwSearch\Helper\Text;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Class SearchCommandController
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB\Binding
 */
class SearchCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {


    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_CLEAR_PAGE_VARNISH = 'afterIndexClearVarnishCachePage';

    /**
     * @var \RKW\RkwSearch\Domain\Repository\RidMappingRepository
     * @inject
     */
    protected $ridMappingRepository;


    /**
     * @var \RKW\RkwSearch\Domain\Repository\QueueTaggedContentRepository
     * @inject
     */
    protected $queueTaggedContentRepository;


    /**
     * @var \RKW\RkwSearch\Domain\Repository\QueueAnalysedKeywordsRepository
     * @inject
     */
    protected $queueAnalysedKeywordsRepository;


    /**
     * @var \RKW\RkwSearch\Keywords\Analyser
     * @inject
     */
    protected $keywordAnalyser;


    /**
     * @var \RKW\RkwSearch\Keywords\Indexer
     * @inject
     */
    protected $keywordIndexer;

    /**
     * @var \RKW\RkwSearch\Keywords\Fetcher
     * @inject
     */
    protected $keywordFetcher;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;


    /**
     * @var \RKW\RkwSearch\OrientDb\Cache\RepositoryCache
     * @inject
     */
    protected $cache;



    /**
     * Tags the contents with TreeTagger and saves the result as serialized string in database
     *
     * @param integer $itemsToProcess
     */
    public function tagContentsCommand($itemsToProcess = 0) {

        try {

            // get all entries that have not been tagged so long
            $result = $this->ridMappingRepository->findNotQueueTaggedContents($itemsToProcess);

            if ($result) {

                $repository = NULL;

                /** @var \RKW\RkwSearch\Domain\Model\RidMapping $entry */
                foreach ($result as $entry) {

                    try {

                        // load tagger
                        /** @var \RKW\RkwSearch\TreeTagger\TreeTagger $tagger */
                        $tagger = GeneralUtility::makeInstance('RKW\\RkwSearch\\TreeTagger\\TreeTagger', array ($entry->getT3lid()));

                        if (
                            ($className = $entry->getClass())
                            && ($repositoryName = Common::getOrientRepositoryFromClassName($entry->getClass()))
                            && (class_exists($repositoryName))
                        ) {

                            // prevent loading new repository if not necessary!
                            /** @var $repository \RKW\RkwSearch\OrientDb\Domain\Repository\RepositoryAbstract */
                            if (! $repository instanceof $repositoryName)
                                $repository = GeneralUtility::makeInstance($repositoryName);

                            // now get the data of the object
                            // and tag the text via TreeTagger
                            if (
                                ($element = $repository->findByRid($entry->getRid()))
                                && ($element instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
                                && ($content = $this->keywordFetcher->getContent($element))
                                && ($tagResult = $tagger->setText($content)->execute()->getFilteredResults('distance'))
                                && ($searchFieldAbstract = OrientDbFields::getCtrlField('DocumentAbstract', 'searchField'))
                                && (count($tagResult) > 0)
                            ) {

                                // save text to database fields
                                // here we save it to global search fields that can be used for a fulltext search
                                $setterSearchFieldAbstract = 'set' . ucFirst($searchFieldAbstract);
                                $getterSearchFieldAbstract = 'get' . ucFirst($searchFieldAbstract);
                                $element->$setterSearchFieldAbstract(Text::sanitizeString($content, $entry->getT3lid()));

                                if ($searchFieldAbstractFuzzy = OrientDbFields::getCtrlField('DocumentAbstract', 'searchFieldFuzzy')) {
                                    $setterSearchFieldAbstractFuzzy = 'set' . ucFirst($searchFieldAbstractFuzzy);
                                    $element->$setterSearchFieldAbstractFuzzy(ColognePhonetic::encode(Text::sanitizeString($content, $entry->getT3lid())));
                                }

                                // check if there is some titleField to search for
                                if ($searchFieldAbstractTitle = OrientDbFields::getCtrlField('DocumentAbstract', 'searchFieldTitle')) {
                                    $setterSearchFieldAbstractTitle = 'set' . ucFirst($searchFieldAbstractTitle);
                                    $element->$setterSearchFieldAbstractTitle($this->keywordFetcher->getContent($element, 'fieldListTitle'));
                                }

                                // check if the given element has been imported via PDF-Importer
                                if ($searchFieldAbstractType = OrientDbFields::getCtrlField('DocumentAbstract', 'searchFieldType')) {
                                    $setterSearchFieldAbstractType = 'set' . ucFirst($searchFieldAbstractType);
                                    $type = 'default';
                                    if ($element->getPdfImport()) {
                                        $type = 'pdf';
                                             if ($element->getPdfImportSub())
                                            $type = 'pdfSub';
                                    }
                                    $element->$setterSearchFieldAbstractType($type);
                                }

                                // get the size of the content
                                if ($searchFieldAbstractSize = OrientDbFields::getCtrlField('DocumentAbstract', 'searchFieldSize')) {
                                    $setterSearchFieldAbstractSize = 'set' . ucFirst($searchFieldAbstractSize);

                                    $length = 'small';
                                    $contentLength = strlen($element->$getterSearchFieldAbstract());

                                    if ($contentLength > 2500)
                                        $length = 'medium';

                                    if ($contentLength > 10000)
                                        $length = 'big';

                                    $element->$setterSearchFieldAbstractSize($length);
                                }

                                // now we additionally save it in special fields of the class - but only the fuzzy-part of it!
                                foreach (array ('searchField', 'searchFieldTwo') as $fieldLabel) {

                                    if ($searchField = OrientDbFields::getCtrlField($className, $fieldLabel)) {

                                        /* This would override existing values of imported date if the fields are no especially set for search
                                           and this does not make sense here!

                                            $setterSearchField = 'set' . ucFirst($searchField);
                                           $element->$setterSearchField(Text::sanitizeString($content, $entry->getT3lid()));
                                        */

                                        if (($searchFieldFuzzy = OrientDbFields::getCtrlField($className, $fieldLabel . 'Fuzzy'))) {
                                            $setterSearchFieldFuzzy = 'set' . ucFirst($searchFieldFuzzy);
                                            $element->$setterSearchFieldFuzzy(ColognePhonetic::encode(Text::sanitizeString($content, $entry->getT3lid())));
                                        }
                                    }
                                }

                                $repository->update($element);

                                // create target model
                                $taggedContentModel = GeneralUtility::makeInstance('RKW\\RkwSearch\\Domain\\Model\\QueueTaggedContent');
                                $taggedContentModel->setRidMapping($entry);

                                // store results and reset status
                                $taggedContentModel->setSerialized($tagResult);
                                $taggedContentModel->setStatus(0);
                                $taggedContentModel->setMessage('waiting');

                                // set the timestamp for the entry
                                $entry->setTagTstamp(time());

                                // update statuses
                                $this->ridMappingRepository->updateStatusAll($entry, 1, NULL, NULL);

                                // add data to repository and persist it!
                                $this->queueTaggedContentRepository->add($taggedContentModel);
                                $this->persistenceManager->persistAll();

                                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Successfully tagged content mapping-uid "%s".', $entry->getUid()));

                            // nothing to tag - maybe the no text available
                            // this is no error at all
                            } else {

                                // set the timestamp for the entry
                                $entry->setTagTstamp(time());

                                // update statuses
                                $this->ridMappingRepository->updateStatusAll($entry, 4, NULL, NULL);

                                $this->persistenceManager->persistAll();
                                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('There is no content for tagging or there was nothing that matched the tagging criteria. Item with mapping-uid "%s" was therefore removed.', $entry->getUid()));
                            }

                        } else {

                            throw new \RKW\RkwSearch\Exception(sprintf('Necessary models or repositories for class "%s" could not be loaded.', $entry->getClass()), 1422543416);
                            //===
                        }


                    } catch (\RKW\RkwSearch\Exception $e) {

                        // update statuses
                        $this->ridMappingRepository->updateStatusAll($entry, 99, NULL, NULL);

                        $this->persistenceManager->persistAll();
                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An catchable error occurred while trying to tag content. Item with mapping-uid "%s" was therefore suspended. Message: %s', $entry->getUid(), str_replace(array ("\n", "\r"), '', $e->getMessage())));
                    }
                }
            }

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to tag content. Message: %s', str_replace(array ("\n", "\r"), '', $e->getMessage())));
        }

    }


    /**
     * Analyses the tagged content and saves the results in the database
     *
     * @param integer $itemsToProcess
     */
    public function analyseContentsCommand($itemsToProcess) {

        try {

            // get all entries that have not been tagged so long
            $result = $this->queueTaggedContentRepository->findByCrdateAndStatus($itemsToProcess);

            if ($result) {

                /** @var \RKW\RkwSearch\Domain\Model\QueueTaggedContent $entry */
                foreach ($result as $entry) {

                    try {

                        if (! $entry->getRidMapping())
                            throw new \RKW\RkwSearch\Exception('RidMappingId is not set.', 1422515964);
                            //===

                        // block source entry for other cronjobs
                        $entry->setStatus(2);
                        $entry->setMessage('blocked');
                        $this->queueTaggedContentRepository->update($entry);
                        $this->persistenceManager->persistAll();

                        if (
                            ($className = $entry->getRidMapping()->getClass())
                            && ($repositoryName = Common::getOrientRepositoryFromClassName($entry->getRidMapping()->getClass()))
                            && (class_exists($repositoryName))
                        ) {

                            // prevent loading new repository if not necessary!
                            /** @var $repository \RKW\RkwSearch\OrientDb\Domain\Repository\RepositoryAbstract */
                            if (!$repository instanceof $repositoryName)
                                $repository = GeneralUtility::makeInstance($repositoryName);

                            // now get the data of the object
                            // and tag the text via TreeTagger
                            if (
                                ($element = $repository->findByRid($entry->getRidMapping()->getRid()))
                                && ($element instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
                                && ($serializedContent = $entry->getSerialized())
                                && ($serializedContent instanceof \RKW\RkwSearch\TreeTagger\Collection\FilteredRecords)
                            ) {

                                // do analysis
                                // Note: Here the results have to be sorted by keyword-length!
                                // otherwise the setting of edges to shorter keywords in the next step won't work properly!!!
                                $this->keywordAnalyser->setData($serializedContent)
                                    ->countMatches()
                                    ->weightMatches();
                                $analyseResult = $this->keywordAnalyser->getResults('length');

                                // update status if result is not valid!
                                if (
                                    ($analyseResult)
                                    && ($analyseResult instanceof \RKW\RkwSearch\Collection\AnalysedKeywords)
                                ) {

                                    // create target model
                                    $analysedKeywordsModel = GeneralUtility::makeInstance('RKW\\RkwSearch\\Domain\\Model\\QueueAnalysedKeywords');
                                    $analysedKeywordsModel->setRidMapping($entry->getRidMapping());

                                    // set results and update status
                                    $analysedKeywordsModel->setSerialized($analyseResult);
                                    $analysedKeywordsModel->setStatus(0);
                                    $analysedKeywordsModel->setMessage('waiting');
                                    $this->queueAnalysedKeywordsRepository->add($analysedKeywordsModel);

                                    // delete source from tagged content-table
                                    $this->queueTaggedContentRepository->remove($entry);

                                    // get best matching keywords for database field
                                    $element->setTopKeywords($this->keywordAnalyser->getTopKeywords(50));
                                    $repository->update($element);

                                    // now get short-result for the pages-properties
                                    $resultDataText = '';
                                    if (in_array($entry->getRidMapping()->getT3table(), array('pages', 'pages_language_overlay')))
                                        $resultDataText = $this->keywordAnalyser->getResultsSummary(100, 'weight');

                                    // set the timestamp for the entry
                                    $entry->getRidMapping()->setAnalyseTstamp(time());

                                    // update statuses
                                    $this->ridMappingRepository->updateStatusAll($entry->getRidMapping(), 2, NULL, $resultDataText);
                                    $this->persistenceManager->persistAll();

                                    // clear Varnish-Cache and FE-Cache of current page since the successfully tagging
                                    // may result in a change in plugins (e.g. related items)
                                    $this->getCache()->getCacheManager()->flushCachesByTag('orientdb_related_' . $entry->getRidMapping()->getClass());
                                    GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\CacheService')->clearPageCache($entry->getRidMapping()->getT3id());
                                    GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->dispatch(__CLASS__, self::SIGNAL_CLEAR_PAGE_VARNISH, array($entry->getRidMapping()->getT3id()));

                                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Successfully analysed tagged content with id "%s" and mapping-uid "%s".', $entry->getUid(), $entry->getRidMapping()->getUid()));

                                } else {

                                    throw new \RKW\RkwSearch\Exception('Invalid data collection returned from analyser.', 422543059);
                                    //===
                                }

                            } else {

                                throw new \RKW\RkwSearch\Exception('Invalid data collection given.', 1422543154);
                                //===
                            }

                        } else {

                            throw new \RKW\RkwSearch\Exception('Could not load repository for document.', 1442494270);
                            //===
                        }

                    } catch (\Exception $e) {

                        // update status if object is not valid!
                        $entry->setStatus(99);
                        $entry->setMessage('error');
                        $this->queueTaggedContentRepository->update($entry);

                        // update statuses
                        if ($entry->getRidMapping()) {
                            $this->ridMappingRepository->updateStatusAll($entry->getRidMapping(), 99, NULL, NULL);
                            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Unable to analyse tagged content with id "%s" and mapping-uid "%s". Item was therefore suspended. Message: %s', $entry->getUid(), $entry->getRidMapping()->getUid(), str_replace(array ("\n", "\r"), '', $e->getMessage())));

                         // if the ridMapping-entry is not available any more, kick it!
                        } else {

                            $this->queueTaggedContentRepository->remove($entry);
                            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::WARNING, sprintf('Unable to analyse tagged content with id "%s". Can not find mapping-entry. Item was therefore deleted. Message: %s', $entry->getUid(), str_replace(array ("\n", "\r"), '', $e->getMessage())));
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to analyse tagged content. Message: %s', str_replace(array ("\n", "\r"), '', $e->getMessage())));
        }

    }


    /**
     * Indexes the analysed content and sets relations in OrientDb
     *
     * @param integer $itemsToProcess
     * @param integer $approxQueryLimit
     * @param integer $keywordsPerItemLimit
     */
    public function indexContentsCommand ($itemsToProcess, $approxQueryLimit, $keywordsPerItemLimit) {

        try {

            $queryCount = 0;
            $result = $this->queueAnalysedKeywordsRepository->findByCrdateAndStatus($itemsToProcess);

            // load configuration
            foreach ($result as $entry) {

                try {

                    $keywordCountPerEntry = 0;

                    // break if query limit is reached
                    if ($queryCount > $approxQueryLimit)
                        break 1;
                        //===

                    // block current entry
                    $entry->setStatus(2);
                    $entry->setMessage('blocked');
                    $this->queueAnalysedKeywordsRepository->update($entry);
                    $this->persistenceManager->persistAll();

                    // unserialize data and check if it is valid
                    if (
                        ($serializedContent = $entry->getSerialized())
                        && ($serializedContent instanceof \RKW\RkwSearch\Collection\AnalysedKeywords)
                        && ($entry->getRidMapping())
                    ) {

                        try {

                            // set data and mapping model
                            $this->keywordIndexer->setData($serializedContent);
                            $this->keywordIndexer->setMappingModel($entry->getRidMapping());

                            // at the beginning of a new indexing:
                            // clean up old relations of content to keywords
                            if (
                                ($keywordCountPerEntry == 0)
                                && ($entry->getTstamp() == $entry->getCrdate())
                            )
                                $this->keywordIndexer->unrelateAll();


                            $this->keywordIndexer->index($queryCount, $keywordCountPerEntry, $approxQueryLimit, $keywordsPerItemLimit);

                        } catch (\Exception $e) {

                            $entry->setStatus(99);
                            $entry->setMessage('error');
                            $this->queueAnalysedKeywordsRepository->update($entry);

                            // update status of page and mapping-table
                            $this->ridMappingRepository->updateStatusAll($entry->getRidMapping(), 99, NULL, NULL);

                            $this->persistenceManager->persistAll();
                            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to index content. Item with id "%s" and mapping-uid "%s" was therefore suspended. Message: %s', $entry->getUid(), $entry->getRidMapping()->getUid(), str_replace(array ("\n", "\r"), '', $e->getMessage())));
                            continue;
                            //===
                        }

                        # ===================================================
                        # Delete processed keywords from array
                        # ===================================================
                        $newDataArray = $serializedContent->getData();
                        array_splice($newDataArray, 0, $keywordCountPerEntry);
                        $newContentCollection = GeneralUtility::makeInstance('RKW\\RkwSearch\\Collection\\AnalysedKeywords', $newDataArray);

                        // update status of current entry
                        $entry->setSerialized($newContentCollection);
                        $entry->setStatus(1);
                        $entry->setMessage('processing');

                        // if the array is empty, we delete the entry
                        if (count($newDataArray) == 0) {

                            // set the timestamp for the entry to prevent new index of the same object status
                            $entry->getRidMapping()->setIndexTstamp(time());

                            // update status of page and mapping-table
                            $this->ridMappingRepository->updateStatusAll($entry->getRidMapping(), 4, NULL, NULL);

                            // remove entry
                            $this->queueAnalysedKeywordsRepository->remove($entry);

                            // flush all OrientDB caches - this is relevant for the cached OrientDB queries
                            $this->getCache()->getCacheManager()->flushCaches();

                            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Successfully indexed content with id "%s" and mapping-uid "%s".', $entry->getUid(), $entry->getRidMapping()->getUid()));


                        } else {

                            // update status of page and mapping-table
                            $this->ridMappingRepository->updateStatusAll($entry->getRidMapping(), 3, NULL, NULL);

                            // update entry
                            $this->queueAnalysedKeywordsRepository->update($entry);
                        }

                        // persist it!
                        $this->persistenceManager->persistAll();



                    } else {
                        throw new \RKW\RkwSearch\Exception('Analysed content is invalid.', 1422547512);
                        //===
                    }

                }  catch (\Exception $e) {

                    $entry->setStatus(99);
                    $entry->setMessage('error');
                    $this->queueAnalysedKeywordsRepository->update($entry);

                    // update page status
                    if ($entry->getRidMapping()) {

                        $this->ridMappingRepository->updateStatusAll($entry->getRidMapping(), 99, NULL, NULL);
                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Unable to index analysed content with id "%s" and mapping-uid "%s". Item was therefore suspended. Message: %s', $entry->getUid(), $entry->getRidMapping()->getUid(), str_replace(array ("\n", "\r"), '', $e->getMessage())));

                    // if the ridMapping-entry is not available any more, kick it!
                    } else {

                        $this->queueAnalysedKeywordsRepository->remove($entry);
                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::WARNING, sprintf('Unable to index analysed content with id "%s". Can not find mapping-entry. Item was therefore deleted. Message: %s', $entry->getUid(), str_replace(array ("\n", "\r"), '', $e->getMessage())));
                    }

                    $this->persistenceManager->persistAll();

                }
            }

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to index content. Message: %s', str_replace(array ("\n", "\r"), '', $e->getMessage())));
        }

    }



    /**
     * Deletes keyword vertexes that have no edges any more
     *
     * @param integer $itemsToProcess
     */
    public function cleanupCommand($itemsToProcess = 500) {

        try {

            // only clean up if no process is running
            if (count($this->queueAnalysedKeywordsRepository->findAllByStatus()) == 0) {

                $this->keywordIndexer->cleanup($itemsToProcess);
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, 'Successfully executed clean up.');
            }

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to clean up the database. Message: %s', str_replace(array ("\n", "\r"), '', $e->getMessage())));
        }
    }


    /**
     * Resets all imports
     *
     * IMPORTANT: This should not be done regularly!!!
     */
    public function resetCommand() {

        try {

            $this->ridMappingRepository->resetImportAll();
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, 'Successfully executed import reset.');

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to reset the import. Message: %s', str_replace(array ("\n", "\r"), '', $e->getMessage())));
        }
    }


    /**
     * Analyses the list of popular search strings which has been saved as text file
     *
     */
    public function processPopularSearchstringsCommand() {

        try {

            // rename file
            $newName = PATH_site . 'typo3temp/tx_rkwsearch_popular_searchstrings.temp.lock';
            $oldName = PATH_site . 'typo3temp/tx_rkwsearch_popular_searchstrings.tmp';
            $cnt = 0;

            $fileLines = array ();
            if (file_exists($oldName)) {
                rename($oldName, $newName);

                // open file and go through it line by line
                if (file_exists($newName)) {

                    $file = fopen($newName, "r+");
                    if (flock($file, LOCK_EX)) {

                        while (!feof($file)) {

                            if ( $line = fgets($file, 1024)) {

                                // split IP from string - if set
                                $temp = explode("\t", $line);
                                if (count($temp) == 2) {
                                    $fileLines[] = array (
                                        'ip' => $temp[0],
                                        'keyword' => Text::sanitizeStringOrientDb($temp[1])
                                    );
                                }

                            }
                        }
                        flock($file, LOCK_UN);
                    }

                    fclose($file);
                    unlink($newName);
                }
            }

            // now go through all lines
            // each IP is counted once per keyword!
            $keywordIpMap = array ();
            $finalList = array ();
            foreach ($fileLines as $set) {

                // define basic array
                if (! is_array($keywordIpMap[$set['keyword']]))
                    $keywordIpMap[$set['keyword']] = array ();

                // check if ip has already been counted for this keyword
                if (! in_array(filter_var($set['ip'], FILTER_VALIDATE_IP), $keywordIpMap[$set['keyword']])) {

                    // set counter accordingly
                    $finalList[$set['keyword']] += 1;
                    $keywordIpMap[$set['keyword']][] = filter_var($set['ip'], FILTER_VALIDATE_IP);
                }
            }


            // now fo through final list and set counter values in database!
            if ($finalList) {

                /** @var \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepository $keywordVariationsRepository */
                $keywordVariationsRepository = GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\KeywordVariationsRepository');

                foreach ($finalList as $keyword => $counter) {

                    /**  @var \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariations $keywordVariation */
                    $keywordVariation = $keywordVariationsRepository->findOneByName($keyword);
                    if ($keywordVariation) {
                        $keywordVariation->setSearchCounter((intval($keywordVariation->getSearchCounter()) + intval($counter)));
                        $keywordVariationsRepository->update($keywordVariation);

                        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::DEBUG, sprintf('Successfully updated counter for popular keyword "%s".', $line));
                        $cnt++;
                    }
                }
            }

            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Updated %s counters of popular keywords', $cnt));

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying update the counters of popular keywords. Message: %s', str_replace(array ("\n", "\r"), '', $e->getMessage())));
        }
    }



    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger() {

        if (! $this->logger instanceof \TYPO3\CMS\Core\Log\Logger)
            $this->logger = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);

        return $this->logger;
        //===
    }


    /**
     * Returns the cache object
     *
     * @return \RKW\RkwSearch\OrientDb\Cache\RepositoryCache
     */
    protected function getCache() {

        if (! $this->cache instanceof \RKW\RkwSearch\OrientDb\Cache\RepositoryCache)
            $this->cache = GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Cache\\RepositoryCache');

        return $this->cache;
        //===
    }



}
?>