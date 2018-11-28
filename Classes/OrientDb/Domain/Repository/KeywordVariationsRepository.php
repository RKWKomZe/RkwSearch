<?php
namespace RKW\RkwSearch\OrientDb\Domain\Repository;
use \RKW\RkwSearch\OrientDb\Storage\Query\Query;
use RKW\RkwSearch\Helper\Text;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;
use RKW\RkwSearch\OrientDb\Helper\Tca\OrientDbFields;
use Doctrine\OrientDB\Query\Validator\Escaper as EscapeValidator;

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
 * Class KeywordVariationsRepository
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

class KeywordVariationsRepository extends KeywordRepositoryAbstract implements KeywordVariationsRepositoryInterface {

    /**
     * Adds an object to this repository or simply updates it
     * We need to use a separate method here since we have no mapping-id for keywords to check against
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param boolean $forceInsert Forces insertion
     * @param integer $queryCount Counts the number of queries
     * @return \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariations
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function add($object, $forceInsert = FALSE, &$queryCount = 0) {

        /** @var \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object */
        $objectType = $this->getObjectType();
        if (!$object instanceof $objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $objectType . ') this repository manages.', 1422890972);
            //===

        // Create variation-keyword if needed
        $entry = NULL;
        $result = parent::add($object, $forceInsert);
        if ($result) {

            // get object for further processing
            $entry = $this->findOneByName($object->getName(), $object->getLanguageUid());
            $queryCount++;

            // Update if not newly inserted
            // May be relevant for updating counter or changed object data
            if (
                ($result == 2)
                && ($entry)
            ) {
                $properties = $object->getPropertiesChanged();
                $entry->setProperties($properties);

                $this->update($entry);
                $queryCount++;
            }
        }

        return $entry;
        //===

    }


    /**
     * Finds related keywords based on given keyword
     *
     * @param string $keyword Keyword to search for
     * @param integer $limit Limit of items to return
     * @param boolean $fuzzyIgnore If set to TRUE fuzzySearch will be turned off
     * @param boolean $includeCombined If set to TRUE combined keywords will be returned
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @api
     */
    public function findRelated($keyword, $limit = 100, $fuzzyIgnore = FALSE, $includeCombined = FALSE) {

        mb_internal_encoding('UTF-8');

        // sanitize string
        // we need to replace ß by ss here for OrientDb, too
        // @see: RKW\RkwSearch\OrientDb\Storage\Query\Query::getRaw()
        $escapeValidator = new EscapeValidator();
        $keywordForLucene = $escapeValidator->check(trim(Text::sanitizeStringLucene(Text::encodeGermanUmlauts($keyword), TRUE)));
        $keywordForOrientDb = $escapeValidator->check(trim(Text::sanitizeStringOrientDb(str_replace('ß', 'ss', $keyword))));

        // check if there is something left after sanitize
        if (
            ($keywordForLucene)
            && ($keywordForOrientDb)
        ) {

            $fuzzyAppendix = OrientDbFields::getCtrlField($this->getOrientDbClass(), 'fuzzyAppendix');

            // select-fields and conditions for queries
            $select = array(
                "inE('EdgeContains').size() AS edgeCounter",
                "name",
                "nameCaseSensitive",
                "keywordType",
                "nameLength",
                '@class',
                '@type',
                '@rid'
            );

            // build wrapper query for ordering
            $queryOne = new Query();
            $queryOne->select($select);
            $queryOne->orderBy('searchCounter ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING);
            $queryOne->orderBy('edgeCounter ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING);
            $queryOne->orderBy('keywordType ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING);
            $queryOne->orderBy('nameLength ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_ASCENDING);

            // build second query for further WHERE conditions
            // because after LUCENE the rest is ignored!
            $queryTwo = new Query();
            $queryTwo->select(array('*'));
            $queryTwo->orderBy('score ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING);

            // build lucene query with limit
            $queryLucene = new Query ();
            $queryLucene->select(array('*, $score AS score'));
            $queryLucene->from(array($this->getOrientDbClass()));

            // This is inefficient
            // $queryLucene->orderBy('score ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING);

            // check if fuzzy search is active
            // only use fuzzy search for encoded words > 4 signs
            // otherwise the amount of words gets to big and the query takes too long
            $useFuzzy = FALSE;
            if (
                (!OrientDbFields::getCtrlField($this->getOrientDbClass(), 'fuzzyIgnore'))
                && (!$fuzzyIgnore)
                && ($fuzzyAppendix)
                && (mb_strlen(ColognePhonetic::encode(trim($keywordForOrientDb))) > 4)
            )
                $useFuzzy = TRUE;

            $keywordQuery = NULL;
            $keywordQueryFuzzy = NULL;
            $searchFieldBoost = 0;
            //==================================================
            // default search
            $keywordQuery = str_replace(' ', ' AND ', $keywordForLucene) . '*';

            //==================================================
            // add fuzzy search if activated and configured
            if ($useFuzzy) {

                if (strpos(trim($keywordForLucene), ' ') > 0) {
                    $keywordQueryFuzzy = str_replace(' ', ' AND ', ColognePhonetic::encode($keywordForLucene)) . '*';
                } else {
                    $keywordQueryFuzzy = str_replace(' ', ' AND ', ColognePhonetic::encode($keywordForLucene));
                }
            }

            //==================================================
            // build final WHERE query
            $keywordQueryFinal = 'name:(' . Text::sanitizeStringLucene($keywordQuery) . ')^' . $searchFieldBoost;
            $keywordQueryFields = 'name,keywordType';
            if (
                    ($keywordQueryFuzzy)
                    && ($useFuzzy)
            ) {
                $keywordQueryFinal = '(name:(' . Text::sanitizeStringLucene($keywordQuery) . ')^' . $searchFieldBoost . ' OR name' . ucFirst($fuzzyAppendix) . ': (' . Text::sanitizeStringLucene($keywordQueryFuzzy) . '))';
                $keywordQueryFields = 'name,name' . ucFirst($fuzzyAppendix) . ',keywordType';
            }

            if (!$includeCombined)
                $keywordQueryFinal .= ' AND keywordType:("default")';

            $queryLucene->andWhere('[' . $keywordQueryFields . '] LUCENE ?', array($keywordQueryFinal));

            //==================================================
            // find only words that start with the given keyword!
            if (!$includeCombined) {

                if ($useFuzzy) {

                    $queryTwo->andWhere('
                        (
                            name.left(' . mb_strlen($keywordForOrientDb) . ') = ?
                            OR name' . ucFirst($fuzzyAppendix) . '.left(' . mb_strlen(ColognePhonetic::encode($keywordForOrientDb)) . ') = ?
                        )',
                            array($keywordForOrientDb, ColognePhonetic::encode($keywordForOrientDb)));
                } else {

                    $queryTwo->andWhere('
                        (
                            name.left(' . mb_strlen($keywordForOrientDb) . ') = ?
                        )',
                            array($keywordForOrientDb));
                }
            }

            //==================================================
            // additional settings
            $this->getWhereClauseForEnableFields($queryTwo);
            if ($this->getEnvironmentMode() == 'FE')
                $this->getWhereClauseForLanguageFields($queryTwo, intval($GLOBALS['TSFE']->sys_language_uid));

            // set limit - to speed the query up we set a limit for Lucene to (factor 10)
            $queryLucene->limit(intval($limit * 10));
            $queryTwo->limit(intval($limit));

            // find only keywords which link to documents
            //$queryOne->andWhere('edgeCounter > 0');

            $queryTwo->fromQuery($queryLucene);
            $queryOne->fromQuery($queryTwo);

            return $this->getOrientDbDatabase()->execute($queryOne);
            //===
        }

        return NULL;
        //===
    }

    /**
     * Finds most searched keywords
     *
     * @param integer $limit Number of keywords to return
     * @return \RKW\RkwSearch\OrientDb\Collection\Document|NULL
     * @api
     */
    public function findMostSearched($limit = 10) {

        // build wrapper query for ordering
        $query = new Query();
        $query->select(array('*'));
        $query->from(array($this->getOrientDbClass()));
        $query->limit(intval($limit));
        $query->orderBy('searchCounter ' . \RKW\RkwSearch\OrientDb\Storage\Query\QueryInterface::ORDER_DESCENDING);

        $this->getWhereClauseForEnableFields($query);
        if ($this->getEnvironmentMode() == 'FE')
            $this->getWhereClauseForLanguageFields($query, intval($GLOBALS['TSFE']->sys_language_uid));


        return $this->getOrientDbDatabase()->execute($query);
        //===

    }

}
?>