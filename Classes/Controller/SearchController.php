<?php
namespace RKW\RkwSearch\Controller;
use \RKW\RkwBasics\Helper\Common;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
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
 * Class SearchController
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB\Binding
 */
class SearchController extends AbstractController {


    /**
     * documentTypeRepository
     *
     * @var \RKW\RkwBasics\Domain\Repository\DocumentTypeRepository
     * @inject
     */
    protected $documentTypeRepository = NULL;


    /**
     * departmentRepository
     *
     * @var \RKW\RkwBasics\Domain\Repository\DepartmentRepository
     * @inject
     */
    protected $departmentRepository = NULL;


    /**
     * projectsRepository
     *
     * @var \RKW\RkwProjects\Domain\Repository\ProjectsRepository
     * @inject
     */
    protected $projectRepository = NULL;


    /**
     * sectorsRepository
     *
     * @var \RKW\RkwBasics\Domain\Repository\SectorRepository
     * @inject
     */
    protected $sectorRepository = NULL;


    /**
     * enterpriseSizeRepository
     *
     * @var \RKW\RkwBasics\Domain\Repository\EnterpriseSizeRepository
     * @inject
     */
    protected $enterpriseSizeRepository = NULL;

    /** @toDo: Finally delete when tested
     * consultantBasicServiceRepository
     *
     *  \RKW\RkwConsultant\Domain\Repository\BasicServiceRepository
     *

    protected $consultantBasicServiceRepository = NULL;
     */

    /**
     * categoryRepository
     *
     * @var \RKW\RkwSearch\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository = NULL;


    /**
     * pagesRepository
     *
     * @var \RKW\RkwSearch\Domain\Repository\PagesRepository
     * @inject
     */
    protected $pagesRepository = NULL;


    /**
     * @var \RKW\RkwSearch\Keywords\Fetcher
     * @inject
     */
    protected $keywordFetcher;


    /**
     * Original action
     *
     * @var string
     */
    protected $originalAction = NULL;



    /**
     * Shows the most searched keywords
     *
     * @return void
     */
    public function mostSearchedKeywordsAction() {

        /** @var \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepository $keywordVariationsRepository */
        $keywordVariationsRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\KeywordVariationsRepository');
        $result = $keywordVariationsRepository->findMostSearched();

        $this->view->assign('keywords', $result);
    }


    /**
     * Shows search field on home site
     *
     * @return string|void
     */
    public function homeAction() {

        $this->view->assignMultiple($this->getSearchFormReplacements('search', 'home'));

    }


    /**
     * Executed for search based on URL, if url could not be found
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function pageNotFoundAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {

            if (! \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('type') == 1433770902) {

                // we take the last part of the url as search string - but only if it is longer than 3 signs!
                $url = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('originalUrl');
                if ($url) {
                    $urlArray = array_reverse(explode('/', $url));

                    $searchString = '';
                    foreach ($urlArray as $part) {

                        // remove trailing numbers - may be a page-id or some item-id
                        $part = preg_replace('/(.+)(-[0-9]+)$/', '$1', $part);

                        if (strlen($part) >= 3) {
                            $searchString = str_replace('-', ' ', urldecode($part));
                            break;
                            //===
                        }
                    }

                    if ($searchString)
                        $this->searchAction(array('searchString' => $searchString), $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
                }
            } else {
                $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
            }

        }
    }

    /**
     * Shows related contents
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function relatedAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }


    /**
     * Shows related contents for events
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function relatedNoCacheAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }


    /**
     * Shows elements which belong to the same series
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function seriesAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }

    /**
     * Shows list of publications
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function publicationsAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);

    }

    /**
     * Shows list of publicationsSpecial
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function publicationsSpecialAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);

    }

    /**
     * Shows elements which belong to the same series
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function publicationsSeriesAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }


    /** @toDo: Finally delete when tested

     * Shows list of consultants
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void

    public function consultantsAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);

    }

    /**
     * Shows list of consultants
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void

    public function consultantsInternalAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);

    }
    */

    /**
     * Shows list of blog-articles
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function blogAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);

    }

    /**
     * Shows list of events
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function eventsAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);

    }


    /**
     * Shows newest contents
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function newsAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }


    /**
     * Shows example
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function exampleAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }

    /**
     * Shows newest contents with another box template
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function recentAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }

    /**
     * Shows newest contents without publications
     *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
     */
    public function recentArticleAction($searchFilter= array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $this->originalAction = str_replace('Action', '', __FUNCTION__);
        $this->searchAction($searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
    }


    /**
	 * action execute
	 *
     * @param array $searchFilter
     * @param integer $page
     * @param integer $maxResults
     * @param boolean $noAutoloadMore
     * @param boolean $boostPublications
     * @param string $containerId
     * @return string|void
	 */
	public function searchAction($searchFilter = array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        $timeStart = microtime(true);

        // @toDo: FIXEN !!!!
        // Workaround for missing containerId from init form!
        if (! $containerId)
            $containerId = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('containerId');

        //============
        // cleanup for params
        $page = intval($page);
        $maxResults = intval($maxResults);
        $noAutoloadMore = (boolean) $noAutoloadMore;
        $boostPublications = (boolean) $boostPublications;
        $containerId = preg_replace('/[^a-z0-9]+/i', '', $containerId);

        //============
        // clean-up for searchFilter-array
        foreach($searchFilter as $key => $value)
            $searchFilter[$key] = strip_tags($value);

        //============
        // Some basics
        $errorMessage = NULL;
        $result = NULL;
        $action = $this->originalAction ? $this->originalAction : str_replace('Action', '', __FUNCTION__);
        $relatedTolerance = floatval($this->settings['search']['related']['scoreTolerance']) ? floatval($this->settings['search']['related']['scoreTolerance']) : 0.2;
        $relatedItemsPerHundredSigns = floatval($this->settings['search']['related']['itemsPerHundredSigns']) ? floatval($this->settings['search']['related']['itemsPerHundredSigns']) : 0.6;
        $relatedMinItems = intval($this->settings['search']['related']['minItems']) ? intval($this->settings['search']['related']['minItems']) : 2;
        if (! $maxResults)
            $maxResults = (intval($this->settings['search']['limit']) ? intval($this->settings['search']['limit']) : (intval($this->settings['queryFactory'][0]['resultsPerPage']) ? intval($this->settings['queryFactory'][0]['resultsPerPage']) : 10));
        if (! $boostPublications)
            $boostPublications = (boolean) $this->settings['search']['publications']['boost'];

        //============
        // check for AJAX
        $ajax = FALSE;
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('type') == 1433770902)
            $ajax = TRUE;

        // check if user selected keyword from list
        $autoComplete = FALSE;
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('searchautocomplete'))
            $autoComplete = TRUE;

        $autoCompleteSearchString = null;
        $autoSuggest = array();
        $queryFactory = null;
        //============
        // get results
        try {

            /** @var \RKW\RkwSearch\Search\QueryFactory $queryFactory */
            // get QueryFactory with all relevant filters!
            $queryFactory = $this->getQueryFactory($action, $searchFilter);

            if ($action == 'news')
                $maxResults = 8;

            // no autosuggest for related or pageNotFound!
            if (
                ($action != 'related')
                && ($action != 'relatedNoCache')
                && ($action != 'pageNotFound')
            ) {

                // get auto suggest if a keyword filter has been set
                // but not if flags are set and only if keyword filter is set at all!
                if (
                    (strpos($searchFilter['searchString'], '--') === FALSE)
                    && ($keywordFilter = $queryFactory->getFilter('keywords'))
                    && ($searchString = $keywordFilter['value'])
                    && (! $this->settings['search']['command'])
                ) {

                    $autoSuggest = $this->getAutoSuggest($searchFilter['searchString'], $autoComplete, $autoCompleteSearchString);
                }

                // set search string to autoSuggest if available
                if ($autoCompleteSearchString)
                    $queryFactory->setSearchString($autoCompleteSearchString);
            }

            if ($searchFilter['ordering']) {
                $direction = 'DESC';
                if ($searchFilter['orderAscending'])
                    $direction = 'ASC';
                $queryFactory->setOrdering(array(preg_replace('/[^a-zA-Z0-9_-]+/', '', $searchFilter['ordering']) => $direction));
            }

            // set limit
            $queryFactory->setLimit($maxResults);

            // set page
            if ($page)
                $queryFactory->setCurrentPage(intval($page));

            // set boost for publications if wanted
            if ($boostPublications) {

                // no boosting for pdfs and length here
                $queryFactory->setPublicationBoostSearchLucene(TRUE);
                $queryFactory->setLengthBoostSearchLucene(TRUE);
            }

            // track search strings
            if (
                (intval($page) < 2)
                && (! $autoComplete)
                && ($action != 'related')
                && ($action != 'relatedNoCache')
                && ($action != 'pageNotFound')
            ) {

                try {

                    // we save them in a file and process it later since update in the database takes too much time here
                    if (
                        (strpos($searchFilter['searchString'], '--') === FALSE)
                        && (strlen($searchFilter['searchString']) > 3)
                        && (! $this->settings['search']['command'])
                    ) {

                        // proof remote addr
                        $remoteAddr = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
                        if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
                            $ips = GeneralUtility::trimExplode (',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                            if ($ips[0])
                                $remoteAddr = filter_var($ips[0], FILTER_VALIDATE_IP);
                        }

                        file_put_contents(PATH_site.'typo3temp/tx_rkwsearch_popular_searchstrings.tmp', $remoteAddr . "\t" . $searchFilter['searchString'] . "\n", FILE_APPEND|LOCK_EX);

                    }

                } catch (\Exception $e) {
                    // do nothing
                }

            }

            // get results
            $tempResult = array ();

            // separate handling for related
            if (
                ($action == 'related')
                || ($action == 'relatedNoCache')
            ){

                // set filter by given author
                if (
                    (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rkw_events'))
                    && ($getParams = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_rkwevents_pi1'))
                    && ($getParams['event'])
                ) {

                    /** @var \RKW\RkwEvents\Domain\Repository\EventRepository $eventRepository */
                    $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
                    $eventRepository = $objectManager->get('RKW\\RkwEvents\\Domain\\Repository\\EventRepository');

                    /** @var \RKW\RkwEvents\Domain\Model\Event $event */
                    if (
                        ($event = $eventRepository->findByIdentifier(intval($getParams['event'])))
                        && ($event->getDepartment())
                    )
                        $tempResult = $queryFactory->getRepository()->findRelatedByQueryFactory($event->getDepartment(), $relatedTolerance, $relatedItemsPerHundredSigns, $relatedMinItems);

                } else {
                    $tempResult = $queryFactory->getRepository()->findRelatedByQueryFactory(intval($GLOBALS['TSFE']->id), $relatedTolerance, $relatedItemsPerHundredSigns, $relatedMinItems);
                }



            // separate handling for related series
            } elseif (
                ($action == 'publicationsSeries')
                || ($action == 'series')
            ){

                // show results only if there has been a filtering!
                if ($queryFactory->getFilter('series'))
                    $tempResult = $queryFactory->getRepository()->findByQueryFactory(intval($GLOBALS['TSFE']->id));

            // everything else
            }else {
                $tempResult = $queryFactory->getRepository()->findByQueryFactory(intval($GLOBALS['TSFE']->id));
            }

            // get final limit from queryFactory
            $maxResults = $queryFactory->getLimit() -1;

            if (count($tempResult) > 0)
                $result = $tempResult;

        } catch (\RKW\RkwSearch\Exception $e) {
            // Nothing to do here. Nothing!
        }

        $timeEnd = microtime(true);
        $queryTime = $timeEnd - $timeStart;

        //============
        // prepare replacements
        $formReplacements = $this->getSearchFormReplacements($action, $action, $searchFilter, $queryFactory);
        $resultReplacements = $this->getSearchResultReplacements ($action, $result, $searchFilter, $page, $maxResults, $noAutoloadMore, $boostPublications, $containerId);
        $resultReplacements['queryTime'] = $queryTime;

        //============
        // fallback if no results
        if (! $result) {

            $resultReplacements = array (
                'errorMessage' => $errorMessage,
                'searchResultEmptyHtml' => '',
                'maxResults' => $maxResults,
                'queryTime' => $queryTime,
                'action' => $this->originalAction ? $this->originalAction : $this->getControllerContext()->getRequest()->getControllerActionName()
            );


            if (
                ($this->settings['search']['emptyResultPid'])
                && (intval($GLOBALS['TSFE']->id) != $this->settings['search']['emptyResultPid'])
                && (! $this->settings['search']['hideFilters'])
            ){

                try {
                    // build url from pid!
                    $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
                    $uriBuilder = $objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Routing\\UriBuilder');
                    if ($uriBuilder instanceof \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder) {
                        $url = $uriBuilder->reset()
                            ->setTargetPageUid(intval($this->settings['search']['emptyResultPid']))
                            ->setArguments(array('type' => '1444893030'))
                            ->setCreateAbsoluteUri(TRUE)
                            ->build();

                        $resultReplacements['searchResultEmptyHtml'] = file_get_contents($url);
                    }
                } catch (\Exception $e) {
                    $resultReplacements['searchResultEmptyHtml'] = $e->getMessage();
                }
            }
        }


        //============
        // Output via AJAX
        if ($ajax) {

            // get JSON helper
            /** @var \RKW\RkwBasics\Helper\Json $jsonHelper */
            $jsonHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\Helper\\Json');

            // set results
            $jsonHelper->setHtml(
                (($page > 1) ? 'boxes-grid-' . $containerId : 'search-result-section-' . $containerId),
                $resultReplacements,
                (($page > 1) ? 'append' : 'replace'),
                'Ajax/SearchResultList.html'
            );

            // set pushState for more-link!
            /* NOT WORKING FOR SEARCH-FILTERS, SO COMMENTED OUT
            if (
                ($page > 1)
                || ($activelySelected)
            )
                $jsonHelper->setJavaScript("jQuery(document).trigger('ajax-api-push-state', [jQuery(document).find('.search-result-section'), 'http://relaunch.rkw-kompetenzzentrum.local/more-link-new', 'Title']);");
            */

            if (
                ($autoSuggest)
                && (count($autoSuggest) > 0)
            ){
                $jsonHelper->setData(
                    array(
                        'keyword' => $autoCompleteSearchString,
                        'keyword-alternatives' => $autoSuggest
                    )
                );

            } else {

                // delete auto complete
                $jsonHelper->setData(
                    array(
                        'keyword' => NULL
                    )
                );
            }

            $eTrackerEvent = '';
            if (
                ($searchFilter['searchString'])
                && (! $this->settings['search']['command'])
            )
                $eTrackerEvent = '
                    try {
                        ET_Event.eventStart(\'Search\', \'' . addslashes((($autoSuggest && (count($autoSuggest) > 0) && $autoCompleteSearchString) ? $autoCompleteSearchString : $searchFilter['searchString'])). '\', \'' . ( ($autoComplete) ? 'auto-suggest' : 'strict') . '\', \'\');
                    } catch (e) {}
                ';

            // show filters when coming from home.html
            $jsonHelper->setJavaScript('
                $(".search-filter.hidden-at-start").trigger("special-keyword-removed");
                ' . $eTrackerEvent .'
            ');

            /** @toDo: This is simply bullshit :) */
            /*// Check if we need to redirect
            if (
                ($searchFilter['searchString'])
                && (! $this->settings['search']['command'])
                && (is_array($this->settings['search']['searchTermJumpTo']))
                && (! $autoComplete)
            ) {

                foreach ($this->settings['search']['searchTermJumpTo'] as $pid => $searchTerms) {

                    if ($pid < 1)
                        continue;
                        //===

                    if (strpos(strtolower($searchTerms), strtolower($searchFilter['searchString'])) !== FALSE)  {

                        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
                        /** @var \RKW\RkwMailer\Helper\FrontendUriBuilder $uriBuilder /
                        $uriBuilder = $objectManager->get('RKW\\RkwMailer\\Helper\\FrontendUriBuilder');
                        $uriBuilder->reset();

                        // build link based on given pid
                        $uriBuilder->setTargetPageUid($pid)
                            ->setCreateAbsoluteUri(TRUE);

                        $url = $uriBuilder->buildFrontendUri();

                        // delete results and redirect via JavaScript
                        $jsonHelper->unsetData();
                        $jsonHelper->unsetHtml();
                        $jsonHelper->setJavaScript('
                            window.location.replace("' . $url. '");
                        ');

                        break;
                        //===
                    }
                }
            }
            */

            if ($errorMessage)
                $jsonHelper->setStatus('STATUS_ERROR')
                    ->setMessage('search-errors', $errorMessage, '99');

            print (string) $jsonHelper;
            exit();
            //===
        }

        //============
        // normal rendering here!
        $this->view->assignMultiple($formReplacements);
        $this->view->assignMultiple($resultReplacements);

        if ($errorMessage)
            $this->addFlashMessage($errorMessage, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);

	}


    /**
     * test action
     *
     * @param string $content
     * @return void
     */
    public function testAction($content = NULL) {

       # $repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\DocumentAuthorsRepository');
       # $element = $repository->findByRid('#13:5');
       # $content = $this->keywordFetcher->getContent($element);

       # var_dump($content);


        if ($content) {

            $tagger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\TreeTagger\\TreeTagger', array (0));
            $tagResultFiltered = $tagger->setText($content)->execute()->getFilteredResults('distance');

            $keywordAnalyser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\Keywords\\Analyser');
            $keywordAnalyser->setData($tagResultFiltered);

            $this->view->assignMultiple(
                array(
                    'strippedHtml' => $tagger->getText(),
                    'content' => $content, TRUE,
                    'tagResult' => print_r($tagger->getResults(), TRUE),
                    'tagResultFiltered' => print_r( $tagResultFiltered, TRUE),
                    'analyseResultRaw' => print_r($keywordAnalyser->countMatches()->getResults(), TRUE),
                    'analyseResultSorted' => print_r($keywordAnalyser->weightMatches()->getResultsSummary(500), TRUE)
                )
            );
        }


    }


    /**
     * Returns all filters and replacements for the search form
     *
     * @param string $action
     * @param string $template
     * @param array $searchFilter
     * @return array
     */
    protected function getSearchFormReplacements ($action = NULL, $template = NULL, $searchFilter = array(), $queryFactory = null) {

        // get action
        if (! $action)
            $action = $this->originalAction ? $this->originalAction : $this->getControllerContext()->getRequest()->getControllerActionName();

        // get filters
        $departments = $this->departmentRepository->findAllByVisibility();
        $projects = $this->projectRepository->findAllByVisibility();
        /**  @toDo: Finally delete when tested
        $consultantServices = $this->consultantBasicServiceRepository->findAll();
        */
        $categories = $this->categoryRepository->findByParent(intval($this->settings['search']['categoryParentUid']));

        $type = 'default';
        if (in_array($action, array('publications', 'publicationsSpecial')))
            $type = 'publications';
        if ($action == 'events')
            $type = 'events';

        $documentTypes = $this->documentTypeRepository->findAllByTypeAndVisibility($type);
        $years = range(2011, date("Y"));

        $sectors = $this->sectorRepository->findAllByTypeAndVisibility($type);
        $enterpriseSizes = $this->enterpriseSizeRepository->findAllByTypeAndVisibility($type);

        // preset for searchFilter when command is set
        if (
            ($queryFactory)
            && ($queryFactory instanceof \RKW\RkwSearch\Search\QueryFactory)
        ) {

            $filters = $queryFactory->getFilters();
            foreach ($filters as $filterName => $filterData) {

                if (empty($searchFilter[$filterName])) {
                    $searchFilter[$filterName] = $filterData['value'];
                }
            }
        }

        return array (
            'action' => $action ? $action : 'search',
            'template' => $template ? Common::splitAtUpperCase($template, 0) : ($action ? Common::splitAtUpperCase($action, 0) : 'search') ,
            'departments' => $departments,
            'documentTypes' => $documentTypes,
            /**  @toDo: Finally delete when tested
            'consultantServices' => $consultantServices,
             */
            'projects' => $projects,
            'years' => $years,
            'enterpriseSizes' => $enterpriseSizes,
            'sectors' => $sectors,
            'categories' => $categories,
            'searchFilter' => $searchFilter,
            'containerId' => $template, // important for home-search. will be overwritten if result-list is displayed, too
            'configForm' => array (
                 'specialKeywords' => (($this->settings['searchAutocomplete']['specialKeywords']) ? $this->settings['searchAutocomplete']['specialKeywords'] : ''),
                 'delayTimeForAjax' => ($this->settings['searchAutocomplete']['delayTimeForAjax'] ? intval($this->settings['searchAutocomplete']['delayTimeForAjax']) : 0),
                 'delayLettersForAjax' => ($this->settings['searchAutocomplete']['delayLettersForAjax'] ? intval($this->settings['searchAutocomplete']['delayLettersForAjax']) : 0),
                 'ariaControls' => ($this->settings['search']['ariaControls'] ? $this->settings['search']['ariaControls'] : ''),
                 'hideFilters' => (boolean) $this->settings['search']['hideFilters'],
                 'hideFreetextFilter' => ($this->settings['search']['command'] || $this->settings['search']['hideFreetextFilter'])? TRUE : FALSE,
            ),
            'config' => array (
                'boostPublications' => (boolean) $this->settings['search']['publications']['boost'], // important for home-search. will be overwritten if result-list is displayed, too
            )
        );
        //===
    }


    /**
     * Returns relevant replacements for templates
     *
     * @param string $action
     * @param array $result Results to be displayed
     * @param array $searchFilter Array fo set search filters
     * @param integer $page Current page
     * @param integer $maxResults Number of desired results
     * @param boolean $noAutoloadMore If set to TRUE the more-link does not load more pages when end of page is reached
     * @param boolean $boostPublications
     * @param string $containerId Id of DIV-container for results
     * @return array
     */
    protected function getSearchResultReplacements ($action = NULL, $result = array(), $searchFilter = array(), $page = 1, $maxResults = 0, $noAutoloadMore = FALSE, $boostPublications = FALSE, $containerId = NULL) {

        // get action
        if (! $action)
            $action = $this->originalAction ? $this->originalAction : $this->getControllerContext()->getRequest()->getControllerActionName();

        // get max Results
        if (! $maxResults)
            $maxResults = (intval($this->settings['search']['limit']) ? intval($this->settings['search']['limit']) : (intval($this->settings['queryFactory'][0]['resultsPerPage']) ? intval($this->settings['queryFactory'][0]['resultsPerPage']) : 10));

        // get noAutloadMore
        if (! $noAutoloadMore)
            $noAutoloadMore = (boolean) $this->settings['search']['noAutoloadMore'];

        // get boostPublications
        if (! $boostPublications)
            $boostPublications = (boolean) $this->settings['search']['publications']['boost'];

        // check for AJAX
        $ajax = FALSE;
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('type') == 1433770902)
            $ajax = TRUE;

        // check if user selected keyword from list
        $autoComplete = FALSE;
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('searchautocomplete'))
            $autoComplete = TRUE;

        // generate unique container-id
        if (! $containerId)
            $containerId = uniqid();

        return $resultReplacements = array (
            'action' => $action ? $action : 'search',
            'template' => Common::splitAtUpperCase($action, 0),
            'searchResult' => $result,
            'searchResultEmptyHtml' => '',
            'nextPage' => $page + 1,
            'page' => $page,
            'showGrid' => ($ajax && $page > 1) ? FALSE : TRUE,
            'isAjax' => $ajax,
            'containerId' => $containerId,
            'searchFilterLinkParams' => array (
                'tx_rkwsearch_rkwsearch' => array (
                    'searchFilter' => $searchFilter,
                    'page' => intval($page) + 1,
                    'maxResults' => intval($maxResults),
                    'noAutoloadMore' => $noAutoloadMore,
                    'boostPublications' => $boostPublications,
                    'containerId' => $containerId,
                ),
                'autocomplete' => $autoComplete
            ),
            'config' => array (
                /**  @toDo: Finally delete when tested
                'consultantsDetailPid' => intval($this->settings['search']['consultantsDetailPid']),
                'consultantsInternalDetailPid' => intval($this->settings['search']['consultantsInternalDetailPid']),
                 */
                'authorsDetailPid' => intval($this->settings['search']['authorsDetailPid']),
                'hideMore' => (boolean) $this->settings['search']['hideMore'],
                'linkTarget' => $this->settings['search']['linkTargetSelf'] ? '_self' : ($this->settings['search']['hideMore'] ? '_self' : '_blank'),
                'noAutoloadMore' => $noAutoloadMore,
                'boostPublications' => $boostPublications,
                'maxResults' => $maxResults,
            )
        ) ;
        //===
    }

    /**
     * Returns the auto-suggest data for search
     *
     * @param string $searchString The search string
     * @param boolean $autoComplete Checks if autoComplete is activated
     * @param string &$searchAutoSuggest Returns the auto-complete string
     * @return array
     */
    protected function getAutoSuggest ($searchString, $autoComplete, &$autoCompleteSearchString) {

        /** @var \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepository $keywordVariationsRepository */
        $keywordVariationsRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\KeywordVariationsRepository');

        // we need to replace ß by ss here for OrientDb, too
        // @see: RKW\RkwSearch\OrientDb\Storage\Query\Query::getRaw()
        $searchString = str_replace('ß', 'ss', $searchString);

        // first of all we try to find matches without fuzzy search
        $temp = $keywordVariationsRepository->findRelated($searchString, 10, TRUE);
        if ($temp instanceof \RKW\RkwSearch\OrientDb\Collection\Document) {

            // if we find nothing, we use fuzzy search here
            if (count($temp) < 1)
                $temp = $keywordVariationsRepository->findRelated($searchString, 10);

            // if there is still nothing we search for combined keywords
            if (count($temp) < 1)
                $temp = $keywordVariationsRepository->findRelated($searchString, 10, TRUE, TRUE);

            if ($temp instanceof \RKW\RkwSearch\OrientDb\Collection\Document) {
                $autoSuggest = $temp->getDataByKey('name', 1, TRUE);

                if (
                    ($autoSuggest)
                    && (is_array($autoSuggest))
                ) {

                    // check if keyword is in autosuggest list - if it is, take it to the top and use it!
                    $key = array_search(strtolower($searchString), $autoSuggest);
                    if ($key !== FALSE) {

                        $tempValue = $autoSuggest[$key];
                        unset($autoSuggest[$key]);
                        array_unshift($autoSuggest, $tempValue);
                        $autoCompleteSearchString = $tempValue;


                    // else just take the suggestion with the most entries
                    // but only if the user has not explicitly chosen this keyword
                    } else if (
                        ($autoComplete)
                        && ($autoSuggest[0])
                        && (strpos($autoSuggest[0], strtolower($searchString)) !== FALSE)
                    ) {
                        $autoCompleteSearchString = $autoSuggest[0];
                    }
                }

                return $autoSuggest;
                //===
            }
        }

        return array ();
        //===
    }
    

    /**
     * Returns the query-factory set set filetrs 
     *
     * @param string $action Current action
     * @param array &$searchFilter Search filter that has been set
     * @return \RKW\RkwSearch\Search\QueryFactory
     */
    protected function getQueryFactory ($action, &$searchFilter) {
        
        /** @var \RKW\RkwSearch\Search\QueryFactory $queryFactory */
        $queryFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\Search\\QueryFactory');

        // set filter by given author
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rkw_authors')) {

            $getParams = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_rkwauthors_rkwauthorsdetail');
            if ($getParams['author']) {

                /** @var \RKW\RkwAuthors\Domain\Repository\AuthorsRepository $authorsRepository */
                $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
                $authorsRepository = $objectManager->get('RKW\\RkwAuthors\\Domain\\Repository\\AuthorsRepository');

                /** @var \RKW\RkwAuthors\Domain\Model\Authors $author */
                $author = $authorsRepository->findByIdentifier(intval($getParams['author']));
                if ($author) {
                    $queryFactory->setFilter('author', $author->getFirstname() . ' ' . $author->getLastName());
                    $searchFilter['author'] = $author->getFirstname() . ' ' . $author->getLastName();
                }
            }
        }

        // set search string
        $queryFactory->setSearchString($searchFilter['searchString']);

        // get search string from flexbox - overrides all except for filters and kills fuzzy search!
        if ($this->settings['search']['command']) {
            $queryFactory->setSearchString($this->settings['search']['command']);
            $searchFilter['searchString'] = $this->settings['search']['command'];
            $queryFactory->setFuzzySearchLucene(FALSE);
        }


        // set filters by action
        if ($action == 'publications')
            $queryFactory->setFilter('publication');

        if ($action == 'publicationsSpecial')
            $queryFactory->setFilter('publicationSpecial');

        /**  @toDo: Finally delete when tested
        // set filter for consultants (internal RKW Network and external)
        if (
            ($action == 'consultants')
            || ($action == 'consultantsInternal')
        )
            $queryFactory->setFilter('consultant');

        if ($action == 'consultantsInternal') {
            $queryFactory->setFilter('consultantInternal', 1);

        } else if ($action == 'consultants') {
            $queryFactory->setFilter('consultantInternal', 0);
        }
        */


        if ($action == 'events')
            $queryFactory->setFilter('event');

        if (
            ($action == 'publicationsSeries')
            || ($action == 'series')
        ) {

            // get series of current page!
            /** @var \RKW\RkwSearch\Domain\Model\Pages $page */
            if ($page = $this->pagesRepository->findByUid(intval($GLOBALS['TSFE']->id))) {

                // get series
                /** @var \RKW\RkwBasics\Domain\Model\Series $txRkwbasicsSeries $series */
                if ($series = $page->getTxRkwbasicsSeries())
                    $queryFactory->setFilter('series', $series->getName());
            }
        }

        if (
            ($action == 'recent')
            || ($action == 'news')
        )
            $queryFactory->setFilter('news');

        if ($action == 'recentArticle')
            $queryFactory->setFilter('newsArticle');


        // set filters by form values
        if ($searchFilter['department'])
            $queryFactory->setFilter('department', $searchFilter['department']);

        if ($searchFilter['enterpriseSize'])
            $queryFactory->setFilter('enterpriseSize', $searchFilter['enterpriseSize']);

        if ($searchFilter['sector'])
            $queryFactory->setFilter('sector', $searchFilter['sector']);

        if ($searchFilter['category'])
            $queryFactory->setFilter('category', $searchFilter['category']);

        if ($searchFilter['documentType'])
            $queryFactory->setFilter('type', $searchFilter['documentType']);

        /**  @toDo: Finally delete when tested
        if (isset($searchFilter['consultantInternal']))
            $queryFactory->setFilter('consultantInternal', $searchFilter['consultantInternal']);

        if ($searchFilter['consultantService'])
            $queryFactory->setFilter('consultantService', $searchFilter['consultantService']);

        if ($searchFilter['consultantLocation'])
            $queryFactory->setFilter('consultantLocation', $searchFilter['consultantLocation']);
        */
        if ($searchFilter['project'])
            $queryFactory->setFilter('project', $searchFilter['project']);

        if ($searchFilter['author'])
            $queryFactory->setFilter('author', $searchFilter['author']);

        if ($searchFilter['year']) {
            $queryFactory->setFilter('dateFrom', intval($searchFilter['year']));
            $queryFactory->setFilter('dateTo', intval(($searchFilter['year'])));
        }
        
        return $queryFactory;
        //====
    }


}