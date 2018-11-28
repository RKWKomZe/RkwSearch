<?php
namespace RKW\RkwSearch\Tests\Search\Filters;

/**
 * Class AuthorTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class AuthorTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Search\Filters\Author
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

        );

        $queryFactoryConfiguration = array (
            0 => array (
                'selectFields' => 'test1',
                'searchClass' => 'DocumentAbstract',
            )
        );

        $this->dataString = '(Dr. Alexander Blaeser-Benfer UND Clemens Queißner) ODER Dr. Noemí Fernández Sánchez';
        $this->queryFactory = new \RKW\RkwSearch\Search\QueryFactory(0, $queryFactoryConfiguration);

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, $this->dataString, $this->configuration);
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

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this, $this->dataString, $this->configuration);
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function constructInstanceGivenWrongDataTypeThrowsException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, array('test'), $this->configuration);
    }


    /**
     * @test
     */
    public function constructInstanceGivenEmptyStringThrowsNoException() {

        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, '', $this->configuration);
    }

    //==========================================

    /**
     * @test
     */
    public function getFilterWithoutConfigurationReturnsEmptyArray() {

        unset($this->configuration['searchField']);
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, $this->dataString, $this->configuration);

        $this->assertEmpty($this->fixture->getFilter());
    }


    /**
     * @test
     */
    public function getFilterWithMultiplePersonsReturnsExpectedArray() {

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeAuthor\') contains (((firstname = "Alexander") AND (lastname = "Blaeser-Benfer")) OR ((firstname = "Clemens") AND (lastname = "Queißner")) OR ((firstname = "Noemí") AND (lastname = "Fernández") OR (lastname = "Fernández Sánchez"))))',
            'orderBy' => Array (
                'test' => 'ASC'
            )

        );
        $this->assertSame($result, $this->fixture->getFilter());
    }

    /**
     * @test
     */
    public function getFilterWithOnePersonReturnsExpectedArray() {

        $this->dataString = '(Dr. Alexander Blaeser-Benfer';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, $this->dataString, $this->configuration);

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeAuthor\') contains (((firstname = "Alexander") AND (lastname = "Blaeser-Benfer"))))',
            'orderBy' => Array (
                'test' => 'ASC'
            )

        );
        $this->assertSame($result, $this->fixture->getFilter());
    }



    /**
     * @test
     */
    public function getFilterWithOnePersonOnlyFirstnameReturnsExpectedArray() {

        $this->dataString = 'Dr. Alexander';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, $this->dataString, $this->configuration);

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeAuthor\') contains ((lastname = "Alexander")))',
            'orderBy' => Array (
                'test' => 'ASC'
            )

        );
        $this->assertSame($result, $this->fixture->getFilter());
    }

    /**
     * @test
     */
    public function getFilterWithOnePersonOnlyLastnameReturnsExpectedArray() {

        $this->dataString = 'Dr. Blaeser-Benfer';
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, $this->dataString, $this->configuration);

        $result = array (
            'selectFields' => Array (
                0 => 'test'
            ),
            'searchClass' => NULL,
            'where' => '(out(\'EdgeAuthor\') contains ((lastname = "Blaeser-Benfer")))',
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
        $this->fixture = new \RKW\RkwSearch\Search\Filters\Author($this->queryFactory, $this->dataString, $this->configuration);

        $result = array(
            'stringLucene' => '(Dr. Alexander Blaeser-Benfer UND Clemens Queißner) ODER Dr. Noemí Fernández Sánchez',
            'wordsArray' => Array (
                0 => 'Dr. Alexander Blaeser-Benfer UND Clemens Queißner ODER Dr. Noemí Fernández Sánchez',
            ),
            'wordsArrayFuzzy' => array (
                0 => 'd27 a0548627 b15871637 u062 c85668 q4867 o027 d27 n66 f376628 s8648'
            )
        );

        $this->assertSame($result, $this->fixture->getDataPrepared());
    }

    /**
     * @test
     */
    public function getDataPreparedReturnsExpectedArray() {

        $result = array (
            'stringLucene' => '(Dr. Alexander Blaeser-Benfer OR Clemens Queißner) OR Dr. Noemí Fernández Sánchez',
            'wordsArray' => array (
                0 => 'Dr. Alexander Blaeser-Benfer',
                1 => 'Clemens Queißner',
                2 => 'Dr. Noemí Fernández Sánchez'
            ),
            'wordsArrayFuzzy' => array (
                0 => 'd27 a0548627 b15871637',
                1 => 'c85668 q4867',
                2 => 'd27 n66 f376628 s8648'
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