<?php
namespace RKW\RkwSearch\Tests\TreeTagger\Filter\FilterItem;

/**
 * Class DistanceTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DistanceTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance
     */
    protected $fixture;


    /**
     * @var string
     */
    protected $text;

    /**
     * @var array
     */
    protected $expectedObject;


    /**
     * @var array
     */
    protected $globalConfiguration;

    /**
     * @var array
     */
    protected $filterConfigurationOne;

    /**
     * @var array
     */
    protected $filterConfigurationTwo;

    /**
     * @var array
     */
    protected $expectedArrayOne;

    /**
     * @var array
     */
    protected $expectedArrayTwo;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->text = 'Der Trend zu mehr Gründungen im Alter der wurde erkannt.';

        $this->globalConfiguration = array (

            'ignoreCardinalNumbers' => 1,
            'ignoreDoubles' => 1,
            'ignoreWords' => 'Abb, Mrd, Mio, testen',
            'ignoreBaseWords' => 'sein, haben, werden, test',
            'minWordLength' => 3,
        );

        $this->filterConfigurationOne = array (

            'cur' => 'NN',
            'next' => 'NN,NE,ADJA,VVFIN,VVINF,VVIZU,VAFIN,VVFIN,VVINF,VVIZU',
			'nextFiller' => 'APPR,ART,APPRART',
            'prev' => 'NN,NE,ADJA,VVFIN,VVINF,VVIZU,VAFIN,VVFIN,VVINF,VVIZU',
            'prevFiller' => 'APPR,ART,APPRART',
            'test' => '1',
            'matchAll' => 1,
            'combineKeywords' => 1
        );

        $this->filterConfigurationTwo = array (

            'cur' => 'NN',
            'next' => 'NN,NE',
            'prev' => 'NN,NE',

        );


        $this->expectedArrayOne = array (
            'gründung' => array (
                'variations' => array (
                    'gründungen' => 'Gründungen'
                ),
                'count' => 1,
                'position' => 4,
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'trend gründung' => array (
                'variations' => array (
                    'trend gründungen' => 'Trend Gründungen'
                ),
                'count' => 1,
                'position' => 1,
                'distance' => 3,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'gründung alter' => array (
                'variations' => array (
                    'gründungen alter' => 'Gründungen Alter'
                ),
                'count' => 1,
                'position' => 4,
                'distance' => 2,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'trend gründung alter' => array (
                'variations' => array (
                    'trend gründungen alter' => 'Trend Gründungen Alter'
                ),
                'count' => 1,
                'position' => 1,
                'distance' => 5,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            )

        );


        $this->expectedArrayTwo = array (
            'trend zu gründung alter' => array (
                'variations' => array (
                    'trend zu gründungen alter' => 'Trend zu Gründungen Alter'
                ),
                'count' => 1,
                'position' => 1,
                'distance' => 7,
                'length' => 4,
                'tags' => 'NN APPR NN NN',
                'type' => 'combined',
                'noWeight' => FALSE
            )
        );


        $this->fixture = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($this->globalConfiguration, $this->filterConfigurationOne);
    }


    /**
     *  Special setup for keywords
     */
    public function setUpForKeywords () {

        // set items into object
        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );
        $this->fixture->setCur(4, $treeTaggerRecord);


        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Der',
                    'ART',
                    'die'
                )
            )
        );
        $this->fixture->setPrev(4, $treeTaggerRecord);


        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Trend',
                    'NN',
                    'Trend'
                )
            )
        );
        $this->fixture->setPrev(3, $treeTaggerRecord);


        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'zu',
                    'APPR',
                    'zu'
                )
            )
        );
        if ($this->fixture->checkData($treeTaggerRecord, 'prev'))
            $this->fixture->setPrev(2, $treeTaggerRecord);

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'mehr',
                    'PIAT',
                    'mehr'
                )
            )
        );
        $this->fixture->setPrev(2, $treeTaggerRecord);


        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );
        $this->fixture->setPrev(1, $treeTaggerRecord);

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'im',
                    'APPRART',
                    'in'
                )
            )
        );
        $this->fixture->setNext(1, $treeTaggerRecord);

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Alter',
                    'NN',
                    'Alter'
                )
            )
        );
        $this->fixture->setNext(2, $treeTaggerRecord);


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
    public function getConfigurationFilterWithoutValidFilterConfigurationThrowsException() {
        $this->fixture = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($this->globalConfiguration, array());
        $this->fixture->getConfigurationFilter();
    }


    /**
     * @test
     */
    public function getConfigurationFilterWithoutKeyReturnsArray() {
        $this->assertInternalType('array', $this->fixture->getConfigurationFilter());
    }


    /**
     * @test
     */
    public function getConfigurationFilterGivenKeyReturnsKeyValue() {
        $this->assertSame(array ('NN'), $this->fixture->getConfigurationFilter('cur'));
    }


    /**
     * @test
     */
    public function getConfigurationFilterGivenKeyReferringToStringReturnsExpectedArray() {

        $result = array (
            'APPR',
            'ART',
            'APPRART'
        );
        $this->assertSame($result, $this->fixture->getConfigurationFilter('nextFiller'));
    }


    /**
     * @test
     */
    public function getConfigurationFilterGivenKeyReferringToIntegerReturnsInteger() {

        $this->assertEquals(1, $this->fixture->getConfigurationFilter('test'));
    }


    //==========================================


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getConfigurationWithoutValidGlobalConfigurationThrowsException() {
        $this->fixture = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance(array(), $this->filterConfigurationOne);
        $this->fixture->getConfiguration();
    }



    /**
     * @test
     */
    public function getConfigurationWithoutKeyReturnsArray() {
        $this->assertInternalType('array', $this->fixture->getConfiguration());
    }


    /**
     * @test
     */
    public function getConfigurationGivenKeyReturnsKeyValue() {
        $this->assertSame(3, $this->fixture->getConfiguration('minWordLength'));
    }

    /**
     * @test
     */
    public function getConfigurationGivenKeyReferringToStringReturnsExpectedArray() {
        $result = array (
            'sein',
            'haben',
            'werden',
            'test'
        );
        $this->assertSame($result, $this->fixture->getConfiguration('ignoreBaseWords'));
    }


    /**
     * @test
     */
    public function getConfigurationGivenKeyReferringToIntegerReturnsInteger() {

        $this->assertEquals(1, $this->fixture->getConfiguration('ignoreCardinalNumbers'));
    }



    //==========================================

    /**
     * @test
     */
    public function getBasesReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getBases());

    }


    /**
     * @test
     */
    public function setCurAndGetBasesReturnsExpectedArray() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $result = array (
            'Gründung'
        );

        $this->fixture->setCur(5, $treeTaggerRecord);
        $this->assertSame($result, $this->fixture->getBases());

    }
    /**
     * @test
     */
    public function setPrevAndGetBasesReturnsExpectedArray() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $result = array (
            'Gründung'
        );

        $this->fixture->setPrev(5, $treeTaggerRecord);
        $this->assertSame($result, $this->fixture->getBases());

    }


    /**
     * @test
     */
    public function setNextAndGetBasesReturnsExpectedArray() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $result = array (
            'Gründung'
        );

        $this->fixture->setNext(5, $treeTaggerRecord);
        $this->assertSame($result, $this->fixture->getBases());

    }

    //==========================================

    /**
     * @test
     */
    public function getLastBaseReturnsNull() {

        $this->assertNull($this->fixture->getLastBase());

    }


    /**
     * @test
     */
    public function getLastBaseReturnsExpectedValue() {

        $treeTaggerRecordOne = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $treeTaggerRecordTwo = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Fachkräfte',
                    'NN',
                    'Fachkraft'
                )
            )
        );

        $this->fixture->setCur(5, $treeTaggerRecordOne);
        $this->fixture->setNext(1, $treeTaggerRecordTwo);
        $this->assertSame('Fachkraft', $this->fixture->getLastBase());

    }


    //==========================================

    /**
     * @test
     */
    public function getCurReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getCur());

    }


    /**
     * @test
     */
    public function setCurAndGetCurReturnsExpectedArray() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $result = array (
            5 => $treeTaggerRecord
        );

        $this->fixture->setCur(5, $treeTaggerRecord);
        $this->assertSame($result, $this->fixture->getCur());

    }

    //==========================================


    /**
     * @test
     */
    public function getPrevReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getPrev());

    }


    /**
     * @test
     */
    public function setPrevAndGetPrevReturnsExpectedArray() {


        $treeTaggerRecordOne = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $treeTaggerRecordTwo = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Laden',
                    'NN',
                    'Laden'
                )
            )
        );

        $this->fixture->setPrev(7, $treeTaggerRecordTwo);
        $this->fixture->setPrev(5, $treeTaggerRecordOne);

        $result = array (
                5 => $treeTaggerRecordOne,
                7 => $treeTaggerRecordTwo
        );

        $this->assertSame($result, $this->fixture->getPrev());

    }

    //==========================================


    /**
     * @test
     */
    public function getNextReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getNext());

    }


    /**
     * @test
     */
    public function setNextAndGetNextReturnsExpectedArray() {

        $treeTaggerRecordOne = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $treeTaggerRecordTwo = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Laden',
                    'NN',
                    'Laden'
                )
            )
        );

        $this->fixture->setNext(7, $treeTaggerRecordTwo);
        $this->fixture->setNext(5, $treeTaggerRecordOne);

        $result = array (
            5 => $treeTaggerRecordOne,
            7 => $treeTaggerRecordTwo
        );

        $this->assertSame($result, $this->fixture->getNext());

    }

    //==========================================


    /**
     * @test
     */
    public function checkDataGivenValidRecordReturnsTrue() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $this->assertSame(TRUE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }


    /**
     * @test
     */
    public function checkDataGivenInvalidRecordReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'wissenschaftlicher',
                    'ADJA',
                    'wissenschaftlich'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }

    /**
     * @test
     */
    public function checkDataGivenCardinalNumberRecordReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    '1980',
                    'CARD',
                    'Gründung'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    '1980',
                    '1980',
                    '@CARD@'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }

    /**
     * @test
     */
    public function checkDataGivenDoubleRecordReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );
        $this->fixture->setCur(5, $treeTaggerRecord);

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }

    /**
     * @test
     */
    public function checkDataGivenIgnoreWordRecordReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'testen',
                    'NN',
                    'blablub'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }

    /**
     * @test
     */
    public function checkDataGivenIgnoreBaseWordsRecordReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'blablubb',
                    'NN',
                    'test'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }

    /**
     * @test
     */
    public function checkDataGivenTooShortWordRecordReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'b',
                    'NN',
                    'b'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'cur'));

    }


    /**
     * @test
     */
    public function checkDataGivenFillerRecordForPrevFirstReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'der',
                    'ART',
                    'die'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'prev'));

    }

    /**
     * @test
     */
    public function checkDataGivenFillerRecordForPrevNotFirstReturnsTrue() {

        $treeTaggerRecordOne = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $treeTaggerRecordTwo = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'der',
                    'ART',
                    'die'
                )
            )
        );

        $this->fixture->setPrev(5, $treeTaggerRecordOne);
        $this->assertSame(TRUE, $this->fixture->checkData($treeTaggerRecordTwo, 'prev'));

    }

    /**
     * @test
     */
    public function checkDataGivenFillerRecordForNextFirstReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'der',
                    'ART',
                    'die'
                )
            )
        );

        $this->assertSame(FALSE, $this->fixture->checkData($treeTaggerRecord, 'next'));

    }

    /**
     * @test
     */
    public function checkDataGivenFillerRecordForNextNotFirstReturnsTrue() {

        $treeTaggerRecordOne = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $treeTaggerRecordTwo = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'der',
                    'ART',
                    'die'
                )
            )
        );

        $this->fixture->setNext(5, $treeTaggerRecordOne);
        $this->assertSame(TRUE, $this->fixture->checkData($treeTaggerRecordTwo, 'next'));

    }

    //==========================================

    /**
     * @test
     */
    public function hasMatchWithoutCurReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );


        $this->fixture->setNext(5, $treeTaggerRecord);
        $this->fixture->setPrev(5, $treeTaggerRecord);

        $this->assertSame(FALSE, $this->fixture->hasMatch());

    }


    /**
     * @test
     */
    public function hasMatchWithoutPrevReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );


        $this->fixture->setCur(5, $treeTaggerRecord);
        $this->fixture->setNext(5, $treeTaggerRecord);

        $this->assertSame(FALSE, $this->fixture->hasMatch());

    }

    /**
     * @test
     */
    public function hasMatchWithoutNextReturnsFalse() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );


        $this->fixture->setCur(5, $treeTaggerRecord);
        $this->fixture->setPrev(5, $treeTaggerRecord);

        $this->assertSame(FALSE, $this->fixture->hasMatch());

    }


    /**
     * @test
     */
    public function hasMatchWithAllDataReturnsTrue() {

        $treeTaggerRecordOne = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        $treeTaggerRecordTwo = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Zauberei',
                    'NN',
                    'Zauberei'
                )
            )
        );

        $treeTaggerRecordThree = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Dach',
                    'NN',
                    'Dach'
                )
            )
        );

        $this->fixture->setCur(5, $treeTaggerRecordOne);
        $this->fixture->setPrev(5, $treeTaggerRecordTwo);
        $this->fixture->setNext(5, $treeTaggerRecordThree);

        $this->assertSame(TRUE, $this->fixture->hasMatch());

    }

    /**
     * @test
     */
    public function hasMatchWithCurOnlyAndWithoutMatchAllReturnsTrue() {

        $treeTaggerRecord = new \RKW\RkwSearch\TreeTagger\TreeTaggerRecord(
            implode(
                "\t",
                array (
                    'Gründungen',
                    'NN',
                    'Gründung'
                )
            )
        );

        unset($this->filterConfigurationOne['matchAll']);
        $this->fixture = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($this->globalConfiguration, $this->filterConfigurationOne);

        $this->fixture->setCur(5, $treeTaggerRecord);
        $this->assertSame(TRUE, $this->fixture->hasMatch());

    }

    /**
     * @test
     */
    public function hasMatchWithoutDataAndWithoutMatchAllReturnsFalse() {

        unset($this->filterConfigurationOne['matchAll']);
        $this->fixture = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($this->globalConfiguration, $this->filterConfigurationOne);

        $this->assertSame(FALSE, $this->fixture->hasMatch());

    }

    //==========================================
    /**
     * @test
     */
    public function getKeywordsWithoutDataReturnsEmptyArray() {

        $this->assertEmpty($this->fixture->getKeywords());
    }



    /**
     * @test
     */
    public function getKeywordsWithDefaultSettingReturnsExpectedArray() {

        // set fixture with configuration
        $this->fixture = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($this->globalConfiguration, $this->filterConfigurationTwo);

        // load keywords into fixture
        $this->setUpForKeywords();

        $this->assertEquals($this->expectedArrayOne, $this->fixture->getKeywords());
    }


    /**
     * @test
     */
    public function getKeywordsWithCombineSettingReturnsExpectedArray() {

        // load keywords into fixture
        $this->setUpForKeywords();

        $this->assertEquals($this->expectedArrayTwo, $this->fixture->getKeywords());
    }


    //==========================================

    /**
     * @test
     */
    public function getPreparedStringGivenOnlyOneKeywordReturnsOneKeyword() {

        $this->assertSame('First', $this->fixture->getPreparedString('First', ''));
    }

    /**
     * @test
     */
    public function getPreparedStringGivenOnlySecondKeywordReturnsOneKeyword() {

        $this->assertSame('Second', $this->fixture->getPreparedString('', 'Second'));
    }


    /**
     * @test
     */
    public function getPreparedStringGivenFirstKeywordAsArrayReturnsFirstValueOfArrayCombinedWithSecondKeyword() {

        $this->assertSame('First Second', $this->fixture->getPreparedString(array('First'), 'Second'));
    }

    /**
     * @test
     */
    public function getPreparedStringGivenSecondKeywordAsArrayReturnsFirstKeywordCombinedWithFirstValueOfArray() {

        $this->assertSame('First Second', $this->fixture->getPreparedString('First', array('Second')));
    }



    /**
     * @test
     */
    public function getPreparedStringGivenTwoKeywordsReturnsCombinedKeywords() {

        $this->assertSame('First Second', $this->fixture->getPreparedString('First', 'Second'));
    }

    /**
     * @test
     */
    public function getPreparedStringGivenTwoKeywordsAndSeparatorReturnsCombinedKeywordsWithGivenSeparator() {

        $this->assertSame('First-Second', $this->fixture->getPreparedString('First', 'Second', FALSE, '-'));
    }


    /**
     * @test
     */
    public function getPreparedStringGivenOnlyOneKeywordAndStrToLowerTrueReturnsOneKeyword() {

        $this->assertSame('first', $this->fixture->getPreparedString('First', '', TRUE));
    }

    /**
     * @test
     */
    public function getPreparedStringGivenTwoKeywordsAndStrToLowerTrueReturnsCombinedKeywords() {

        $this->assertSame('first second', $this->fixture->getPreparedString('First', 'Second', TRUE));
    }

    /**
     * @test
     */
    public function getPreparedStringGivenTwoKeywordsAndStrToLowerTrueAndSeparatorReturnsCombinedKeywordsWithGivenSeparator() {

        $this->assertSame('first-second', $this->fixture->getPreparedString('First', 'Second', TRUE, '-'));
    }


} 