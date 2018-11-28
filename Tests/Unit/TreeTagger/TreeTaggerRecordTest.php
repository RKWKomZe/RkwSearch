<?php
namespace RKW\RkwSearch\Tests\TreeTagger;

/**
 * Class TreeTaggerRecordTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class TreeTaggerRecordTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\TreeTagger\TreeTaggerRecord
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\TreeTagger\TreeTaggerRecord
     */
    protected $fixture1;

    /**
     * @var \RKW\RkwSearch\TreeTagger\TreeTaggerRecord
     */
    protected $fixture2;

    /**
     * @var \RKW\RkwSearch\TreeTagger\TreeTaggerRecord
     */
    protected $fixture3;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $data1;

    /**
     * @var array
     */
    protected $data2;

    /**
     * @var array
     */
    protected $data3;

    /**
     * Set up fixture
     */
    public function setUp() {

        $this->data = array (
            'Word',
            'Tag',
            'Base'
        );

        $this->data1 = array (
            'Word',
            'Tag',
            '<unknown>'
        );

        $this->data2 = array (
            'Word',
            'Tag',
            'Base1|Base2'
        );

        $this->data3 = array (
            'Word',
            'Tag',
            '@card@'
        );

        $this->fixture = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(implode("\t", $this->data));
        $this->fixture1 = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(implode("\t", $this->data1));
        $this->fixture2 = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(implode("\t", $this->data2));
        $this->fixture3 = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(implode("\t", $this->data3));

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
    public function getDataWithWrongInitDataTypeReturnsEmptyArray() {

        $test = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord($this->data);
        $this->assertSame(array(), $test->getData());
    }


    /**
     * @test
     */
    public function getDataReturnsFullDataArray() {
        $this->assertSame($this->data, $this->fixture->getData());
    }


    /**
     * @test
     */
    public function getWordReturnsTaggedWord() {
        $this->assertSame($this->data[0], $this->fixture->getWord());
    }


    /**
     * @test
     */
    public function getTagReturnsTaggedTag() {
        $this->assertSame($this->data[1], $this->fixture->getTag());
    }


    /**
     * @test
     */
    public function getBaseReturnsTaggedBase() {
        $this->assertSame($this->data[2], $this->fixture->getBase());
    }

    /**
     * @test
     */
    public function getBaseWithUnknownBaseReturnsTaggedWord() {
        $this->assertSame($this->data1[0], $this->fixture1->getBase());
    }

    /**
     * @test
     */
    public function getBaseWithTwoBasesReturnsArray() {
        $this->assertInternalType('array', $this->fixture2->getBase());
    }

    /**
     * @test
     */
    public function getBaseWithCardinalNumberReturnsTaggedWord() {
        $this->assertSame($this->data3[0], $this->fixture3->getBase());
    }

    /**
     * @test
     */
    public function getBaseRawWithUnknownBaseReturnsUnknownTag() {
        $this->assertSame($this->data1[2], $this->fixture1->getBaseRaw());
    }
} 