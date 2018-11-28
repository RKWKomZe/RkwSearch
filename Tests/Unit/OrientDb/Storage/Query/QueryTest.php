<?php
namespace RKW\RkwSearch\Tests\OrientDb\Storage\Query;

/**
 * Class QueryTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class QueryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Query
     */
    protected $fixture;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Storage\Query\Query();
    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function updateEdgeReturnsInstanceOfOfEdgeCreate() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\Update', $this->fixture->updateEdge('Test'));
    }



    /**
     * @test
     */
    public function createEdgeReturnsInstanceOfOfEdgeCreate() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\Create', $this->fixture->createEdge('Test'));
    }

    /**
     * @test
     */
    public function deleteEdgeAllReturnsInstanceOfOfDeleteAll() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\DeleteAll', $this->fixture->deleteEdgeAll('Test'));
    }

    /**
     * @test
     */
    public function selectInEdgeReturnsInstanceOfOfSelectInEdge() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\SelectIn', $this->fixture->selectInEdge('Test'));
    }

    /**
     * @test
     */
    public function selectOutEdgeReturnsInstanceOfOfSelectOutEdge() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Edge\\SelectOut', $this->fixture->selectOutEdge('Test'));
    }


    /**
     * @test
     */
    public function createVertexReturnsInstanceOfOfVertexCreate() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Create', $this->fixture->createVertex('Test'));
    }

    /**
     * @test
     */
    public function updateVertexReturnsInstanceOfVertexUpdate() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Update', $this->fixture->updateVertex('Test'));
    }

    /**
     * @test
     */
    public function updateVertexSingleReturnsInstanceOfVertexUpdateSingle() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\UpdateSingle', $this->fixture->updateVertexSingle('Test'));
    }


    /**
     * @test
     */
    public function deleteVertexReturnsInstanceOfVertexDelete() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Delete', $this->fixture->deleteVertex('Test'));
    }

    /**
     * @test
     */
    public function deleteVertexSingleReturnsInstanceOfVertexDeleteSingle() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\DeleteSingle', $this->fixture->deleteVertexSingle('Test'));
    }


    /**
     * @test
     */
    public function selectSingleReturnsInstanceOfSelectSingle() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\SelectSingle', $this->fixture->selectSingle('Test'));
    }


    /**
     * @test
     */
    public function fromQueryReturnsInstanceOfSelect() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Select', $this->fixture->fromQuery($this->fixture));
    }


    /**
     * @test
     */
    public function groupByReturnsInstanceOfSelect() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Query\\Command\\Vertex\\Select', $this->fixture->groupBy(array('@rid', 'test')));
    }


    /**
     * @test
     */
    public function getRawReplacesGermanEszett() {

        $this->fixture->select(array('*'));
        $this->fixture->from(array('test'));
        $this->fixture->where('name = ?', 'Fußball');

        $this->assertSame('SELECT * FROM test WHERE name = "Fussball"', $this->fixture->getRaw());
    }


} 