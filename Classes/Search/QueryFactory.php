<?php
namespace RKW\RkwSearch\Search;
use RKW\RkwSearch\OrientDb\Helper\Common;
use Doctrine\OrientDB\Query\Validator\Escaper as EscapeValidator;
use \RKW\RkwSearch\OrientDb\Storage\Query\Query;
use RKW\RkwSearch\OrientDb\Helper\Query as QueryHelper;

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
 * Class QueryFactory
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class QueryFactory {

    /**
     *
     * @const string Introduces flag
     */
    const FLAG_INTRO = '--';

    /**
     *
     * @const string Ends flag and introduces flag value
     */
    const FLAG_EXTRO = ':';

    /**
     * @var string contains the orientDb class for the query
     */
    protected $orientDbClass;

    /**
     * @boolean Checks if the standard filters are set or not
     */
    protected $standardFilterStatus;

    /**
     * @var array Contains the already set filters together with their configuration
     */
    protected $filters = array();


    /**
     * @var integer Language Uid
     */
    protected $languageUid;


    /**
     * @var integer Current page for results
     */
    protected $currentPage;


    /**
     * @var string Contains the text to process
     */
    protected $searchString;


    /**
     * @var array sets the ordering
     */
    protected $ordering = array();


    /**
     * @var array sets the grouping
     */
    protected $grouping = array();

    /**
     * @var array sets the WHERE-clause
     */
    protected $where = array();

    /**
     * @var integer limit value
     */
    protected $limit;

    /**
     * @var array Contains the TypoScript settings
     */
    protected $settings;

    /**
     * @var boolean If set to TRUE the objects are marked with a special param
     */
    protected $debugMode = FALSE;


    /**
     * @var boolean If set to TRUE the fuzzy search is not executed
     */
    protected $fuzzySearchLucene = TRUE;

    /**
     * @var boolean If set to TRUE the search rates publications up
     */
    protected $publicationBoostSearchLucene = FALSE;

    /**
     * @var boolean If set to TRUE the search rates long texts up
     */
    protected $lengthBoostSearchLucene = FALSE;


    /**
     * @var boolean If set to TRUE the search rates perfect matches up
     */
    protected $perfectMatchBoostSearchLucene = TRUE;


    /**
     * Set all filters using flags in search-string
     * Example: '--autor: Karl spak --fachbereich: Gründung'
     *
     * @throws \RKW\RkwSearch\Exception
     * @return array
     */
    public function setFilters() {

        // explode search string on '--'
        $flagArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(self::FLAG_INTRO, $this->getSearchString(), TRUE);

        // get filter-mapping from configuration
        $filterMapping = array ();
        if (
            ($this->getConfiguration('filterMapping'))
            && (is_array($this->getConfiguration('filterMapping')))
        )
            $filterMapping = $this->getConfiguration('filterMapping');

        // check if we have a match with our available filters
        foreach ($flagArray as $flag) {

            // find flag name
            // flags may be followed by a : specifying params or not. So we need some fallback here
            $flagName = substr($flag, 0, strpos($flag, self::FLAG_EXTRO));
            if (! $flagName)
                $flagName = substr($flag, 0, strpos($flag, ' '));
            if (! $flagName)
                $flagName = $flag;

            // check if flagname is defined in TypoScript
            if (
                ($flagName)
                && ($filterMapping[strtolower($flagName)])
            ){

                // get flag value - may be the case that there is no value!
                $flagValue = NULL;
                if (strpos($flag, ':'))
                    $flagValue = trim(substr($flag, strpos($flag, ':') + 1));

                // set filter
                $this->setFilter($filterMapping[strtolower($flagName)], $flagValue);

            // set default filter if nothing matches
            } elseif ($filterMapping['_default']) {
                $this->setFilter($filterMapping['_default'], $flag);
            }
        }
        return $this->filters;
        //===


    }

    /**
     * Returns all set filters
     *
     * @return array
     */
    public function getFilters() {

        if (is_array($this->filters))
            return $this->filters;
            //===

        return array();
        //===
    }


    /**
     * sets single filter
     *
     * @param string $className
     * @param string $data
     * @throws \RKW\RkwSearch\Exception
     * @return boolean
     */
    public function setFilter($className, $data = NULL) {

        // check for configuration
        if (
            ($filters = $this->getConfiguration('filters'))
            && (is_array($filters))
            && (! empty($filters))
        ) {

            if ($configuration = $filters[lcfirst($className)]) {

                // get path of class
                if (
                    ($classPath = 'RKW\\RkwSearch\\Search\\Filters\\' . ucfirst($className))
                    && (class_exists($classPath))
                ) {

                    // load class
                    if ($class = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($classPath, $this, $data, $configuration)) {

                        // set filter
                        if ($result = $class->getFilter()) {

                            $this->filters[lcfirst($className)] = array (
                                'configuration' => $result,
                                'value' => $data
                            );

                            return TRUE;
                            //===
                        }
                    }
                }
            }
        }

        return FALSE;
        //===
    }



    /**
     * Returns set filter by $className
     *
     * @param string $className
     * @return array
     */
    public function getFilter($className) {

        if ($this->filters[lcfirst($className)])
            return $this->filters[lcfirst($className)];
            //===

        return array();
        //===

    }


    /**
     * Returns query
     *
     * @param boolean $isRelated
     * @returns \RKW\RkwSearch\OrientDb\Storage\Query\Query
     */
    public function getQuery ($related = FALSE) {

        // set query and subquery
        $query = new Query();
        $subQuery = new Query();
        $subQuery->select(array('*'));

        if ($this->getConfiguration('selectFields'))
            $query->select(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->getConfiguration('selectFields')));

        if ($this->getConfiguration('selectFieldsEdges'))
            $query->select(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->getConfiguration('selectFieldsEdges')), TRUE);

        if ($this->getConfiguration('where'))
            $query->andWhere($this->getConfiguration('where'));

        $excludeClassList = array();
        if ($this->getConfiguration('excludeClassList'))
            $excludeClassList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->getConfiguration('excludeClassList'));

        if ($related)
            if ($this->getConfiguration('excludeClassListRelated'))
                $excludeClassList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->getConfiguration('excludeClassListRelated'));

        if (
            ($orderBy = $this->getConfiguration('orderBy'))
            && (is_array($orderBy))
        ) {
            foreach ($orderBy as $field => $direction) {

                // add ordering by fields and add those fields to the select list
                $query->orderBy($field . ' ' . $direction, TRUE);
            }
        }

        foreach ($this->getWhere() as $where)
            $query->andWhere($where);

        // set debug mode if needed
        if ($this->debugMode)
            $query->andWhere('debug = ?', 1);

        // go through all filters
        $subSearchFields = array();
        $subSearchWhere = array();
        $orderingSubQuery = array ();
        $orderingQuery = array();
        $orderAppend = FALSE;
        $orderAppendSub = FALSE;
        $groupingQuery = array();
        $groupAppend = FALSE;
        foreach ($this->filters as $name => $filter) {

            $configuration = $filter['configuration'];

            //=================================================
            // Check fulltext search
            if (
                ($configuration['fulltext'])
                && ($configuration['fulltext']['search'])
                && ($configuration['fulltext']['searchField'])
            ) {

                // add additional select fields to subquery
                if ($configuration['fulltext']['selectFields'])
                    $subQuery->select($configuration['fulltext']['selectFields'], TRUE);

                // add search fields for normal search
                $subSearchFields[] = $configuration['fulltext']['searchField'];

                // search in title
                $titleSearchAddition = '';
                if ($configuration['fulltext']['searchFieldTitle'])
                    // $titleSearchAddition = ' OR ' . $configuration['fulltext']['searchFieldTitle'] . ': (' . $configuration['fulltext']['search'] . ')^' . $configuration['fulltext']['searchFieldTitleBoost'];

                    // NEW VERSION:
                    $titleSearchAddition = ' OR (' . $configuration['fulltext']['searchFieldTitle'] . ': ("' . $configuration['fulltext']['search'] . '")^' . $configuration['fulltext']['searchFieldTitleBoost']*2 . ' OR ' . $configuration['fulltext']['searchFieldTitle'] . ': (' . $configuration['fulltext']['search'] . ')^' . $configuration['fulltext']['searchFieldTitleBoost'] . ')';


                // string search in corpus
                if (
                    ($configuration['fulltext']['searchFuzzy'])
                    && ($configuration['fulltext']['searchFieldFuzzy'])
                    && ($this->fuzzySearchLucene)

                ) {
                    // add fields and string for fuzzy search
                    $subSearchFields[] = $configuration['fulltext']['searchFieldFuzzy'];

                    if ($this->perfectMatchBoostSearchLucene) {
                        $subSearchWhere[] = '((' . $configuration['fulltext']['searchField'] . ': ("' . $configuration['fulltext']['search'] . '")^' . $configuration['fulltext']['searchFieldBoost'] * 2 . ' OR ' . $configuration['fulltext']['searchField'] . ': (' . $configuration['fulltext']['search'] . ')^' . $configuration['fulltext']['searchFieldBoost'] . ')' .
                            ' OR ' . $configuration['fulltext']['searchFieldFuzzy'] . ': (' . $configuration['fulltext']['searchFuzzy'] . ')' . $titleSearchAddition . ')';

                    } else {

                        $subSearchWhere[] = '(' . $configuration['fulltext']['searchField'] . ': (' . $configuration['fulltext']['search'] . ')^' . $configuration['fulltext']['searchFieldBoost'] .
                            ' OR ' . $configuration['fulltext']['searchFieldFuzzy'] . ': (' . $configuration['fulltext']['searchFuzzy'] . ')' . $titleSearchAddition . ')';

                    }

                } else {

                    // add string if there is no fuzzy search
                   if ($this->perfectMatchBoostSearchLucene) {
                        $subSearchWhere[] = '((' . $configuration['fulltext']['searchField'] . ': ("' . $configuration['fulltext']['search'] . '")^' . $configuration['fulltext']['searchFieldBoost'] * 2 . ' OR ' . $configuration['fulltext']['searchField'] . ': (' . $configuration['fulltext']['search'] . ')^' . $configuration['fulltext']['searchFieldBoost'] . ')' . $titleSearchAddition . ')';
                    } else {
                        $subSearchWhere[] = '(' . $configuration['fulltext']['searchField'] . ': (' . $configuration['fulltext']['search'] . ')^' . $configuration['fulltext']['searchFieldBoost'] . $titleSearchAddition . ')';
                    }

                }

                // add field for search in title
                if ($configuration['fulltext']['searchFieldTitle'])
                    $subSearchFields[] = $configuration['fulltext']['searchFieldTitle'];


                // additional filters
                if (
                    ($configuration['fulltext']['searchFieldSize'])
                    && ($configuration['fulltext']['searchFieldType'])

                ) {

                    $subSearchFields[] = $configuration['fulltext']['searchFieldType'];
                    if ($this->publicationBoostSearchLucene) {
                        $subSearchWhere[] = '(searchContentType: (pdf)^50 OR searchContentType:(default))';
                    } else {
                        $subSearchWhere[] = '(searchContentType: (pdf) OR searchContentType:(default))';
                    }

                    $subSearchFields[] = $configuration['fulltext']['searchFieldSize'];
                    if ($this->lengthBoostSearchLucene) {
                        $subSearchWhere[] = '(searchContentSize: (small) OR searchContentSize: (medium)^1.5 OR searchContentSize: (big)^2)';
                    } else {
                        $subSearchWhere[] = '(searchContentSize: (small) OR searchContentSize: (medium) OR searchContentSize: (big))';
                    }
                }

                // set ordering if there is something to order
                if (
                    ($orderBy = $configuration['fulltext']['orderBy'])
                    && (is_array($orderBy))
                ) {

                    foreach ($orderBy as $field => $direction) {

                        // prevent double orderings by the same field
                        if (! in_array($field, $orderingSubQuery)) {

                            // add ordering by fields and add those fields to the select list
                            $subQuery->orderBy($field . ' ' . $direction,  $orderAppendSub);
                            $orderAppendSub = TRUE;
                            $orderingSubQuery[] = $field;
                        }
                    }
                }

                // set limit - just for the case!
                $subQuery->limit($this->getLimit()  * 10);
            }

            //=================================================
            // check normal filters
            if ($configuration['selectFields'])
                $query->select($configuration['selectFields'], TRUE);

            if ($configuration['where'])
                $query->andWhere($configuration['where']);

            if ($configuration['searchClass'])
                $this->setOrientDbClass($configuration['searchClass']);

            if (
                ($orderBy = $configuration['orderBy'])
                && (is_array($orderBy))
            ) {
                foreach ($orderBy as $field => $direction) {

                    // prevent double orderings by the same field
                    if (! in_array($field, $orderingQuery)) {

                        // add ordering by fields and add those fields to the select list
                        $query->orderBy($field . ' ' . $direction, $orderAppend);
                        $orderAppend = TRUE;
                        $orderingQuery[] = $field;
                    }
                }
            }

            if (
                ($groupBy = $configuration['groupBy'])
                && (is_array($groupBy))
            ) {
                foreach ($groupBy as $field) {

                    // prevent double orderings by the same field
                    if (! in_array($field, $groupingQuery)) {

                        // add ordering by fields and add those fields to the select list
                        $query->groupBy($field, $groupAppend);
                        $groupAppend = TRUE;
                        $groupingQuery[] = $field;
                    }
                }
            }
        }

        //=================================================
        // check if we need a subquery here and insert it if needed
        if (
            ($subSearchFields)
            && ($subSearchWhere)
        ) {
            $subQuery->andWhere('[' . implode(',', $subSearchFields) . '] LUCENE ?', array(implode(' AND ', $subSearchWhere)));

            // add sub-query to main-query
            $query->fromQuery($subQuery);
        }

        //=================================================
        // set classes to search from
        $query->from(array($this->getOrientDbClass()), FALSE);
        $subQuery->from(array($this->getOrientDbClass()), FALSE);

        // remove current class from exclude list and add filter accordingly
        $key = array_search($this->getOrientDbClass(), $excludeClassList);
        unset($excludeClassList[$key]);

        if ($excludeClassList)
            $query->andWhere('(NOT (@class IN [\'' . implode('\', \'', $excludeClassList) . '\']))');

        //=================================================
        // insert default filtering by enable fields etc.
        QueryHelper::getWhereClauseForEnableFields($query, $this->getOrientDbClass(), TRUE);
        QueryHelper::getWhereClauseForLanguageFields($query, $this->getLanguageUid(), $this->getOrientDbClass());

        $query->limit($this->getLimit());
        if ($this->getCurrentPage() > 1)
            $query->skip($this->getSkip());

        //=================================================
        // ordering
        $orderAppend = FALSE;
        if (count($this->getOrdering()) > 0) {
            foreach ($this->getOrdering() as $field => $direction) {

                // add ordering by fields and add those fields to the select list
                $query->orderBy($field . ' ' . $direction, $orderAppend);
                $query->select(array($field), TRUE);
                $orderAppend = TRUE;
            }
        }

        //=================================================
        // grouping
        $groupingAppend = FALSE;
        if (count($this->getGrouping()) > 0) {
            foreach ($this->getGrouping() as $field) {

                // add ordering by fields and add those fields to the select list
                $query->groupBy($field, $groupingAppend);
                $query->select(array($field), TRUE);
                $groupingAppend = TRUE;
            }
        }

        return $query;
        //===
    }




    /**
     * Get repository of query class
     *
     * @returns \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentRepositoryInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getRepository () {

        if (
            ($class = $this->getOrientDbClass())
            && ($repositoryName = Common::getOrientRepositoryFromClassName($class))
            && (class_exists($repositoryName))
            && ($repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($repositoryName, $this))
                && ($repository instanceof \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentRepositoryInterface)
        ) {


            $repository->setQueryFactory($this);
            $repository->setDebugMode($this->debugMode);
            return $repository;
            //===
        }

        throw new \RKW\RkwSearch\Exception ('Repository could not be loaded.', 1425563270);
        //===

    }

    /**
     * Sets the current class we select from
     *
     * @param string $orientDbClass
     * @return void
     */
    public function setOrientDbClass($orientDbClass) {

        $this->orientDbClass = $orientDbClass;

    }


    /**
     * Gets the current class we select from
     *
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getOrientDbClass() {

        // if no class is set we load it form configuration
        if (! $this->orientDbClass)
            if ($classMapping = $this->getConfiguration('searchClass'))
                $this->orientDbClass = $classMapping;

        // if still no class is set throw exception
        if (! $this->orientDbClass)
            throw new \RKW\RkwSearch\Exception('No default searchClass configuration found.', 1425315389);
            //===


        return $this->orientDbClass;
        //===

    }


    /**
     * Gets text to process
     *
     * @return NULL|string
     */
    public function getSearchString () {

        return $this->searchString;
        //===
    }


    /**
     * Sets text to process
     *
     * @param string $value
     * @return $this
     */
    public function setSearchString ($value) {

        $this->searchString = $value;
        $this->setFilters();

        return $this;
        //===

    }



    /**
     * Returns language uid
     *
     * @returns integer
     */
    public function getLanguageUid () {

        return intval($this->languageUid);
        //===
    }


    /**
     * Returns the current page for the results
     *
     * @returns integer
     */
    public function getCurrentPage () {

        return intval($this->currentPage);
        //===
    }

    /**
     * Sets the current page for the results
     *
     * @param integer $value
     * @returns void
     */
    public function setCurrentPage ($value) {

        $this->currentPage = intval($value);
    }

    /**
     * Sets the limit value
     *
     * @param integer $value
     */
    public function setLimit ($value) {
        $this->limit = $value;
    }

    /**
     * Returns the limit based on current page
     *
     * @returns integer
     */
    public function getLimit () {

        // if limit is set manually just take this
        if ($this->limit > 0)
            return intval($this->limit)+1;
            //===

        // get limit from configuration
        if (
            ($this->getConfiguration('resultsPerPage'))
            && ($this->getConfiguration('resultsPerPage') > 0)
        )
            return intval($this->getConfiguration('resultsPerPage'))+1;
            //===

        // we always get one more item than we need
        // in order to decide if we need some pagination
        return 10 + 1;
        //===
    }

    /**
     * Returns the items to be skipped based on current page
     *
     * @returns integer
     */
    public function getSkip () {

        $skip = 0;
        if ($this->getCurrentPage() > 1)
            $skip = (intval($this->getCurrentPage()) -1) * (intval($this->getLimit()-1));

        return $skip;
        //===
    }


    /**
     * Returns the value of ordering
     *
     * @returns array
     */
    public function getOrdering () {

        return $this->ordering;
        //===
    }

    /**
     * Sets the value of ordering
     *
     * @param array $orders
     * @returns void
     */
    public function setOrdering ($orders) {

        if (is_array($orders))
            $this->ordering = $orders;
    }

    /**
     * Returns the value of grouping
     *
     * @returns array
     */
    public function getGrouping () {

        return $this->grouping;
        //===
    }

    /**
     * Sets the value of grouping
     *
     * @param array $grouping
     * @returns void
     */
    public function setGrouping($grouping) {

        if (is_array($grouping))
            $this->grouping = $grouping;
    }


    /**
     * Gets the value for where
     *
     * @returns array
     */
    public function getWhere () {
        return $this->where;
        //===
    }


    /**
     * Sets the value for where
     *
     * @param string $string
     * @returns void
     */
    public function setWhere ($string) {
        $this->where[] = $string;
    }


    /**
     * Set the debug mode for the repository
     *
     * @param boolean $value
     * @return $this
     */
    public function setDebugMode($value) {
        $this->debugMode= (boolean) $value;
    }


    /**
     * Set the fuzzy search value
     *
     * @param boolean $value
     * @return $this
     */
    public function setFuzzySearchLucene($value) {
        $this->fuzzySearchLucene = (boolean) $value;

    }


    /**
     * Set the publication boost search value
     *
     * @param boolean $value
     * @return $this
     */
    public function setPublicationBoostSearchLucene($value) {
        $this->publicationBoostSearchLucene = (boolean) $value;

    }

    /**
     * Set the length boost search value
     *
     * @param boolean $value
     * @return $this
     */
    public function setLengthBoostSearchLucene($value) {
        $this->lengthBoostSearchLucene = (boolean) $value;

    }


    /**
     * Set the perfect match boost search value
     *
     * @param boolean $value
     * @return $this
     */
    public function setPerfectMatchBoostSearchLucene($value) {
        $this->perfectMatchBoostSearchLucene = (boolean) $value;

    }


    /**
     * Groups results by import parent uid
     *
     * @param \RKW\RkwSearch\OrientDb\Collection\Document|NULL $results
     * @return \RKW\RkwSearch\OrientDb\Collection\Document
     * @deprecated Will be removed
     */
    public function groupResultsByImportParentUid ($results) {

        $groupedResult = array();
        $loadedPublications = array ();
        if ($results instanceof \RKW\RkwSearch\OrientDb\Collection\Document) {
            foreach ($results as $item) {
                if (
                    ($item->getPdfImportSub())
                    && ($item->getPdfImportParentUid())
                ){
                    // load main page of publication if we didn't already during this loop here
                    if (! $loadedPublications[intval($item->getPdfImportParentUid())]) {
                        $tempPublication = $this->getRepository()->findByUid($item->getPdfImportParentUid(), NULL, array('select' => \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',',$this->getConfiguration('selectFields'))));
                        if ($tempPublication)
                            $loadedPublications[intval($item->getPdfImportParentUid())] =  $tempPublication;
                    }

                    // only if not already included!
                    if (! $groupedResult[$item->getPdfImportParentUid()])
                        $groupedResult[$item->getPdfImportParentUid()] =  $loadedPublications[intval($item->getPdfImportParentUid())];

                } else {
                    $groupedResult[$item->getUid()] = $item;
                }
            }

            // rebuild collection
            $results = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $groupedResult);
        }

        return $results;
        //===
    }

    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getConfiguration($key = NULL) {

        if (! $this->settings) {

            // load from TypoScript
            $settings = Common::getTyposcriptConfiguration();
            if ($settings['queryFactory'])
                $this->settings = $settings['queryFactory'];
        }

        if (! $this->settings)
            throw new \RKW\RkwSearch\Exception('No valid configuration found.', 1424157460);
            //===

        if ($key) {

            if ($this->settings[$this->languageUid][$key])
                return $this->settings[$this->languageUid][$key];
                //===

            return $this->settings[0][$key];
            //===
        }

        return $this->settings;
        //===
    }



    /**
     *  Constructor
     *
     * @param integer $languageUid
     * @param array $configuration
     */
    public function __construct ($languageUid = 0, $configuration = array()) {

        // set language
        $this->languageUid = intval($languageUid);

        // set given configuration (if given)
        if (!empty ($configuration))
            $this->settings = $configuration;

    }



}