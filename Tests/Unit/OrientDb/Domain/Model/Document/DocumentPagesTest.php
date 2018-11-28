<?php
namespace RKW\RkwSearch\Tests\OrientDb\Domain\Model\Document;

/**
 * Class DocumentPagesTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DocumentPagesTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
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
            '@type' => 'd',
            '@rid'  => '#11:1151',
            '@version'  => 6,
            '@class'  => 'DocumentPages',

            'uid' =>  12,

            'tstamp'  => time(),
            'crdate' => time(),
            'hidden' => 1,
            'deleted' => 0,
            'sorting' => 0,
            'sysLanguageUid' => 0,
            'endtime' => 0,
            'starttime' => 0,

            'content' => 'Test',
            'doktype' => 1,
            'title' => 'Test',
            'subtitle' => 'Test',
            'abstract' => 'Test',
            'description' => 'Test',
            'keywords' => 'Test'
        );

        $this->fixture = new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages($this->data);
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
    public function getRidReturnsInitialValue() {

        $this->assertSame ($this->data['@rid'], $this->fixture->getRid());
    }


    /**
     * @test
     */
    public function getUidReturnsInitialValue() {

        $this->assertSame ($this->data['uid'], $this->fixture->getUid());

    }


    /**
     * @test
     */
    public function getClassReturnsInitialValue() {
        $this->assertSame ($this->data['@class'], $this->fixture->getClass());
    }


    /**
     * @test
     */
    public function getTypeReturnsInitialValue() {
        $this->assertSame ($this->data['@type'], $this->fixture->getType());
    }


    /**
     * @test
     */
    public function getTypeReturnsDefaultValueIfNotSet() {
        $this->fixture->setProperties(array ('@type' => NULL));
        $this->assertSame (\RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages::RECORD_TYPE_DOCUMENT, $this->fixture->getType());
    }


    /**
     * @test
     */
    public function getVersionReturnsInitialValue() {
        $this->assertSame ($this->data['@version'], $this->fixture->getVersion());
    }


    /**
     * @test
     */
    public function getClusterIdReturnsInitialValue() {
        $this->assertSame (11, $this->fixture->getClusterId());
    }


    /**
     * @test
     */
    public function getPositionIdReturnsInitialValue() {
        $this->assertSame (1151, $this->fixture->getPositionId());
    }



    /**
     * @test
     */
    public function setAndGetPropertyTstampReturnsSetTimestamp() {
        $this->fixture->setTstamp(1234567);
        $this->assertSame (1234567, $this->fixture->getTstamp());
    }

    /**
     * @test
     */
    public function unsetPropertyTstampReturnsNull() {
        $this->fixture->unsTstamp();
        $this->assertSame (NULL, $this->fixture->getTstamp());
    }


    /**
     * @test
     */
    public function hasPropertyHiddenReturnsTrue() {
        $this->assertTrue($this->fixture->hasHidden());
    }


    /**
     * @test
     */
    public function hasPropertyWithUnderscoreReturnsFalse() {
        $this->assertFalse($this->fixture->has_rid());
    }


    /**
     * @test
     */
    public function hasPropertyWithAddSignReturnsFalse() {
        $property = 'has@rid';
        $this->assertFalse($this->fixture->$property());
    }


    /**
     * @test
     */
    public function setPropertiesWithGetPropertiesChangesReturnsSetProperties() {

        // the entries with '@' prefix are not part of the normal properties (= -4)
        $this->assertCount (count($this->data)- 4, $this->fixture->setProperties($this->data)->getPropertiesChanged());
    }

    /**
     *
     * @test
     */
    public function setPropertiesWithUnsetLanguageUidWithGetPropertiesChangesReturnsSetProperties() {

        // the entries with '@' prefix are not part of the normal properties (= -4)
        // and then we delete theL languageUid
        $this->fixture->setProperties($this->data);
        $this->fixture->unsLanguageUid();

        $this->assertCount (count($this->data)- 5, $this->fixture->getPropertiesChanged());
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function invalidMethodThrowsException() {

        $this->fixture->testInvalidMethod();
    }

} 