<?php
namespace RKW\RkwSearch\Tests\Search\Filters;

/**
 * Class DateFromTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DateFromTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\Filters\DateFrom
     */
    protected $fixture;


    /**
     * @var \RKW\RkwSearch\TreeTagger\Collection\Records
     */
    protected $dataObject;


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

            'searchFields' => array (
                'starttime',
                'crdate'
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

        );
        $queryFactoryConfiguration = array (
            0 => array (
                'selectFields' => 'test1',
                'searchClass' => 'DocumentAbstract',
            )
        );

        $this->queryFactory = new \RKW\RkwSearch\Search\QueryFactory(0, $queryFactoryConfiguration);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '01. April 2014', $this->configuration);
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
    public function getMonthValuesWithNoMappingConfigurationReturnsUnchangedString() {

        unset($this->configuration['monthMapping']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '01. April 2014', $this->configuration);
        $this->assertSame('01. April 2014', $this->fixture->getMonthValues('01. April 2014'));
    }


    /**
     * @test
     */
    public function getMonthValuesGivenUpperCamelCaseMonthReturnsExpectedValue() {

        $this->assertSame('01. 4. 2014', $this->fixture->getMonthValues('01. April 2014'));
    }

    /**
     * @test
     */
    public function getMonthValuesGivenLowerMonthReturnsExpectedValue() {

        $this->assertSame('01. 4. 2014', $this->fixture->getMonthValues('01. april 2014'));
    }

    /**
     * @test
     */
    public function getMonthValuesGivenNonMatchingMonthReturnsUnchangedString() {

        $this->assertSame('01. Oktember 2014', $this->fixture->getMonthValues('01. Oktember 2014'));
    }


    //==========================================


    /**
     * @test
     */
    public function getDataPreparedWithoutValidStringReturnsZeroTimestamp() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, 'Clemens stinkt', $this->configuration);
        $this->assertSame(0, $this->fixture->getDataPrepared());
    }


    /**
     * @test
     */
    public function getDataPreparedWithYearOnlyReturnsExpectedTimestamp() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '2012', $this->configuration);
        $this->assertSame(1325372400, $this->fixture->getDataPrepared());
    }


    /**
     * @test
     */
    public function getDataPreparedWithMonthAndYearReturnsExpectedTimestamp() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '4. 2012', $this->configuration);
        $this->assertSame(1333231200, $this->fixture->getDataPrepared());
    }


    /**
     * @test
     */
    public function getDataPreparedWithMonthWordAndYearReturnsExpectedTimestamp() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, 'April 2012', $this->configuration);
        $this->assertSame(1333231200, $this->fixture->getDataPrepared());
    }

    /**
     * @test
     */
    public function getDataPreparedWithDayAndMonthAndYearReturnsExpectedTimestamp() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '25. 4. 2012', $this->configuration);
        $this->assertSame(1335304800, $this->fixture->getDataPrepared());

    }

    /**
     * @test
     */
    public function getDataPreparedWithDayAndMonthWordAndYearReturnsExpectedTimestamp() {


        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '25. April 2012', $this->configuration);
        $this->assertSame(1335304800, $this->fixture->getDataPrepared());

    }
    //==========================================


    /**
     * @test
     */
    public function getFilterWithoutConfigurationReturnsEmptyArray() {

        unset($this->configuration['searchFields']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateFrom($this->queryFactory, '25. April 2012', $this->configuration);

        $this->assertEmpty($this->fixture->getFilter());
    }



    /**
     * @test
     */
    public function getFilterWithReturnsExpectedArray() {

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'orderBy' => Array (
                'test' => 'ASC'
            ),
            'where' => '((starttime >= 1396303200) AND (crdate >= 1396303200))'
        );

        $this->assertSame($result, $this->fixture->getFilter());

    }
} 