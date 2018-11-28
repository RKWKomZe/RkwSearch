<?php
namespace RKW\RkwSearch\Domain\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

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
 * Class RidMappingRepository
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class RidMappingRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     * @var \RKW\RkwSearch\Domain\Repository\PagesRepository
     */
    protected $pagesRepository;


    /**
     * @var \RKW\RkwSearch\Domain\Repository\PagesLanguageOverlayRepository
     */
    protected $pagesLanguageOverlayRepository;



    /**
     * Returns all elements that have not been tagged yet
     *
     * @param integer $limit
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findNotQueueTaggedContents($limit = 0) {

        $query = $this->createQuery();
        $limitAddition = '';
        if ($limit != 0)
            $limitAddition = ' LIMIT 0, ' . intval($limit);

        // select only entries that
        // a) have an tag_tstamp < tstamp AND
        // b) is not beeing tagged right now AND
        // c) is not beeing indexed right now

        // AND (ridMap.no_search = 0 OR ridMap.no_search IS NULL)

        $query->statement('
            SELECT ridMap.* FROM tx_rkwsearch_domain_model_ridmapping AS ridMap
            LEFT JOIN tx_rkwsearch_domain_model_queuetaggedcontent AS taggedContent
                ON taggedContent.rid_mapping = ridMap.uid
            LEFT JOIN tx_rkwsearch_domain_model_queueanalysedkeywords AS analysedKeywords
                ON analysedKeywords.rid_mapping = ridMap.uid
            WHERE
                ridMap.tag_tstamp < ridMap.import_tstamp
                AND (ridMap.debug = 0 OR ridMap.debug IS NULL)
                AND taggedContent.rid_mapping IS NULL
                AND analysedKeywords.rid_mapping IS NULL
            ORDER BY ridMap.tstamp ' . QueryInterface::ORDER_ASCENDING . $limitAddition
        );

        return $query->execute();
        //===
    }


    /**
     * Returns all debug elements
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findAllByDebug() {

        $query = $this->createQuery();
        $query->matching($query->equals('debug', 1));
        return $query->execute();
        //===
    }




    /**
     * Resets all imports
     * !!! IMPORTANT: This should NOT be executed on regular basis !!!
     *
     * @api
     */
    public function resetImportAll() {

        // delete from taggedContent table
        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rkwsearch_domain_model_queuetaggedcontent', '1=1');

        // delete from analysedKeywords table
        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rkwsearch_domain_model_queueanalysedkeywords', '1=1');

        // reset all timestamps and status for re-indexing
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_rkwsearch_domain_model_ridmapping', '1=1',
            array (
                'import_tstamp' => time(),
                'tag_tstamp' => 0,
                'analyse_tstamp' => 0,
                'index_tstamp' => 0,
                'status' => 0,
                'message'=> 'reset'
            )
        );

        // reset indexing info on pages table
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages', '1=1',
            array (
                'tx_rkwsearch_index_timestamp' => 0,
                'tx_rkwsearch_index_status' => 0,
                'tx_rkwsearch_index_result' => '',
            )
        );
    }


    /**
     * Resets import status
     *
     * @param \RKW\RkwSearch\Domain\Model\RidMapping $object
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function resetImport(\RKW\RkwSearch\Domain\Model\RidMapping $object) {

        if (!$object instanceof $this->objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to resetImport() was not of the type (' . $this->objectType . ') this repository manages.', 1424768041);
            //===

        if ($uid = $object->getUid()) {

            // delete from taggedContent table
            $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rkwsearch_domain_model_queuetaggedcontent', 'rid_mapping = ' . intval($uid));

            // delete from analysedKeywords table
            $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rkwsearch_domain_model_queueanalysedkeywords', 'rid_mapping = ' . intval($uid));

            // update status
            $this->updateStatusAll($object, 0, NULL, '');

            return TRUE;
            //===
        }

        return FALSE;
        //===
    }



    /**
     * Updates the status of the given object
     *
     * @param \RKW\RkwSearch\Domain\Model\RidMapping $object
     * @param integer $status
     * @param string $message
     * @throws \RKW\RkwSearch\Exception
     */
    public function updateStatus($object, $status, $message = NULL) {


        if (!$object instanceof $this->objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to updateStatus() was not of the type (' . $this->objectType . ') this repository manages.', 1424776587);
            //===

        if (! in_array($status, array (0,1,2,3,4,99) ))
            throw new \RKW\RkwSearch\Exception ('Invalid status given.', 1424776598);
            //===


        // update mapping entry
        $object->setStatus($status);
        $object->setMessage($message);
        if (! $message) {

            $object->setMessage('waiting');
            if ($status == 1)
                $object->setMessage('tagged');

            if ($status == 2)
                $object->setMessage('analysed');

            if ($status == 3)
                $object->setMessage('indexing');

            if ($status == 4)
                $object->setMessage('ready');

            if ($status == 99)
                $object->setMessage('error');
        }

        $this->update($object);

    }


    /**
     * Updates the status info in the page properties if we process a page
     *
     * @param \RKW\RkwSearch\Domain\Model\RidMapping $object
     * @param integer $status
     * @param string $resultDataText
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     */
    public function updateStatusPage($object, $status, $resultDataText = NULL) {

        if (!$object instanceof $this->objectType)
            throw new \RKW\RkwSearch\Exception ('The object given to updateStatusPage() was not of the type (' . $this->objectType . ') this repository manages.', 1424775958);
            //===

        if (! in_array($status, array (0,1,2,3,4,99) ))
            throw new \RKW\RkwSearch\Exception ('Invalid status given.', 1424776405);
            //===

        // get page model and repository if we are processing a page
        if (in_array($object->getT3table(), array ('pages', 'pages_language_overlay'))) {
            $pageRepository = $this->getPagesRepository();
            if ($object->getT3table() == 'pages_language_overlay')
                $pageRepository = $this->getPagesLanguageOverlayRepository();

            // set status (1 = Tagging) if page was found
            if ($pageModel = $pageRepository->findByUid(intval($object->getT3id()))) {
                $pageModel->setTxRkwsearchIndexStatus($status);
                $pageModel->setTxRkwsearchIndexTimestamp(time());
                if (! is_null($resultDataText))
                    $pageModel->setTxRkwsearchIndexResult($resultDataText);
                if ($status < 2)
                    $pageModel->setTxRkwsearchIndexResult('');
                $pageRepository->update($pageModel);

                return TRUE;
                //===
            }
        }

        return FALSE;
        //===
    }



    /**
     * Updates both statuses
     *
     * @param \RKW\RkwSearch\Domain\Model\RidMapping $object
     * @param integer $status
     * @param string $message
     * @param string $resultDataText
     *
     */
    public function updateStatusAll($object, $status, $message = NULL, $resultDataText = NULL) {

        $this->updateStatusPage($object, $status, $resultDataText);
        $this->updateStatus($object, $status, $message);
    }



    /**
     * Returns pages repository
     *
     * @return \RKW\RkwSearch\Domain\Repository\PagesRepository
     */
    public function getPagesRepository() {

        if (! $this->pagesRepository instanceof \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager) {
            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $this->pagesRepository = $objectManager->get('RKW\RkwSearch\Domain\Repository\PagesRepository');
        }

        return $this->pagesRepository;
        //===
    }


    /**
     * Returns pages repository
     *
     * @return \RKW\RkwSearch\Domain\Repository\PagesLanguageOverlayRepository
     */
    public function getPagesLanguageOverlayRepository() {

        if (! $this->pagesLanguageOverlayRepository instanceof \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager) {
            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $this->pagesLanguageOverlayRepository = $objectManager->get('RKW\RkwSearch\Domain\Repository\PagesLanguageOverlayRepository');
        }

        return $this->pagesLanguageOverlayRepository;
        //===
    }


}