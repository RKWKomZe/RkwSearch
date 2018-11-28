<?php
namespace RKW\RkwSearch\Tests\TreeTagger\Filter;

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
     * @var \RKW\RkwSearch\TreeTagger\TreeTagger
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
    protected $configuration;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->text = 'Der Trend zu mehr Gründungen im Alter der wurde erkannt.';

        $this->configuration = array (

            0 => array (
                'textFilterRegExpr' => array (

                    1 => array (
                        'search' => '/(\s—\s?)|(\s-\s?)|(„)|(“)|(")|(‚)|(‘)|(»)|(«)|(›)|(‹)|(€)/i',
                        'replace' => '/ /'
                    )
                ),

                'filter' => array (

                    'distance' => array (

                        'ignoreCardinalNumbers' => 1,
                        'ignoreWords' => 'Abb, Mrd, Mio',
                        'ignoreBaseWords' => 'sein, haben, werden',
                        'minWordLength' => 2,

                        'definition' => array (

                            10 => array (
                                'cur' => 'NN',
                                'next' => 'NN,NE',
                                'prev' => 'NN,NE',
                            ),

                            20 => array (
                                'cur' => 'NN',
                                'next' => 'NN,NE,ADJA,VVFIN,VVIZU,VAFIN,VVINF',
                                'nextFiller' => 'APPR,ART,APPRART',
                                'prev' => 'NN,NE,ADJA,VVFIN,VVIZU,VAFIN,VVINF',
                                'prevFiller' => 'APPR,ART,APPRART',
                                'combineKeywords' => 1
                            ),

                        )
                    )
                )
            )
        );

        $subFilterConfiguration = $this->configuration[0]['filter']['distance']['definition'];
        $strippedConfiguration = $this->configuration[0]['filter']['distance'];
        unset($strippedConfiguration['definition']);


        // set items into object
        //====================================
        // round 1)
        $expectedArray = array ();
        $subFilter = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($strippedConfiguration, $subFilterConfiguration[10]);
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
        $subFilter->setCur(1, $treeTaggerRecord);


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
        $subFilter->setNext(5, $treeTaggerRecord);


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
        $subFilter->setNext(3, $treeTaggerRecord);

        $expectedArray[] = $subFilter;
        unset($subFilter);

        //====================================
        // round 2)
        $subFilter = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($strippedConfiguration, $subFilterConfiguration[20]);
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
        $subFilter->setCur(1, $treeTaggerRecord);

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
        $subFilter->setNext(5, $treeTaggerRecord);



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
        $subFilter->setNext(4, $treeTaggerRecord);


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
        $subFilter->setNext(3, $treeTaggerRecord);


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
        $subFilter->setNext(1, $treeTaggerRecord);

        $expectedArray[] = $subFilter;
        unset($subFilter);

        //====================================
        // round 3)
        $subFilter = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($strippedConfiguration, $subFilterConfiguration[10]);
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
        $subFilter->setCur(4, $treeTaggerRecord);

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
        $subFilter->setPrev(3, $treeTaggerRecord);


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
        $subFilter->setNext(2, $treeTaggerRecord);

        $expectedArray[] = $subFilter;
        unset($subFilter);

        //====================================
        // round 4)
        $subFilter = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($strippedConfiguration, $subFilterConfiguration[20]);
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
        $subFilter->setCur(4, $treeTaggerRecord);


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
        $subFilter->setPrev(3, $treeTaggerRecord);


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
        $subFilter->setPrev(2, $treeTaggerRecord);


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
        $subFilter->setNext(2, $treeTaggerRecord);


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
        $subFilter->setNext(1, $treeTaggerRecord);


        $expectedArray[] = $subFilter;
        unset($subFilter);


        //====================================
        // round 5)
        $subFilter = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($strippedConfiguration, $subFilterConfiguration[10]);
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
        $subFilter->setCur(6, $treeTaggerRecord);


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
        $subFilter->setPrev(5, $treeTaggerRecord);


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
        $subFilter->setPrev(2, $treeTaggerRecord);

        $expectedArray[] = $subFilter;
        unset($subFilter);

        //====================================
        // round 6)
        $subFilter = new \RKW\RkwSearch\TreeTagger\Filter\FilterItem\Distance($strippedConfiguration, $subFilterConfiguration[20]);
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
        $subFilter->setCur(6, $treeTaggerRecord);


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
        $subFilter->setPrev(5, $treeTaggerRecord);


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
        $subFilter->setPrev(4, $treeTaggerRecord);


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
        $subFilter->setPrev(2, $treeTaggerRecord);


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
        $subFilter->setPrev(1, $treeTaggerRecord);


        $expectedArray[] = $subFilter;
        unset($subFilter);


        // Generate collection object
        $this->expectedObject = new \RKW\RkwSearch\TreeTagger\Collection\FilteredRecords ($expectedArray);

        $this->fixture = new \RKW\RkwSearch\TreeTagger\TreeTagger(0, $this->configuration);
        $this->fixture->setText($this->text)->execute();
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
    public function executeReturnsInstanceOfCollectionFilteredRecords() {
        $this->assertInstanceOf('RKW\\RkwSearch\\TreeTagger\\Collection\\FilteredRecords', $this->fixture->getFilteredResults('distance'));
    }


    /**
     * @test
     */
    public function executeReturnsExpectedObject() {

        $this->assertEquals ( $this->expectedObject, $this->fixture->getFilteredResults('distance'));

    }


} 