<?php
namespace RKW\RkwSearch\Tests\Search\Filters;

/**
 * Class DepartmentTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DepartmentTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\Filters\Department
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

        );

        $queryFactoryConfiguration = array (
            0 => array (
                'selectFields' => 'test1',
                'searchClass' => 'DocumentAbstract',
            )
        );

        $this->dataString = 'Fachkräftesicherung ODER Innovation';
        $this->queryFactory = new \RKW\RkwSearch\Search\QueryFactory(0, $queryFactoryConfiguration);

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, $this->dataString, $this->configuration);
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

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this, $this->dataString, $this->configuration);
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function constructInstanceGivenWrongDataTypeThrowsException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, array('test'), $this->configuration);
    }


    /**
     * @test
     */
    public function constructInstanceGivenEmptyStringThrowsNoException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, '', $this->configuration);
    }

    //==========================================

    /**
     * @test
     */
    public function getFilterWithoutConfigurationReturnsEmptyArray() {

        unset($this->configuration['searchField']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, $this->dataString, $this->configuration);

        $this->assertEmpty($this->fixture->getFilter());
    }


    /**
     * @test
     */
    public function getFilterWithMultipleDepartmentsReturnsExpectedArray() {

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeDepartment\') contains (((name = "Fachkräftesicherung" OR nameFuzzy = "f3473284764") OR (shortName = "Fachkräftesicherung" OR shortNameFuzzy = "f3473284764")) OR ((name = "Innovation" OR nameFuzzy = "i06326") OR (shortName = "Innovation" OR shortNameFuzzy = "i06326"))) )',
            'orderBy' => Array (
                'test' => 'ASC'
            )

        );
        $this->assertSame($result, $this->fixture->getFilter());
    }

    /**
     * @test
     */
    public function getFilterWithOneDepartmentReturnsExpectedArray() {

        $this->dataString = '(Fachkräftesicherung';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, $this->dataString, $this->configuration);

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeDepartment\') contains (((name = "Fachkräftesicherung" OR nameFuzzy = "f3473284764") OR (shortName = "Fachkräftesicherung" OR shortNameFuzzy = "f3473284764"))) )',
            'orderBy' => Array (
                'test' => 'ASC'
            )

        );
        $this->assertSame($result, $this->fixture->getFilter());
    }


    /**
     * @test
     */
    public function getFilterWithOneDepartmentWithoutFuzzyFieldsReturnsExpectedArray() {

        unset($this->configuration['searchFieldFuzzy']);
        unset($this->configuration['searchFieldTwoFuzzy']);

        $this->dataString = '(Fachkräftesicherung';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, $this->dataString, $this->configuration);

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeDepartment\') contains (((name = "Fachkräftesicherung") OR (shortName = "Fachkräftesicherung"))) )',
            'orderBy' => Array (
                'test' => 'ASC'
            )

        );
        $this->assertSame($result, $this->fixture->getFilter());
    }

    /**
     * @test
     */
    public function getFilterWithOneDepartmentOnlyOneFieldReturnsExpectedArray() {

        unset($this->configuration['searchFieldTwo']);

        $this->dataString = '(Fachkräftesicherung';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, $this->dataString, $this->configuration);

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeDepartment\') contains (((name = "Fachkräftesicherung" OR nameFuzzy = "f3473284764"))) )',
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
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Department($this->queryFactory, $this->dataString, $this->configuration);

        $result = array(
            'stringLucene' => 'Fachkräftesicherung ODER Innovation',
            'wordsArray' => array (
                0 => 'Fachkräftesicherung ODER Innovation'
            ),
            'wordsArrayFuzzy' => array (
                0 => 'f3473284764 o027 i06326'
            )
        );

        $this->assertSame($result, $this->fixture->getDataPrepared());
    }

    /**
     * @test
     */
    public function getDataPreparedReturnsExpectedArray() {

        $result = array (
            'stringLucene' => 'Fachkräftesicherung OR Innovation',
            'wordsArray' => array (
                0 => 'Fachkräftesicherung',
                1 => 'Innovation'
            ),
            'wordsArrayFuzzy' => array (
                0 => 'f3473284764',
                1 => 'i06326',
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