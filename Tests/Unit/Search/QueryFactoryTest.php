<?php
namespace RKW\RkwSearch\Tests\Search;

/**
 * Class QueryFactoryTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class QueryFactoryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\QueryFactory
     */
    protected $fixture;


    /**
     * @var array
     */
    protected $data;

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Query
     */
    protected $query;


    /**
     * @var array
     */
    protected $configuration;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->configuration = array (
            0 => array (

                'selectFields' => 'test',
                'searchClass' => 'DocumentAbstract',
                'filterMapping' => array (
                    '_default' => 'keywords',
                    'autor' => 'author',
                    'typ' => 'type',
                    'fachbereich' => 'department',
                    'von' => 'dateFrom',
                    'bis' => 'dateTo',
                    'publikation' => 'publication',
                    'news' => 'news'
                ),

                // filters
                'filters' => array (


                    'author' => array (

                        'selectFieldsAddition' => 'test',
                        'orderBy' => array (
                            'test' => 'ASC'
                        ),

                        'edgeClass' => 'EdgeAuthor',
                        'edgeDirection' => 'out',

                        'searchField' => 'firstname',
                        'searchFieldFuzzy' => 'firstnameFuzzy',

                        'searchFieldTwo' => 'lastname',
                        'searchFieldTwoFuzzy' => 'lastnameFuzzy',

                        'conjunctionMapping' => array (
                            'ODER' => 'OR',
                            'UND' => 'OR',
                        ),
                    ),

                    'type' => array (

                        'selectFieldsAddition' => 'test',
                        'orderBy' => array (
                            'test' => 'ASC'
                        ),

                        'edgeClass' => 'EdgeType',
                        'edgeDirection' => 'out',

                        'searchField' => 'name',
                        'searchFieldFuzzy' => 'nameFuzzy',

                        'conjunctionMapping' => array (
                            'ODER' => 'OR',
                            'UND' => 'OR',
                        ),
                    ),

                    'department' => array (

                        'selectFieldsAddition' => 'test',
                        'orderBy' => array (
                            'test' => 'ASC'
                        ),

                        'edgeClass' => 'EdgeDepartment',
                        'edgeDirection' => 'out',

                        'searchField' => 'name',
                        'searchFieldFuzzy' => 'nameFuzzy',

                        'searchFieldTwo' => 'shortName',
                        'searchFieldTwoFuzzy' => 'shortNameFuzzy',

                        'conjunctionMapping' => array (
                            'ODER' => 'OR',
                            'UND' => 'OR',
                        ),
                    ),

                    'publication' => array (

                        'selectFieldsAddition' => 'pdfImport',

                        'orderBy' => array (
                            'title' => 'ASC'
                        ),

                        'groupBy' => array (
                            'pdfImportParentUid'
                        ),

                        'searchField' => 'pdfImport',
                        'searchFieldTwo' => 'pdfImportSub',


                    ),

                    'news' => array (

                        'selectFieldsAddition' => 'pdfImportSub',

                        'orderBy' => array (
                            'pubdate' => 'ASC'
                        ),

                        'searchField' => 'pdfImportSub',
                        'searchFieldTwo' => 'pubdate',
                    ),


                    'events' => array (

                        'searchClass' => 'DocumentEvents',

                        'orderBy' => array (
                            'title' => 'ASC'
                        ),
                    ),

                    'dateFrom' => array (

                        'searchFields' => array (
                            'starttime',
                            'crdate'
                        ),

                        'orderBy' => array (
                            'starttime' => 'ASC'
                        ),

                        'monthMapping' => array (
                            'Januar' => 1,
                            'Februar' => 2,
                            'März' => 3,
                            'April' => 4,
                            'Mai' => 5,
                            'Juni' => 6,
                            'Juli' => 7,
                            'August' => 8,
                            'September' => 9,
                            'Oktober' => 10,
                            'November' => 11,
                            'Dezember' => 12,
                        ),
                    ),

                    'dateTo' => array (
                        'searchFields' => array (
                            'starttime',
                            'crdate'
                        ),

                        'orderBy' => array (
                            'starttime' => 'ASC'
                        ),

                        'monthMapping' => array (
                            'Januar' => 1,
                            'Februar' => 2,
                            'März' => 3,
                            'April' => 4,
                            'Mai' => 5,
                            'Juni' => 6,
                            'Juli' => 7,
                            'August' => 8,
                            'September' => 9,
                            'Oktober' => 10,
                            'November' => 11,
                            'Dezember' => 12,
                        ),
                    ),

                    'keywords' => array (

                        'selectFieldsAddition' => 'keywords',

                        'searchField' => 'searchContent',
                        'searchFieldBoost' => 2.5,
                        'searchFieldFuzzy' => 'searchContentFuzzy',

                        'searchFieldType' => 'searchContentType',
						'searchFieldSize' => 'searchContentSize',

                        'orderBy' => array (
                            'keywords' => 'ASC'
                        ),

                        'conjunctionMapping' => array (
                            'UND' => 'AND',
                            'ODER' => 'OR',
                        ),

                    )
                ),
            )
        );

        $this->fixture = new \RKW\RkwSearch\Search\QueryFactory(0, $this->configuration);

        $this->query = new \RKW\RkwSearch\OrientDb\Storage\Query\Query();
        $this->query->select(array('*'));
        $this->query->from(array('Test'), FALSE);
        $this->query->resetWhere();

    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {
        unset($this->fixture);
    }


    //==========================================

    /**
     * @test
     */
    public function setFiltersWithoutValidConfigurationReturnsFalse() {

        unset($this->configuration[0]['filterMapping']);
        $this->fixture = new \RKW\RkwSearch\Search\QueryFactory(0, $this->configuration);
        $this->assertEmpty($this->fixture->setFilters());

    }

    /**
     * @test
     */
    public function setFiltersWithMultipleFlagsReturnsExpectedArray() {

        $this->fixture->setSearchString('Fachkräftesicherung UND Innovationen umsetzen ODER Gründung --autor: Dr. Alexander Blaeser-Benfer --von: März 2011 --bis: April 2014');
        $result = array (
            'keywords' => Array (
                'configuration' => array (
                    'fulltext' => Array (
                        'search' => 'Fachkräftesicherung AND Innovationen umsetzen OR Gründung',
                        'searchFuzzy' => 'f3473284764 OR i063266 u06886 OR g476264',
                        'searchField' => 'searchContent',
                        'searchFieldFuzzy' => 'searchContentFuzzy',
                        'searchFieldType' => 'searchContentType',
                        'searchFieldSize' => 'searchContentSize' ,
                        'searchFieldBoost' => 2.5,
                        'selectFields' => Array (
                            0 => 'keywords'
                        ),
                        'orderBy' => Array (
                            'keywords' => 'ASC'
                        ),
                    ),
                    'selectFields' => Array (
                        0 => 'keywords'
                    ),
                    'searchClass' => NULL,
                    'orderBy' => Array (
                        'keywords' => 'ASC'
                    )
                ),
                'value' => 'Fachkräftesicherung UND Innovationen umsetzen ODER Gründung'
            ),
            'author' => Array (
                'configuration' => array (
                    'selectFields' => Array (
                        0 => 'test'
                    ),
                    'searchClass' => NULL,
                    'where' => '(out(\'EdgeAuthor\') contains (((firstname = "Alexander" OR firstnameFuzzy = "a0548627") OR (lastname = "Blaeser-Benfer" OR lastnameFuzzy = "b15871637"))))',
                    'orderBy' => array (
                        'test' => 'ASC'
                    ),
                ),
                'value' => 'Dr. Alexander Blaeser-Benfer'
            ),
            'dateFrom' => array (
                'configuration' => array (
                    'selectFields' => array (),
                    'searchClass' => NULL,
                    'orderBy' => Array (
                        'starttime' => 'ASC'
                    ),
                    'where' => '((starttime >= 1298934000) AND (crdate >= 1298934000))'
                ),
                'value' => 'März 2011'
            ),
            'dateTo' => array (
                'configuration' => array (
                    'selectFields' => Array (),
                    'searchClass' => NULL,
                    'orderBy' => Array (
                        'starttime' => 'ASC'
                    ),
                    'where' => '((starttime < 1398895200 AND starttime > 0) AND (crdate < 1398895200 AND crdate > 0))'
                ),
                'value' => 'April 2014'
            )
        );

        $this->assertSame($result, $this->fixture->setFilters());

    }

    /**
     * @test
     */
    public function setFiltersWithMultipleFlagsAndGetQueryReturnsExpectedQuery() {

        $this->fixture->setSearchString('Fachkräftesicherung UND Innovationen umsetzen ODER Gründung --news --autor: Dr. Alexander Blaeser-Benfer --von: März 2011 --bis: April 2014 --publikation');
        $this->fixture->setFilters();

        $this->assertSame(
            'SELECT test, keywords, pdfImportSub, pdfImport FROM ' .
            '(' .
                'SELECT *, keywords FROM DocumentAbstract ' .
                'WHERE [searchContent,searchContentFuzzy] LUCENE "' .
                    '(searchContent: (Fachkräftesicherung AND Innovationen umsetzen OR Gründung)^2.5 OR searchContentFuzzy: (f3473284764 OR i063266 u06886 OR g476264))' .
                '" ORDER BY keywords ASC' .
            ') WHERE (NOT (pdfImportSub = 1) AND pubdate > 0) ' .
            'AND (out(\'EdgeAuthor\') contains (((firstname = "Alexander" OR firstnameFuzzy = "a0548627") OR (lastname = "Blaeser-Benfer" OR lastnameFuzzy = "b15871637")))) ' .
            'AND ((starttime >= 1298934000) AND (crdate >= 1298934000)) ' .
            'AND ((starttime < 1398895200 AND starttime > 0) AND (crdate < 1398895200 AND crdate > 0)) ' .
            'AND (pdfImport = 1 OR pdfImportSub = 1) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) GROUP BY pdfImportParentUid ORDER BY keywords ASC, pubdate ASC, test ASC, starttime ASC, title ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );

    }

    //==========================================

    /**
     * @test
     */
    public function setFilterWithoutValidConfigurationReturnsFalse() {

        unset($this->configuration[0]['filters']);
        $this->fixture = new \RKW\RkwSearch\Search\QueryFactory(0, $this->configuration);
        $this->assertFalse($this->fixture->setFilter('author', 'test'));

    }


    /**
     * @test
     */
    public function setFilterWithInvalidClassReturnsFalse() {

        $this->assertFalse($this->fixture->setFilter('test', 'test'));

    }

    /**
     * @test
     */
    public function setFilterWithValidFilterReturnsTrue() {

        $this->assertTrue($this->fixture->setFilter('author', 'test'));

    }


    /**
     * @test
     */
    public function setFilterWithFilterAuthorAndGetFilterReturnsExpectedFilter() {

        $this->fixture->setFilter('author', 'test');
        $result = array (
            'configuration' => Array (
                'selectFields' => Array (
                    0 => 'test'
                ),
                'searchClass' => NULL,
                'where' => '(out(\'EdgeAuthor\') contains (((firstname = "test" ) OR (lastname = "test" ))))',
                'orderBy' => Array (
                    'test' => 'ASC'
                )
            ),
            'value' => 'test'
        );

        $this->assertSame($result, $this->fixture->getFilter('author'));

    }


    //==========================================

    /**
     * @test
     */
    public function getQueryReturnsInstanceOfQuery() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Query', $this->fixture->getQuery());
    }


    /**
     * @test
     */
    public function getQueryWithSetFilterAuthorsReturnsExpectedQuery() {
        $this->fixture->setFilter('author', '(Dr. Alexander Blaeser-Benfer UND Dr. Kai Morgenstern) ODER Clemens Queißner');

        $this->assertSame(
            'SELECT test FROM DocumentAbstract ' .
            'WHERE (out(\'EdgeAuthor\') contains (((firstname = "Alexander" OR firstnameFuzzy = "a0548627") OR (lastname = "Blaeser-Benfer" OR lastnameFuzzy = "b15871637")) OR ((firstname = "Kai" OR firstnameFuzzy = "k4") OR (lastname = "Morgenstern" OR lastnameFuzzy = "m67468276")) OR ((firstname = "Clemens" OR firstnameFuzzy = "c85668") OR (lastname = "Queissner" OR lastnameFuzzy = "q4867")))) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY test ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );

    }


    /**
     * @test
     */
    public function getQueryWithSetFilterKeywordsAndQuotationMarksReturnsExpectedQuery() {
        $this->fixture->setFilter('keywords', '(Fachkräftesicherung UND "Fachkräfte finden und binden") ODER Innovationen');

        $this->assertSame(
            'SELECT test, keywords FROM ' .
            '(' .
                'SELECT *, keywords FROM DocumentAbstract ' .
                'WHERE [searchContent,searchContentType] LUCENE "' .
                '(searchContent: ((Fachkräftesicherung AND \"Fachkräfte finden und binden\") OR Innovationen)^2.5) ' .
                'AND (searchContentType: (pdf) OR searchContentType:(default))' .
                '" ORDER BY keywords ASC LIMIT 110' .
            ') WHERE (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY keywords ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    /**
     * @test
     */
    public function getQueryWithSetFilterKeywordsAndSetFuzzySearchLuceneFalseReturnsExpectedQuery() {
        $this->fixture->setFilter('keywords', '(Fachkräftesicherung UND Fachkräfte finden und binden) ODER Innovationen');
        $this->fixture->setFuzzySearchLucene(FALSE);

        $this->assertSame(
            'SELECT test, keywords FROM ' .
            '(' .
                'SELECT *, keywords FROM DocumentAbstract ' .
                'WHERE [searchContent,searchContentType] LUCENE "' .
                '(searchContent: ((Fachkräftesicherung AND Fachkräfte finden und binden) OR Innovationen)^2.5) ' .
                'AND (searchContentType: (pdf) OR searchContentType:(default))' .
                '" ORDER BY keywords ASC LIMIT 110' .
            ') WHERE (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY keywords ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }


    /**
     * @test
     */
    public function getQueryWithSetFilterKeywordsReturnsExpectedQuery() {
        $this->fixture->setFilter('keywords', '(Fachkräftesicherung UND "Fachkräfte finden und binden) ODER Innovationen');

        $this->assertSame(
            'SELECT test, keywords FROM ' .
            '(' .
                'SELECT *, keywords FROM DocumentAbstract ' .
                'WHERE [searchContent,searchContentFuzzy,searchContentType] LUCENE "' .
                '(' .
                    'searchContent: ((Fachkräftesicherung AND Fachkräfte finden und binden) OR Innovationen)^2.5 ' .
                    'OR searchContentFuzzy: (f3473284764 OR f34732 f3626 u062 b1626 OR i063266)' .
                ') AND (searchContentType: (pdf) OR searchContentType:(default))'.
                '" ORDER BY keywords ASC LIMIT 110' .
            ') WHERE (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY keywords ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }


    /**
     * @test
     */
    public function getQueryWithSetFilterKeywordsAndSetPublicationBoostSearchLuceneTrueReturnsExpectedQuery() {
        $this->fixture->setFilter('keywords', '(Fachkräftesicherung UND "Fachkräfte finden und binden) ODER Innovationen');
        $this->fixture->setPublicationBoostSearchLucene(TRUE);

        $this->assertSame(
            'SELECT test, keywords FROM ' .
            '(' .
                'SELECT *, keywords FROM DocumentAbstract ' .
                'WHERE [searchContent,searchContentFuzzy,searchContentType] LUCENE "' .
                '(' .
                    'searchContent: ((Fachkräftesicherung AND Fachkräfte finden und binden) OR Innovationen)^2.5 ' .
                    'OR searchContentFuzzy: (f3473284764 OR f34732 f3626 u062 b1626 OR i063266)' .
                ') AND (searchContentType: (pdf)^10 OR searchContentType:(default))'.
                '" ORDER BY keywords ASC LIMIT 110' .
            ') WHERE (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY keywords ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }


    /**
     * @test
     */
    public function getQueryWithSetFilterKeywordsAndSetLengthBoostSearchLuceneReturnsExpectedQuery() {
        $this->fixture->setFilter('keywords', '(Fachkräftesicherung UND "Fachkräfte finden und binden) ODER Innovationen');
        $this->fixture->setLengthBoostSearchLucene(TRUE);

        $this->assertSame(
            'SELECT test, keywords FROM ' .
            '(' .
                'SELECT *, keywords FROM DocumentAbstract ' .
                'WHERE [searchContent,searchContentFuzzy,searchContentType,searchContentSize] LUCENE "' .
                '(' .
                    'searchContent: ((Fachkräftesicherung AND Fachkräfte finden und binden) OR Innovationen)^2.5 ' .
                    'OR searchContentFuzzy: (f3473284764 OR f34732 f3626 u062 b1626 OR i063266)' .
                ') AND (searchContentType: (pdf) OR searchContentType:(default)) '.
                'AND (searchContentSize: (small) OR searchContentSize: (medium)^5 OR searchContentSize: (large)^10)' .
                '" ORDER BY keywords ASC LIMIT 110' .
            ') WHERE (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY keywords ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }


    /**
     * @test
     */
    public function getQueryWithSetFilterTypeReturnsExpectedQuery() {
        $this->fixture->setFilter('type', 'Blog-Beitrag ODER Praxisbeispiel');

        $this->assertSame(
            'SELECT test FROM DocumentAbstract ' .
            'WHERE (out(\'EdgeType\') contains (((name = "Blog-Beitrag" OR nameFuzzy = "b1541274")) OR ((name = "Praxisbeispiel" OR nameFuzzy = "p14881815"))) ) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY test ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    /**
     * @test
     */
    public function getQueryWithSetFilterDepartmentReturnsExpectedQuery() {
        $this->fixture->setFilter('department', 'Fachkräftesicherung ODER Innovationen');

        $this->assertSame(
            'SELECT test FROM DocumentAbstract ' .
            'WHERE (out(\'EdgeDepartment\') contains (((name = "Fachkräftesicherung" OR nameFuzzy = "f3473284764") OR (shortName = "Fachkräftesicherung" OR shortNameFuzzy = "f3473284764")) OR ((name = "Innovationen" OR nameFuzzy = "i063266") OR (shortName = "Innovationen" OR shortNameFuzzy = "i063266"))) ) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY test ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }


    /**
     * @test
     */
    public function getQueryWithSetFilterDateFromReturnsExpectedQuery() {
        $this->fixture->setFilter('dateFrom', '1.April 2012');

        $this->assertSame(
            'SELECT test FROM DocumentAbstract ' .
            'WHERE ((starttime >= 1333231200) AND (crdate >= 1333231200)) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY starttime ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    /**
     * @test
     */
    public function getQueryWithSetFilterDateToReturnsExpectedQuery() {
        $this->fixture->setFilter('dateTo', '1.April 2012');

        $this->assertSame(
            'SELECT test FROM DocumentAbstract ' .
            'WHERE ((starttime < 1333317600 AND starttime > 0) AND (crdate < 1333317600 AND crdate > 0)) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY starttime ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    /**
     * @test
     */
    public function getQueryWithSetFilterPublicationReturnsExpectedQuery() {
        $this->fixture->setFilter('publication', '');

        $this->assertSame(
            'SELECT test, pdfImport FROM DocumentAbstract ' .
            'WHERE (pdfImport = 1 OR pdfImportSub = 1) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) GROUP BY pdfImportParentUid ORDER BY title ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    /**
     * @test
     */
    public function getQueryWithSetFilterNewsReturnsExpectedQuery() {
        $this->fixture->setFilter('news', '');

        $this->assertSame(
            'SELECT test, pdfImportSub FROM DocumentAbstract ' .
            'WHERE (NOT (pdfImportSub = 1) AND pubdate > 0) ' .
            'AND (noSearch = 0 OR noSearch IS NULL) ' .
            'AND (doktype IN [0,1] OR doktype IS NULL) ' .
            'AND (hidden = 0 OR hidden IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY pubdate ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    /**
     * @test
     */
    public function getQueryWithSetFilterEventsReturnsExpectedQuery() {
        $this->fixture->setFilter('events', '');

        $this->assertSame(
            'SELECT test FROM DocumentEvents ' .
            'WHERE (hidden = 0 OR hidden IS NULL) ' .
            'AND (deleted = 0 OR deleted IS NULL) ' .
            'AND (sysLanguageUid = 0 OR sysLanguageUid IS NULL) ORDER BY title ASC LIMIT 11',
            $this->fixture->getQuery()->getRaw()
        );
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getRepositoryWithoutConfigurationThrowsException() {

        unset($this->configuration[0]['searchClass']);
        $this->fixture = new \RKW\RkwSearch\Search\QueryFactory(0, $this->configuration);
        $this->fixture->getRepository();
    }

    /**
     * @test
     */
    public function getRepositoryReturnsInstanceOfDocumentRepositoryInterface() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\DocumentRepositoryInterface', $this->fixture->getRepository());
    }


    //==========================================

    /**
     * @test
     */
    public function setOrientDbClassAndGetOrientDbClassReturnsExpectedValue() {

        $this->fixture->setOrientDbClass('test');
        $this->assertSame('test', $this->fixture->getOrientDbClass());
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getOrientDbClassWithNoConfigurationThrowsException() {

        unset($this->configuration[0]['searchClass']);
        $this->fixture = new \RKW\RkwSearch\Search\QueryFactory(0, $this->configuration);
        $this->fixture->getOrientDbClass();
    }

    /**
     * @test
     */
    public function getOrientDbClassReturnsExpectedClass () {

        $this->assertSame('DocumentAbstract', $this->fixture->getOrientDbClass());
    }



    //==========================================

    /**
     * @test
     */
    public function getSearchStringGivenNothingReturnsNull() {
        $this->assertNull($this->fixture->getSearchString());
    }

    /**
     * @test
     */
    public function setSearchStringAndGetSearchStringReturnsSetText() {
        $this->assertSame('Test', $this->fixture->setSearchString('Test')->getSearchString());
    }

    /**
     * @test
     */
    public function setSearchStringAndGetFiltersReturnsExpectedFilters() {
        $this->fixture->setSearchString('Test')->getSearchString();

        $result = array (
            'keywords' => Array (
                'configuration' => Array (
                    'fulltext' => Array (
                        'search' => 'Test',
                        'searchFuzzy' => 't282',
                        'searchField' => 'searchContent',
                        'searchFieldFuzzy' => 'searchContentFuzzy',
                        'searchFieldBoost' => 2.5,
                        'searchFieldType' => 'searchContentType',
                        'searchFieldSize' => 'searchContentSize',
                        'selectFields' => Array (
                            0 => 'keywords',
                        ),
                        'orderBy' => Array (
                            'keywords' => 'ASC',
                        ),
                    ),
                    'selectFields' => Array (
                        0 => 'keywords'
                    ),
                    'searchClass' => NULL,
                    'orderBy' => Array (
                        'keywords' => 'ASC'
                    )
                ),
                'value' => 'Test'
            )
        );

        $this->assertSame($result, $this->fixture->getFilters());
    }



    //==========================================

    /**
     * @test
     */
    public function getLanguageUidWithNullValueReturnsZero() {

        $this->fixture =  new \RKW\RkwSearch\Search\QueryFactory(0, $this->configuration);
        $this->assertSame(0, $this->fixture->getLanguageUid());
    }


    /**
     * @test
     */
    public function getLanguageUidWithValueOneReturnsOne() {

        $this->fixture =  new \RKW\RkwSearch\Search\QueryFactory(1, $this->configuration);
        $this->assertSame(1, $this->fixture->getLanguageUid());
    }



    //==========================================

    /**
     * @test
     */
    public function setCurrentPageAndGetCurrentPageWithNullValueReturnsZero() {

        $this->fixture->setCurrentPage(NULL);
        $this->assertSame(0, $this->fixture->getCurrentPage());
    }


    /**
     * @test
     */
    public function setCurrentPageAndGetCurrentPageWithValueOneReturnsOne() {

        $this->fixture->setCurrentPage(1);
        $this->assertSame(1, $this->fixture->getCurrentPage());
    }



    //==========================================

    /**
     * @test
     */
    public function getLimitReturnsDefaultValue() {

        $this->assertSame(11, $this->fixture->getLimit());
    }


    /**
     * @test
     */
    public function setLimitAndgetLimitReturnsSetValue() {

        $this->fixture->setLimit(55);
        $this->assertSame(56, $this->fixture->getLimit());
    }



    //==========================================

    /**
     * @test
     */
    public function getSkipWithSetCurrentPageOneReturnsZeroValue() {

        $this->fixture->setCurrentPage(1);
        $this->assertSame(0, $this->fixture->getSkip());
    }


    /**
     * @test
     */
    public function getSkipWithSetCurrentPageTwoReturnsSingleDefaultLimitValue() {

        $this->fixture->setCurrentPage(2);
        $this->assertSame(10, $this->fixture->getSkip());
    }


    /**
     * @test
     */
    public function getSkipWithSetCurrentPageThreeReturnsDoubleDefaultLimitValue() {

        $this->fixture->setCurrentPage(3);
        $this->assertSame(20, $this->fixture->getSkip());
    }


    //==========================================
    /**
     * @test
     */
    public function getOrderingReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getOrdering());
    }


    /**
     * @test
     */
    public function setOrderingGivenNonArrayReturnsEmptyArray() {

        $this->fixture->setOrdering('name ASC');
        $this->assertEmpty($this->fixture->getOrdering());
    }

    /**
     * @test
     */
    public function setOrderingAndGetOrderingReturnsExpectedArray() {

        $this->fixture->setOrdering(array('name' => 'ASC'));
        $this->assertSame(array('name' => 'ASC'), $this->fixture->getOrdering());
    }

    //==========================================
    /**
     * @test
     */
    public function getGroupingReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getGrouping());
    }


    /**
     * @test
     */
    public function setGroupingGivenNonArrayReturnsEmptyArray() {

        $this->fixture->setGrouping('name');
        $this->assertEmpty($this->fixture->getGrouping());
    }

    /**
     * @test
     */
    public function setGroupingAndGetGroupingReturnsExpectedArray() {

        $this->fixture->setGrouping(array('name'));
        $this->assertSame(array('name'), $this->fixture->getGrouping());
    }

    //==========================================
    /**
     * @test
     */
    public function getWhereReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getWhere());
    }

    /**
     * @test
     */
    public function setWhereAndGetWhereReturnsExpectedArray() {

        $this->fixture->setWhere('test = 1');
        $this->fixture->setWhere('test = 2');
        $this->assertSame(array('test = 1', 'test = 2'), $this->fixture->getWhere());
    }

} 