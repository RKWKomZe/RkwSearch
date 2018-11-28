<?php
namespace RKW\RkwSearch\Tests\OrientDb\Collection;
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('rkw_search') . 'Classes/Libs/OrientDB-PHP/OrientDB/OrientDB.php');

/**
 * Class DocumentBinaryTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DocumentBinaryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Collection\Document
     */
    protected $fixture;


    /**
     * @var array
     */
    protected $data;

    /**
     * Set up fixture
     */
    public function setUp() {

        $newObject = new \OrientDBRecord();
        $newObject->type = 'd';
        $newObject->clusterID = 11;
        $newObject->recordPos = 1151;
        $newObject->content = 'DocumentPages@content:"bla",uid:12,tstamp:1399983525';
        $this->data[] = $newObject;

        $newObject2 = new \OrientDBRecord();
        $newObject2->type = 'd';
        $newObject2->clusterID = 11;
        $newObject2->recordPos = 1161;
        $newObject2->content = 'DocumentPages@content:"bla",uid:14,tstamp:1400165182';
        $this->data[] = $newObject2;

        $this->fixture = new \RKW\RkwSearch\OrientDb\Collection\Document($this->data);
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
    public function currentReturnsInstanceOfDocumentPages() {
        $this->assertInstanceOf ('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages', $this->fixture->current());
    }


    /**
     * @test
     */
    public function firstReturnsInstanceOfDocumentPages() {
        $this->assertInstanceOf ('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages', $this->fixture->first());
    }


    /**
     * @test
     */
    public function rewindAndKeyReturnZero() {

        $this->fixture->rewind();
        $this->assertSame (0, $this->fixture->key());
    }



    /**
     * @test
     */
    public function countReturnsExpectedValue() {
        $this->assertSame (2, $this->fixture->count());
    }


    /**
     * @test
     */
    public function getFirstAndGetUidReturnsInitialValue() {

        $model = $this->fixture->first();
        $this->assertSame (12, $model->getUid());

    }

    /**
     * @test
     */
    public function getFirstAndGetRidReturnsInitialValue() {

        $model = $this->fixture->first();
        $this->assertSame ('#11:1151', $model->getRid());

    }


    /**
     * @test
     */
    public function getFirstAndGetClassReturnsInitialValue() {

        $model = $this->fixture->first();
        $this->assertSame ('DocumentPages', $model->getClass());

    }

    /**
     * @test
     */
    public function getDataByKeyGivenObjectReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getDataByKey('tstamp'));

    }


    /**
     * @test
     */
    public function getDataByKeyGivenInvalidKeyWithObjectReturnsEmptyArray() {

        $this->assertInternalType('array', $this->fixture->getDataByKey('tstamp'));
        $this->assertEmpty($this->fixture->getDataByKey('test'));

    }

    /**
     * @test
     */
    public function getDataByKeyGivenObjectReturnsExpectedArray() {

        $result = array (
            1399983525,
            1400165182
        );
        $this->assertSame ($result, $this->fixture->getDataByKey('tstamp'));

    }


    /**
     * @test
     */
    public function getDataByKeyGivenArrayReturnsArray() {

        $this->data = array (
            array('name' => 'First'),
            array('name' => 'Second'),
            array('name' => 'Third'),
        );

        $this->fixture = new \RKW\RkwSearch\Collection\AnalysedKeywords($this->data);
        $this->assertInternalType('array', $this->fixture->getDataByKey('name'));

    }


    /**
     * @test
     */
    public function getDataByKeyGivenInvalidKeyWithArrayReturnsEmptyArray() {

        $this->data = array (
            array('name' => 'First'),
            array('name' => 'Second'),
            array('name' => 'Third'),
        );

        $this->fixture = new \RKW\RkwSearch\Collection\AnalysedKeywords($this->data);
        $this->assertEmpty($this->fixture->getDataByKey('test'));

    }
    /**
     * @test
     */
    public function getDataByKeyGivenArrayReturnsExpectedArray() {

        $this->data = array (
            array('name' => 'First'),
            array('name' => 'Second'),
            array('name' => 'Third'),
        );

        $this->fixture = new \RKW\RkwSearch\Collection\AnalysedKeywords($this->data);
        $result = array (
            'First',
            'Second',
            'Third'
        );
        $this->assertSame ($result, $this->fixture->getDataByKey('name'));

    }

    /**
     * @test
     */
    public function getDataByKeyGivenArrayWithToleranceOneReturnsExpectedArray() {

        $this->data = array (
                array('name' => 'First'),
                array('name' => 'Firste'),
                array('name' => 'Second'),
        );

        $this->fixture = new \RKW\RkwSearch\Collection\AnalysedKeywords($this->data);
        $result = array (
                'First',
                'Second'
        );
        $this->assertSame ($result, $this->fixture->getDataByKey('name', 1));

    }

    /**
     * @test
     */
    public function getDataByKeyGivenArrayWithToleranceOneAndTakeLongerTrueReturnsExpectedArray() {

        $this->data = array (
            array('name' => 'First'),
            array('name' => 'Firste'),
            array('name' => 'Second'),
        );

        $this->fixture = new \RKW\RkwSearch\Collection\AnalysedKeywords($this->data);
        $result = array (
            'Firste',
            'Second'
        );
        $this->assertSame ($result, $this->fixture->getDataByKey('name', 1, TRUE));

    }

} 