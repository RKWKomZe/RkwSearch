<?php
namespace RKW\RkwSearch\Tests\TreeTagger\Collection;

/**
 * Class RecordsTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class RecordsTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\TreeTagger\Collection\Records
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

        $this->data = array (
            'Word1' ."\t" . 'Tag1' . "\t" . 'Base1',
            'Word2' ."\t" . 'Tag2' . "\t" . 'Base2',
            'Word3' ."\t" . 'Tag3' . "\t" . 'Base3',
            'Word4' ."\t" . 'Tag4' . "\t" . 'Base4',
        );


        $this->fixture = new \RKW\RkwSearch\TreeTagger\Collection\Records($this->data);
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
    public function firstReturnsInstanceOfTreeTaggerRecord() {
        $this->assertInstanceOf ('RKW\\RkwSearch\\TreeTagger\\TreeTaggerRecord', $this->fixture->first());
    }


    /**
     * @test
     */
    public function keyInitiallyReturnsZero () {

        $this->fixture->rewind();
        $this->assertSame (0, $this->fixture->key());
    }


    /**
     * @test
     */
    public function nextReturnsNextKey () {

        $this->fixture->rewind();
        $this->fixture->next();
        $this->assertSame (1, $this->fixture->key());
    }


    /**
     * @test
     */
    public function prevReturnsPrevKey () {

        $this->fixture->rewind();
        $this->fixture->next();
        $this->fixture->prev();
        $this->assertSame (0, $this->fixture->key());
    }


    /**
     * @test
     */
    public function prevInitiallyReturnsNegativeNumber () {

        $this->fixture->rewind();
        $this->fixture->prev();
        $this->assertSame (-1, $this->fixture->key());
    }



    /**
     * @test
     */
    public function validReturnsTrue () {

        $this->fixture->rewind();
        $this->assertTrue($this->fixture->valid());
    }



    /**
     * @test
     */
    public function countReturnsCorrectNumber() {
        $this->assertSame (count($this->data), $this->fixture->count());
    }


    /**
     * @test
     */
    public function getNextReturnsInstanceOfTreeTaggerRecord() {
        $this->assertInstanceOf('RKW\\RkwSearch\\TreeTagger\\TreeTaggerRecord', $this->fixture->getNext());
    }

    /**
     * @test
     */
    public function getNextReturnsNextInstanceOfTreeTaggerRecord() {
        $this->assertSame('Word2', $this->fixture->getNext()->getWord());
    }

    /**
     * @test
     */
    public function getNextGivenTwoReturnsSecondNextInstanceOfTreeTaggerRecord() {
        $this->assertSame('Word3', $this->fixture->getNext(2)->getWord());
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
    public function getPrevFromSecondItemReturnsInstanceOfTreeTaggerRecord() {
        $this->fixture->next();
        $this->assertInstanceOf('RKW\\RkwSearch\\TreeTagger\\TreeTaggerRecord', $this->fixture->getPrev());
    }

    /**
     * @test
     */
    public function getPrevFromSecondItemReturnsFirstInstanceOfTreeTaggerRecord() {
        $this->fixture->next();
        $this->assertSame('Word1', $this->fixture->getPrev()->getWord());
    }


    /**
     * @test
     */
    public function getPrevFromThirdItemGivenTwoReturnsFirstInstanceOfTreeTaggerRecord() {
        $this->fixture->next();
        $this->fixture->next();
        $this->assertSame('Word1', $this->fixture->getPrev(2)->getWord());
    }

    /**
     * @test
     */
    public function forwardToWithCurrentPositionGreaterThanForwardToValueDoesNothing() {
        $this->fixture->next();
        $this->fixture->next();
        $this->fixture->forwardToPosition(1);
        $this->assertSame('Word3', $this->fixture->current()->getWord());
    }


    /**
     * @test
     */
    public function forwardToWithCurrentPositionEqualsForwardToValueDoesNothing() {
        $this->fixture->next();
        $this->fixture->next();
        $this->fixture->forwardToPosition(2);
        $this->assertSame('Word3', $this->fixture->current()->getWord());
    }

    /**
     * @test
     */
    public function forwardToJumpsToGivenPosition() {
        $this->fixture->forwardToPosition(2);
        $this->assertSame('Word3', $this->fixture->current()->getWord());
    }


    /**
     * @test
     */
    public function forwardToInForeachLoopJumpsToGivenPosition() {

        $test = '';
        foreach ($this->fixture as $object) {

            if ($this->fixture->forwardToPosition(2))
                continue;
                //===

            $test .= $object->getWord();
        }

        $this->assertSame('Word4', $test);
    }
} 