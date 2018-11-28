<?php
namespace RKW\RkwSearch\Tests\Search\Filters;

/**
 * Class DateToTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DateToTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\Filters\DateTo
     */
    protected $fixture;

    /**
     * @var array
     */
    protected $configuration;


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
        $this->fixture = new \RKW\RkwSearch\Search\Filters\DateTo($this->queryFactory, '01. April 2014', $this->configuration);
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
            'where' => '((starttime < 1396389600 AND starttime > 0) AND (crdate < 1396389600 AND crdate > 0))'
        );

        $this->assertSame($result, $this->fixture->getFilter());

    }

}