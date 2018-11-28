<?php
namespace RKW\RkwSearch\Tests\Keywords;

/**
 * Class FetcherTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class FetcherTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Keywords\Fetcher
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
     */
    protected $contentModel;


    /**
     * Set up fixture
     */
    public function setUp() {

        $dataForContent = array (
            'uid' => rand(201,300) + time(),

            'tstamp'  => time(),
            'crdate' => time(),
            'hidden' => 0,
            'deleted' => 0,
            'sorting' => 0,
            'sysLanguageUid' => 0,
            'endtime' => 0,
            'starttime' => 0,

            'content' => 'Test',
            'doktype' => 1,
            'title' => 'Test',
            'subtitle' => 'Test Test',
            'abstract' => 'Test',
            'description' => 'Test',
            'keywords' => 'Test'

        );


        // set up content entry
        $this->contentModel = new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages($dataForContent);

        $configuration = array(
            'fields' => array (

                'DocumentPages' => array (
                    'fieldList' => 'title,content',
                ),
            ),
        );

        $this->fixture = new \RKW\RkwSearch\Keywords\Fetcher($configuration);


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
    public function getContentGivenWrongInstanceThrowsException() {
        $this->fixture->getContent($this);

    }


    /**
     * @test
     */
    public function getContentReturnsString() {
        $this->assertInternalType('string', $this->fixture->getContent($this->contentModel));

    }

    /**
     * @test
     */
    public function getContentReturnsExpectedString() {
        $this->assertSame('Test. Test' , $this->fixture->getContent($this->contentModel));

    }

    /**
     * @test
     */
    public function getContentWithDefinedStringSeparatorReturnsExpectedString() {

        $configuration = array(
            'fields' => array (
                'DocumentPages' => array (
                    'fieldList' => 'title,content',
                    'separator' => '-'
                ),
            ),
        );

        $this->fixture = new \RKW\RkwSearch\Keywords\Fetcher($configuration);
        $this->assertSame('Test' . '-' . 'Test' , $this->fixture->getContent($this->contentModel));

    }


    /**
     * @test
     */
    public function getContentWithDefinedAsciiSeparatorReturnsExpectedString() {

        $configuration = array(
            'fields' => array (
                'DocumentPages' => array (
                    'fieldList' => 'title,content',
                    'separator' => 9
                ),
            ),
        );

        $this->fixture = new \RKW\RkwSearch\Keywords\Fetcher($configuration);
        $this->assertSame('Test' . "\t" . 'Test' , $this->fixture->getContent($this->contentModel));

    }

    /**
     * @test
     */
    public function getContentWithOneInvalidFieldReturnsExpectedString() {
        $configuration = array(
            'fields' => array (
                'DocumentPages' => array (
                    'fieldList' => 'titleles,content',
                ),
            ),
        );

        $this->fixture = new \RKW\RkwSearch\Keywords\Fetcher($configuration);
        $this->assertSame('Test', $this->fixture->getContent($this->contentModel));

    }


}