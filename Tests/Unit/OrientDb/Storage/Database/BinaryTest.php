<?php
namespace RKW\RkwSearch\Tests\OrientDb\Storage\Database;

/**
 * Class DatabaseLoaderTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
use \RKW\RkwSearch\OrientDb\Storage\Query\Query;

class BinaryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Database\Binary
     */
    protected $fixture;


    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Query
     */
    protected $query;



    /**
     * Set up fixture
     */
    public function setUp() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Storage\Database\Binary();
    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {
        unset($this->fixture);
        $repository = new \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentPagesRepository();
        $repository->setDebugMode(TRUE);
        $repository->removeAll();

    }

    /**
     * @test
     */
    public function isValidRidGivenValidRidReturnsTrue() {

        $this->assertTrue($this->fixture->isValidRid('#1:100000000000'));
    }


    /**
     * @test
     */
    public function isValidRidGivenInvalidRidReturnsFalse() {

        $this->assertFalse($this->fixture->isValidRid('1:100000000000'));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function executeRawGivenInvalidDataThrowsException() {

        $this->assertNull($this->fixture->executeRaw(array('test')));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\StorageException
     */
    public function executeRawGivenInvalidQueryReturnsThrowsStorageException() {

        $query = new Query();
        $query->select(array('*'));
        $query->from(array('test'));
        $query->andWhere('debug = ?', 1);

        $this->assertInternalType('array',$this->fixture->executeRaw($query));
    }

    /**
     * @test
     */
    public function executeRawGivenQueryInstanceOnNonExistentReturnsFalse() {

              $query = new Query();
        $query->select(array('*'));
        $query->from(array('DocumentPages'));
        $query->andWhere('debug = ?', 1);

        $this->assertFalse($this->fixture->executeRaw($query));
    }

    /**
     * @test
     */
    public function executeRawGivenQueryStringOnNonExistentReturnsFalse() {

        $query = new Query();
        $query->select(array('*'));
        $query->from(array('DocumentPages'));
        $query->andWhere('debug = ?', 1);

        $this->assertFalse($this->fixture->executeRaw($query->getRaw()));
    }

    /**
     * @test
     */
    public function executeRawGivenQueryInstanceOnExistentReturnsArray() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $this->fixture->insert($query);

        $query = new Query();
        $query->select(array('*'));
        $query->from(array('DocumentPages'));
        $query->andWhere('debug = ?', 1);

        $this->assertInternalType('array',$this->fixture->executeRaw($query));
    }

    /**
     * @test
     */
    public function executeRawGivenQueryStringOnExistentReturnsArray() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $this->fixture->insert($query);

        $query = new Query();
        $query->select(array('*'));
        $query->from(array('DocumentPages'));
        $query->andWhere('debug = ?', 1);

        $this->assertInternalType('array',$this->fixture->executeRaw($query->getRaw()));
    }


    /**
     * @test
     */
    public function executeWithSelectReturnsInstanceOfCollectionDocument() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
             ));

        $this->fixture->insert($query);

        $query = new Query();
        $query->select(array('*'));
        $query->from(array('DocumentPages'));
        $query->andWhere('debug = ?', 1);

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->execute($query));
    }


    /**
     * @test
     */
    public function executeWithSelectSingleOnExistingReturnsInstanceOfDocumentPages() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $rid = $this->fixture->insert($query);

        $query = new Query();
        $query->selectSingle($rid);
        //$query->from(array('DocumentPages'));
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages', $this->fixture->execute($query));
    }


    /**
     * @test
     */
    public function executeWithSelectSingleOnNonExistingReturnsNull() {

        $query = new Query();
        $query->selectSingle('#1:100000000000');
        $this->assertNull($this->fixture->execute($query));
    }



    /**
     * @test
     */
    public function executeInvalidQueryReturnsInstanceOfCollectionDocument() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $this->fixture->insert($query);

        $query = new Query();
        $query->select(array('*'));
        $query->from(array('DocumentPages'));

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->execute($query));
    }

    /**
     * @test
     */
    public function insertGivenValidQueryReturnsString() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $this->assertInternalType('string', $this->fixture->insert($query));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\StorageException
     */
    public function insertGivenInvalidQueryThrowsStorageException() {

        $query = new Query();
        $query->createVertex('test')
            ->set(array ('debug' =>  1));

        $this->fixture->insert($query);
    }


    /**
     * @test
     */
    public function updateGivenValidQueryReturnsOne() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $rid = $this->fixture->insert($query);

        $query = new Query();
        $query->updateVertex('DocumentPages')
            ->set(array (
                'tstamp' => time(),
                'debug' =>  1
            ))->where ('@rid = ?', $rid);

        $this->assertSame(1, $this->fixture->update($query));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\StorageException
     */
    public function updateGivenInvalidQueryThrowsStorageException() {

        $query = new Query();
        $query->updateVertex('test')
            ->set(array (
                'tstamp' => time(),
                'debug' =>  1
            ));

        $this->fixture->update($query);
    }



    /**
     * @test
     */
    public function deleteGivenValidQueryReturnsOne() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $rid = $this->fixture->insert($query);

        $query = new Query();
        $query->deleteVertex('DocumentPages')
            ->where ('@rid = ?', $rid);

        $this->assertSame(1, $this->fixture->delete($query));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\StorageException
     */
    public function deleteGivenInvalidQueryThrowsStorageException() {

        $query = new Query();
        $query->deleteVertex('test');

        $this->fixture->delete($query);
    }


    /**
     * @test
     */
    public function countGivenValidQueryReturnsTwo() {

        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  TRUE
            ));

        $this->fixture->insert($query);
        $this->fixture->insert($query);

        $query = new Query();
        $query->select(array('count(@rid)'));
        $query->from(array('DocumentPages'));
        $query->where ('debug = ?', TRUE);

        $this->assertSame(2, $this->fixture->count($query));
    }

    /**
     * @test
     */
    public function countGivenValidQueryWithNoMatchReturnsZero() {

        $query = new Query();
        $query->select(array('count(@rid)'));
        $query->from(array('DocumentPages'));
        $query->where ('debug = ?', 1);

        $this->assertSame(0, $this->fixture->count($query));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\StorageException
     */
    public function countGivenInvalidQueryThrowsStorageException() {

        $query = new Query();
        $query->select(array('count(@rid)'));
        $query->from(array('test'));

        $this->fixture->count($query);
    }


    /**
     * @test
     */
    public function createEdgeGivenValidQueryWithNoMatchReturnsOne() {


        $query = new Query();
        $query->createVertex('DocumentPages')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));

        $query->createVertex('DocumentAuthors')
            ->set(array (
                'uid' => 1,
                'tstamp' => time(),
                'debug' =>  1
            ));
        $rid = $this->fixture->insert($query);
        $ridTwo = $this->fixture->insert($query);

        $query = new Query;
        $query->createEdge('EdgeAuthor')
            ->fromVertex($rid)
            ->toVertex($ridTwo)
            ->set(array(
                'tstamp' => time(),
                'debug' => 1
            ));

        $this->assertSame(1, $this->fixture->createEdge($query));
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\StorageException
     */
    public function createEdgeGivenInvalidQueryThrowsStorageException() {

        $query = new Query;
        $query->createEdge('EdgeAuthor')
            ->fromVertex('#1:100000000000')
            ->toVertex('#1:100000000001')
            ->set(array(
                'tstamp' => time(),
                'debug' => 1
             ));

        $this->fixture->createEdge($query);
    }




} 