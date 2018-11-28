<?php
namespace RKW\RkwSearch\Tests\Search\Filters;

/**
 * Class GeoLocationTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class GeoLocationTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\Filters\GeoLocation
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

            'searchClass' => 'DocumentConsultants',
			'orderBy' => array (
                'distance' => 'ASC',
            ),
			'searchFieldLatitude' => 'latitude',
            'searchFieldLongitude' =>'longitude'
        );

        $queryFactoryConfiguration = array (
            0 => array (
                'selectFields' => 'test1',
                'searchClass' => 'DocumentAbstract',
            )
        );

        $this->queryFactory = new \RKW\RkwSearch\Search\QueryFactory(0, $queryFactoryConfiguration);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\GeoLocation($this->queryFactory, 'Graf-von-Stauffenberg Straße 27, 35037 Marburg', $this->configuration);
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

        unset($this->configuration['searchClass']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\GeoLocation($this->queryFactory, '35037', $this->configuration);
        $this->assertEmpty($this->fixture->getFilter());
    }

    /**
     * @test
     */
    public function getFilterWithoutValidDataReturnsEmptyArray() {

        $result = array ('searchClass' => 'DocumentConsultants');
        $this->fixture = new \RKW\RkwSearch\Search\Filters\GeoLocation($this->queryFactory, 'Fachkräftesicherung', $this->configuration);
        $this->assertSame($result, $this->fixture->getFilter());
    }


    /**
     * @test
     */
    public function getFilterReturnsExpectedArray() {

        $result = array (
            'selectFields' => array (
                0 => 'distance(latitude, longitude, 50.793317, 8.7431324) AS distance'
            ),
            'searchClass' => 'DocumentConsultants',
            'where' => '(latitude > 0 AND longitude > 0)',
            'orderBy' => array (
                'distance' => 'ASC'
            )
        );

        $this->assertSame($result, $this->fixture->getFilter());
    }


    //==================

    /**
     * @test
     */
    public function getDataPreparedGivenAnAddressReturnsExpectedArray() {

        $result = array (
            'longitude' => 8.7431324,
            'latitude' => 50.793317,
        );

        $this->assertSame($result, $this->fixture->getDataPrepared());
    }

    /**
     * @test
     */
    public function getDataPreparedGivenAZipReturnsExpectedArray() {

        $result = array (
            'longitude' => 8.7431324,
            'latitude' => 50.793317,
        );

        $this->fixture = new \RKW\RkwSearch\Search\Filters\GeoLocation($this->queryFactory, '35037', $this->configuration);
        $this->assertSame($result, $this->fixture->getDataPrepared());
    }

    /**
     * @test
     */
    public function getDataPreparedGivenInvalidDataReturnsEmptyArray() {


        $this->fixture = new \RKW\RkwSearch\Search\Filters\GeoLocation($this->queryFactory, 'Fachkräftesicherung', $this->configuration);
        $this->assertEmpty($this->fixture->getDataPrepared());
    }

} 