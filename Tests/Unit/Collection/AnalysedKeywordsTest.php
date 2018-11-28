<?php
namespace RKW\RkwSearch\Tests\Collection;

/**
 * Class AnalyseTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class AnalysedKeywordsTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Collection\AnalysedKeywords
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\Collection\AnalysedKeywords
     */
    protected $fixture2;

    /**
     * @var array
     */
    protected $data;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->data = array (
            'first' => 'First',
            'second' => 'Second',
            'third' => 'Third'
        );

        $this->fixture = new \RKW\RkwSearch\Collection\AnalysedKeywords($this->data);
        $this->fixture2 = new \RKW\RkwSearch\Collection\AnalysedKeywords();

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
    public function nextAndCurrentReturnsSecondElement() {

        $this->fixture->next();
        $this->assertEquals($this->data['second'], $this->fixture->current());
    }


    /**
     * @test
     */
    public function nextAndRewindAndCurrentReturnsFirstElement() {

        $this->fixture->next();
        $this->fixture->rewind();
        $this->assertEquals($this->data['first'], $this->fixture->current());
    }


    /**
     * @test
     */
    public function nextAndFirstReturnsFirstElement() {

        $this->fixture->rewind();
        $this->fixture->next();
        $this->assertEquals($this->data['first'], $this->fixture->first());
    }


    /**
     * @test
     */
    public function nextAndPrevAndCurrentReturnsFirstElement() {

        $this->fixture->rewind();
        $this->fixture->next();
        $this->fixture->prev();
        $this->assertEquals($this->data['first'], $this->fixture->current());
    }


    /**
     * @test
     */
    public function nextAndKeyReturnExpectedKeyValue() {

        $this->fixture->rewind();
        $this->fixture->next();
        $this->assertEquals('second', $this->fixture->key());
    }


    /**
     * @test
     */
    public function serializeAndUnserializeReturnsExpectedKeyValue() {

        $serialized = $this->fixture->serialize();
        $this->fixture2->unserialize($serialized);
        $this->assertEquals('first', $this->fixture2->key());
    }


    /**
     * @test
     */
    public function countReturnsExpectedValue() {

        $this->assertEquals(count($this->data), $this->fixture->count());
    }



    /**
     * @test
     */
    public function rewindAndValidReturnTrue () {

        $this->fixture->rewind();
        $this->assertTrue($this->fixture->valid());
    }


    /**
     * @test
     */
    public function getDataReturnsExpectedArray() {

        $this->assertEquals($this->data, $this->fixture->getData());
    }


    /**
     * @test
     */
    public function getNextReturnsNextItem() {
        $this->assertSame($this->data['second'], $this->fixture->getNext());
    }

    /**
     * @test
     */
    public function getNextGivenTwoReturnsSecondNextItem() {
        $this->assertSame($this->data['third'], $this->fixture->getNext(2));
    }

    /**
     * @test
     */
    public function getPrevFromFirstItemReturnsNull() {
        $this->fixture->rewind();
        $this->assertSame(NULL, $this->fixture->getPrev());
    }


    /**
     * @test
     */
    public function getPrevFromSecondItemReturnsFirstItem() {
        $this->fixture->next();
        $this->assertSame($this->data['first'], $this->fixture->getPrev());
    }


    /**
     * @test
     */
    public function getPrevFromThirdItemGivenTwoReturnsFirstItem() {
        $this->fixture->next();
        $this->fixture->next();
        $this->assertSame($this->data['first'], $this->fixture->getPrev(2));
    }

    /**
     * @test
     */
    public function getElementGivenTwoReturnsThirdItem() {
        $this->assertSame($this->data['third'], $this->fixture->getElement(2));
    }


    /**
     * @test
     */
    public function getElementGivenNonExistingKeyReturnsNull() {
        $this->assertNull($this->fixture->getElement(100));
    }


} 