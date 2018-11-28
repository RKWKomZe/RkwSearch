<?php
namespace RKW\RkwSearch\Search\Filters;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\OrientDb\Helper\Query as QueryHelper;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;
use RKW\RkwSearch\Helper\Text;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is use \RKW\RkwSearch\OrientDb\Storage\Query\Query;
use RKW\RkwSearch\OrientDb\Helper\Query as QueryHelper;ee software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class FiltersAbstract
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

abstract class FiltersAbstract implements FiltersInterface {

    /**
     *
     * @const string Delimiter for explode of one item in separate words
     */
    const STRING_DELIMITER = ' ';


    /**
     * @var \RKW\RkwSearch\TreeTagger\Collection\Records|string
     */
    protected $data;


    /**
     * @var array Contains the TypoScript configuration
     */
    protected $configuration;



    /**
     * Returns filter data
     *
     * @returns array
     */
    public function getFilter () {


        if (
            ($edgeClass = $this->getConfiguration('edgeClass'))
            && ($searchField = $this->getConfiguration('searchField'))
        ) {

            $edgeClassTwo = NULL;
            if ($this->getConfiguration('edgeClassTwo'))
                $edgeClassTwo = $this->getConfiguration('edgeClassTwo');

            // check if there is at least one match
            if (
                ($data = $this->getDataPrepared())
                && (count($data['wordsArray']) > 0)
            ) {

                $searchFieldFuzzy = $this->getConfiguration('searchFieldFuzzy');
                $searchFieldTwo = $this->getConfiguration('searchFieldTwo');
                $searchFieldTwoFuzzy = $this->getConfiguration('searchFieldTwoFuzzy');

                $edgeDirection = 'in';
                if ($this->getConfiguration('edgeDirection') == 'out')
                    $edgeDirection = 'out';

                $edgeDirectionTwo = 'in';
                if ($this->getConfiguration('edgeDirectionTwo') == 'out')
                    $edgeDirectionTwo = 'out';

                $whereClause = array();
                foreach ($data['wordsArray'] as $cnt => $department) {

                    $departmentFuzzy = '';
                    if (isset($data['wordsArrayFuzzy'][$cnt]))
                        $departmentFuzzy = $data['wordsArrayFuzzy'][$cnt];

                    //==============================================================
                    // if two search fields are set
                    if ($searchFieldTwo) {

                        // two fields with fuzzy
                        if (
                            ($searchFieldFuzzy)
                            && ($searchFieldTwoFuzzy)
                            && ($departmentFuzzy)
                        ) {

                            $whereClause[] = '(' .
                                '(' .
                                    $searchField . ' = "' . addslashes($department) . '" ' .
                                    'OR ' . $searchFieldFuzzy . ' = "' . addslashes($departmentFuzzy) . '"' .
                                ') OR (' .
                                    $searchFieldTwo . ' = "' . addslashes($department) . '" ' .
                                    'OR ' . $searchFieldTwoFuzzy . ' = "' . addslashes($departmentFuzzy) . '"' .
                                ')' .
                            ')';

                        } else {

                            // pretend that no fuzzy field is set
                            $whereClause[] = '(' .
                                '(' .
                                    $searchField . ' = "' . addslashes($department) . '"' .
                                ') OR (' .
                                    $searchFieldTwo . ' = "' . addslashes($department) . '"' .
                                ')' .
                            ')';
                        }

                    //==============================================================
                    // if only one search field is set!
                    } else {

                        // two fields with fuzzy
                        if (
                            ($searchFieldFuzzy)
                            && ($departmentFuzzy)
                        ) {

                            $whereClause[] = '(' .
                                '(' .
                                    $searchField . ' = "' . addslashes($department) . '" ' .
                                    'OR ' . $searchFieldFuzzy . ' = "' . addslashes($departmentFuzzy) . '"' .
                                ')' .
                            ')';
                        } else {

                            // pretend that no fuzzy field is set
                            $whereClause[] = '(' .
                                '(' .
                                    $searchField . ' = "' . addslashes($department) . '"' .
                                ')' .
                            ')';
                        }
                    }
                }

                $whereClauseAddition = '';
                if ($edgeClassTwo)
                    $whereClauseAddition = '.' . $edgeDirectionTwo . '(\'' . addslashes($edgeClassTwo) . '\')';

                $defaultWhereClause = '';
                if ($vertexClass = $this->getConfiguration('vertexClass')) {
                    QueryHelper::getWhereClauseForEnableFields($defaultWhereClause, $vertexClass);
                    QueryHelper::getWhereClauseForLanguageFields($defaultWhereClause, $this->getLanguageUid(), $vertexClass);
                }
                if ($vertexClassTwo = $this->getConfiguration('vertexClassTwo')) {
                    QueryHelper::getWhereClauseForEnableFields($defaultWhereClause, $vertexClassTwo);
                    QueryHelper::getWhereClauseForLanguageFields($defaultWhereClause, $this->getLanguageUid(), $vertexClassTwo);
                }

                return array (
                    'selectFields' => (($this->getConfiguration('selectFieldsAddition')) ? explode(',', $this->getConfiguration('selectFieldsAddition')) : array ()),
                    'searchClass' => (($this->getConfiguration('searchClass')) ? $this->getConfiguration('searchClass') : NULL),
                    'where' => '(' . $edgeDirection . '(\'' . addslashes($edgeClass) . '\')' . $whereClauseAddition . ' contains (' . implode(' OR ', $whereClause) . ') ' . $defaultWhereClause . ')',
                    'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array()),
                );
                //===

            }
        }

        if ($this->getConfiguration('searchClass'))
            return array (
                'searchClass' => $this->getConfiguration('searchClass'),
                'orderBy' => ( ($this->getConfiguration('orderBy') && is_array($this->getConfiguration('orderBy'))) ? $this->getConfiguration('orderBy') : array()),
            );
            //===

        return array();
        //===
    }



    /**
     * Gets the prepared data to run through
     *
     * @returns array
     */
    public function getDataPrepared () {

        $result = array(
            'stringLucene' => '',
            'wordsArray' => array (),
            'wordsArrayFuzzy' => array()
        );

        // check for conjunctions settings
        if (
            ($conjunctions = $this->getConfiguration('conjunctionMapping'))
            && is_array($conjunctions)
        ){

            // go through all conjunctions and replace them by their mapping for lucene
            $result['stringLucene'] = Text::sanitizeStringLucene(str_replace(array_keys($conjunctions), array_values($conjunctions), $this->getData()));

            // for everything else we just split them here and put them into an array
            foreach (Common::multiExplode(array_keys($conjunctions), $this->getData()) as $item) {
                //$preparedString = trim(Text::sanitizeStringOrientDb(Text::sanitizeString(Text::removeStopWords($item, $this->getLanguageUid()))));
                $preparedString = trim(Text::sanitizeStringOrientDb(Text::sanitizeString($item, $this->getLanguageUid())));
                $result['wordsArray'][] = $preparedString;
                $result['wordsArrayFuzzy'][] = ColognePhonetic::encode($preparedString);
            }

        } else {
            $result['stringLucene'] = Text::sanitizeStringLucene($this->getData());
            //$preparedString = trim(Text::sanitizeStringOrientDb(Text::sanitizeString(Text::removeStopWords($this->getData(), $this->getLanguageUid()))));
            $preparedString = trim(Text::sanitizeStringOrientDb(Text::sanitizeString($this->getData(), $this->getLanguageUid())));

            $result['wordsArray'][] = $preparedString;
            $result['wordsArrayFuzzy'][] = ColognePhonetic::encode($preparedString);
        }

        // remove fuzzy search, if an exact search is desired!
        if (strpos($result['stringLucene'], '"') !== FALSE)
            unset($result['wordsArrayFuzzy']);

        return $result;
        //===
    }


    /**
     * Returns the data
     *
     * @returns string
     */
    public function getData() {

        return $this->data;
        //===

    }


    /**
     * Returns language uid
     *
     * @returns integer
     */
    public function getLanguageUid () {

        return intval($this->getQueryFactory()->getLanguageUid());
        //===
    }


    /**
     * Returns QueryFactory
     *
     * @returns \RKW\RkwSearch\Search\QueryFactory
     */
    public function getQueryFactory () {

        return $this->queryFactory;
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

        $className =lcfirst(Common::getShortName($this));
        if (! $this->configuration)
            throw new \RKW\RkwSearch\Exception(sprintf('No valid configuration for QueryFactory "%s" found.', $className), 1422347731);
            //===

        if ($key) {

            if ($this->configuration[$key])
                return $this->configuration[$key];
                //===

            return NULL;
            //===
        }

        return $this->configuration[$className];
        //===
    }



    /**
     *  Constructor
     *
     * @param \RKW\RkwSearch\Search\QueryFactory $queryFactory
     * @param string $data
     * @param array $configuration
     * @throws \RKW\RkwSearch\Exception
     */
    public function __construct (&$queryFactory, $data = NULL, $configuration = array()) {

        // set queryFactory
        $this->queryFactory = $queryFactory;
        if (! $this->queryFactory instanceof \RKW\RkwSearch\Search\QueryFactory)
            throw new \RKW\RkwSearch\Exception('No valid QueryFactory given.', 1426940441);
            //===

        // check data
        // only string allowed
        $this->data = $data;
        if (
            (! is_null($this->data))
            && (! is_string($this->data))
            && (! is_numeric($this->data))
        )
            throw new \RKW\RkwSearch\Exception('Invalid data given.', 1424871863);
            //===

        // set given configuration (if given)
        if (!empty ($configuration))
            $this->configuration = $configuration;
    }



}