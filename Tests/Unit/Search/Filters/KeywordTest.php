<?php
namespace RKW\RkwSearch\Tests\Search\Filters;

/**
 * Class KeywordTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class KeywordTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\Filters\Keywords
     */
    protected $fixture;


    /**
     * @var string
     */
    protected $dataString;


    /**
     * @var array
     */
    protected $configuration;


    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Query
     */
    protected $query;


    /**
     * @var \RKW\RkwSearch\Search\QueryFactory
     */
    protected $queryFactory;



    /**
     * Set up fixture
     */
    public function setUp() {

        $this->configuration = array (

            'selectFieldsAddition' => 'test',
            'orderBy' => array (
                'test' => 'ASC'
            ),

            'searchField' => 'searchContentKeywords',
            'searchFieldFuzzy' => 'searchContentKeywordsFuzzy',

            'searchFieldType' => 'searchContentType',
            'searchFieldSize' => 'searchContentSize',

            'conjunctionMapping' => array (
                'ODER' => 'OR',
                'UND' => 'AND',
            ),

        );

        $queryFactoryConfiguration = array (
            0 => array (
                'selectFields' => 'test1',
                'searchClass' => 'DocumentAbstract',
            )
        );

        $this->dataString = '(Fachkräfte finden und binden UND Gründung) ODER Innovation';

        $this->queryFactory = new \RKW\RkwSearch\Search\QueryFactory(0, $queryFactoryConfiguration);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this->queryFactory, $this->dataString, $this->configuration);
    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {
        unset($this->fixture);
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function constructInstanceGivenWrongInstanceTypeAsQueryFactoryThrowsException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this, $this->dataString, $this->configuration);
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function constructInstanceGivenWrongDataTypeThrowsException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this->queryFactory, array('test'), $this->configuration);
    }


    /**
     * @test
     */
    public function constructInstanceGivenEmptyStringThrowsNoException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this->queryFactory, '', $this->configuration);
    }

    //==========================================

    /**
     * @test
     */
    public function getFilterWithoutConfigurationReturnsEmptyArray() {

        unset($this->configuration['searchField']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this->queryFactory, $this->dataString, $this->configuration);

        $this->assertEmpty($this->fixture->getFilter());
    }


    /**
     * @test
     */
    public function getFilterReturnsExpectedArray() {

        $result = array (
            'fulltext' => array (
                'search' => '(Fachkräfte finden und binden AND Gründung) OR Innovation',
                'searchFuzzy' => 'f34732 f3626 u062 b1626 OR g476264 OR i06326',
                'searchField' => 'searchContentKeywords',
                'searchFieldFuzzy' => 'searchContentKeywordsFuzzy',
                'searchFieldBoost' => 1,
                'searchFieldType' => 'searchContentType',
                'searchFieldSize' => 'searchContentSize',
                'selectFields' => array (
                    0 => 'test'
                ),
                'orderBy' => array (
                    'test' => 'ASC'
                )
            ),
            'selectFields' => array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'orderBy' => Array (
                'test' => 'ASC'
            )
        );
        $this->assertSame($result, $this->fixture->getFilter());
    }


    //==========================================


    /**
     * @test
     */
    public function getDataPreparedReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getDataPrepared());
    }


    /**
     * @test
     */
    public function getDataPreparedWithoutConfigurationReturnsExpectedArray() {

        unset($this->configuration['conjunctionMapping']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this->queryFactory, $this->dataString, $this->configuration);

        $result = array(
            'stringLucene' => '(Fachkräfte finden und binden UND Gründung) ODER Innovation',
            'wordsArray' => array (
                0 => 'Fachkräfte finden und binden UND Gründung ODER Innovation'
            ),
            'wordsArrayFuzzy' => array (
                0 => 'f34732 f3626 u062 b1626 u062 g476264 o027 i06326'
            )
        );

        $this->assertSame($result, $this->fixture->getDataPrepared());
    }

    /**
     * @test
     */
    public function getDataPreparedWithQuotationMarksReturnsExpectedArrayWithoutFuzzySearch() {

        $this->dataString = '("Fachkräfte finden und binden" UND Gründung) ODER Innovation';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Keywords($this->queryFactory, $this->dataString, $this->configuration);

        $result = array(
            'stringLucene' => '("Fachkräfte finden und binden" AND Gründung) OR Innovation',
            'wordsArray' =>array (
                0 => 'Fachkräfte finden und binden',
                1 => 'Gründung',
                2 => 'Innovation',
            ),
        );

        $this->assertSame($result, $this->fixture->getDataPrepared());
    }


    /**
     * @test
     */
    public function getDataPreparedReturnsExpectedArray() {

        $result = array (
            'stringLucene' => '(Fachkräfte finden und binden AND Gründung) OR Innovation',
            'wordsArray' =>array (
                0 => 'Fachkräfte finden und binden',
                1 => 'Gründung',
                2 => 'Innovation',
            ),
            'wordsArrayFuzzy' => array (
                0 => 'f34732 f3626 u062 b1626',
                1 => 'g476264',
                2 => 'i06326',
            )
        );

        $this->assertSame($result, $this->fixture->getDataPrepared());
    }


    //==========================================

    /**
     * @test
     */
    public function getDataReturnsString() {

        $this->assertInternalType('string', $this->fixture->getData());
    }


    //==========================================

    /**
     * @test
     */
    public function getLanguageUidReturnsInteger() {

        $this->assertInternalType('integer', $this->fixture->getLanguageUid());
    }

    //==========================================

    /**
     * @test
     */
    public function getQueryFactoryReturnsInstanceOfQueryFactory() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Search\\QueryFactory', $this->fixture->getQueryFactory());
    }





} 