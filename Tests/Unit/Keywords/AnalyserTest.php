<?php
namespace RKW\RkwSearch\Tests\Keywords;

/**
 * Class AnalyserTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class AnalyserTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Keywords\Analyser
     */
    protected $fixture;


    /**
     * @var object
     */
    protected $expectedObjectOne;

    /**
     * @var object
     */
    protected $expectedObjectTwo;

    /**
     * @var object
     */
    protected $expectedObjectThree;

    /**
     * @var object
     */
    protected $configuration;




    /**
     * Set up fixture
     */
    public function setUp() {


        $text = 'Zur Förderung von Senior Entrepreneurship wurde gemeinsam mit der OECD den Kurzdossier zur unternehmerischen Initiative älterer Menschen erstellt. Die Positionen der EU/OECD zum Thema "Senior Entrepreneurship" sind hier nachzulesen.';
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

        //=======================================================

        $expectedArrayOne = array (

            'förderung' => array (
                'variations' => array (
                    'förderung' => 'Förderung'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'förderung senior' => array (
                'variations' => array (
                    'förderung senior' => 'Förderung Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 2,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'förderung entrepreneurship' => array (
                'variations' => array (
                    'förderung entrepreneurship' => 'Förderung Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 3,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'förderung von senior entrepreneurship' => array (
                'variations' => array (
                    'förderung von senior entrepreneurship' => 'Förderung von Senior Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 6,
                'length' => 4,
                'tags' => 'NN APPR NN NN',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'senior' => array (
                'variations' => array (
                    'senior' => 'Senior'
                ),
                'count' => 2,
                'position' => array (
                    0 => 3,
                    1 => 25
                ),
                'distance' => 0.0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'senior entrepreneurship' => array (
                'variations' => array (
                    'senior entrepreneurship' => 'Senior Entrepreneurship'
                ),
                'count' => 2,
                'position' => array (
                    0 => 3,
                    1 => 25,
                ),
                'distance' => 1.0,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'förderung senior entrepreneurship' => array (
                'variations' => array (
                    'förderung senior entrepreneurship' => 'Förderung Senior Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 3,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'entrepreneurship' => array (
                'variations' => array (
                    'entrepreneurship' => 'Entrepreneurship'
                ),
                'count' => 2,
                'position' => array (
                    0 => 4,
                    1 => 26
                ),
                'distance' => 0.0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'entrepreneurship oecd' => array (
                'variations' => array (
                    'entrepreneurship oecd' => 'Entrepreneurship OECD'
                ),
                'count' => 1,
                'position' => array (
                    0 => 4
                ),
                'distance' => 5,
                'length' => 2,
                'tags' => 'NN NE',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'senior entrepreneurship oecd' => array (
                'variations' => array (
                    'senior entrepreneurship oecd' => 'Senior Entrepreneurship OECD'
                ),
                'count' => 1,
                'position' => array (
                    0 => 3
                ),
                'distance' => 6,
                'length' => 3,
                'tags' => 'NN NN NE',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'förderung entrepreneurship oecd' => array (
                'variations' => array (
                    'förderung entrepreneurship oecd' => 'Förderung Entrepreneurship OECD'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 8,
                'length' => 3,
                'tags' => 'NN NN NE',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'förderung von senior entrepreneurship mit die oecd' => array (
                'variations' => array (
                    'förderung von senior entrepreneurship mit der oecd' => 'Förderung von Senior Entrepreneurship mit der OECD'
                ),
                'count' => 1,
                'position' => array (
                    0 => 1
                ),
                'distance' => 18,
                'length' => 7,
                'tags' => 'NN APPR NN NN APPR ART NE',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'kurzdossier' => array (
                'variations' => array (
                    'kurzdossier' => 'Kurzdossier'
                ),
                'count' => 1,
                'position' => array (
                    0 => 11
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'oecd kurzdossier' => array (
                'variations' => array (
                    'oecd kurzdossier' => 'OECD Kurzdossier'
                ),
                'count' => 1,
                'position' => array (
                    0 => 9
                ),
                'distance' => 2,
                'length' => 2,
                'tags' => 'NE NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'kurzdossier initiative' => array (
                'variations' => array (
                    'kurzdossier initiative' => 'Kurzdossier Initiative'
                ),
                'count' => 1,
                'position' => array (
                    0 => 11
                ),
                'distance' => 3,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'kurzdossier mensch' => array (
                'variations' => array (
                    'kurzdossier menschen' => 'Kurzdossier Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 11
                ),
                'distance' => 5,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'oecd kurzdossier initiative' => array (
                'variations' => array (
                    'oecd kurzdossier initiative' => 'OECD Kurzdossier Initiative'
                ),
                'count' => 1,
                'position' => array (
                    0 => 9
                ),
                'distance' => 5,
                'length' => 3,
                'tags' => 'NE NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'oecd kurzdossier mensch' => array (
                'variations' => array (
                    'oecd kurzdossier menschen' => 'OECD Kurzdossier Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 9
                ),
                'distance' => 7,
                'length' => 3,
                'tags' => 'NE NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'oecd die kurzdossier zu unternehmerisch initiative alt mensch' => array (
                'variations' => array (
                    'oecd den kurzdossier zur unternehmerischen initiative älterer menschen' => 'OECD den Kurzdossier zur unternehmerischen Initiative älterer Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 9
                ),
                'distance' => 18,
                'length' => 8,
                'tags' => 'NE ART NN APPRART ADJA NN ADJA NN',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'initiative' => array (
                'variations' => array (
                    'initiative' => 'Initiative'
                ),
                'count' => 1,
                'position' => array (
                    0 => 14
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'oecd initiative' => array (
                'variations' => array (
                    'oecd initiative' => 'OECD Initiative'
                ),
                'count' => 1,
                'position' => array (
                    0 => 9
                ),
                'distance' => 5,
                'length' => 2,
                'tags' => 'NE NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'initiative mensch' => array (
                'variations' => array (
                    'initiative menschen' => 'Initiative Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 14,
                ),
                'distance' => 2,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'kurzdossier initiative mensch' => array (
                'variations' => array (
                    'kurzdossier initiative menschen' => 'Kurzdossier Initiative Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 11
                ),
                'distance' => 5,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'oecd initiative mensch' => array (
                'variations' => array (
                    'oecd initiative menschen' => 'OECD Initiative Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 9
                ),
                'distance' => 7,
                'length' => 3,
                'tags' => 'NE NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'mensch' => array (
                'variations' => array (
                    'menschen' => 'Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 16
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'kurzdossier zu unternehmerisch initiative alt mensch' => array (
                'variations' => array (
                    'kurzdossier zur unternehmerischen initiative älterer menschen' => 'Kurzdossier zur unternehmerischen Initiative älterer Menschen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 11,
                ),
                'distance' => 15,
                'length' => 6,
                'tags' => 'NN APPRART ADJA NN ADJA NN',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'position' => array (
                'variations' => array (
                    'positionen' => 'Positionen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20,
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position eu/oecd' => array (
                'variations' => array (
                    'positionen eu/oecd' => 'Positionen EU/OECD'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 2,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position thema' => array (
                'variations' => array (
                    'positionen thema' => 'Positionen Thema'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 4,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position senior' => array (
                'variations' => array (
                    'positionen senior' => 'Positionen Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 5,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position die eu/oecd zu thema senior' => array (
                'variations' => array (
                    'positionen der eu/oecd zum thema senior' => 'Positionen der EU/OECD zum Thema Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 15,
                'length' => 6,
                'tags' => 'NN ART NN APPRART NN NN',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'eu/oecd' => array (
                'variations' => array (
                    'eu/oecd' => 'EU/OECD'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd thema' => array (
                'variations' => array (
                    'eu/oecd thema' => 'EU/OECD Thema'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 2,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd senior' => array (
                'variations' => array (
                    'eu/oecd senior' => 'EU/OECD Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 3,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd entrepreneurship' => array (
                'variations' => array (
                    'eu/oecd entrepreneurship' => 'EU/OECD Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 4,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position eu/oecd thema' => array (
                'variations' => array (
                    'positionen eu/oecd thema' => 'Positionen EU/OECD Thema'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 4,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position eu/oecd senior' => array (
                'variations' => array (
                    'positionen eu/oecd senior' => 'Positionen EU/OECD Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 5,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position eu/oecd entrepreneurship' => array (
                'variations' => array (
                    'positionen eu/oecd entrepreneurship' => 'Positionen EU/OECD Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 6,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position die eu/oecd zu thema senior entrepreneurship' => array (
                'variations' => array (
                    'positionen der eu/oecd zum thema senior entrepreneurship' => 'Positionen der EU/OECD zum Thema Senior Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 13,
                'length' => 7,
                'tags' => 'NN ART NN APPRART NN NN NN',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'thema' => array (
                'variations' => array (
                    'thema' => 'Thema'
                ),
                'count' => 1,
                'position' => array (
                    0 => 24
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'thema senior' => array (
                'variations' => array (
                    'thema senior' => 'Thema Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 24
                ),
                'distance' => 1,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'thema entrepreneurship' => array (
                'variations' => array (
                    'thema entrepreneurship' => 'Thema Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 24
                ),
                'distance' => 2,
                'length' => 2,
                'tags' => 'NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd thema senior' => array (
                'variations' => array (
                    'eu/oecd thema senior' => 'EU/OECD Thema Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 3,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd thema entrepreneurship' => array (
                'variations' => array (
                    'eu/oecd thema entrepreneurship' => 'EU/OECD Thema Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 4,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position thema senior' => array (
                'variations' => array (
                    'positionen thema senior' => 'Positionen Thema Senior'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 5,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position thema entrepreneurship' => array (
                'variations' => array (
                    'positionen thema entrepreneurship' => 'Positionen Thema Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 6,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position die eu/oecd zu thema senior entrepreneurship nachlesen' => array (
                'variations' => array (
                    'positionen der eu/oecd zum thema senior entrepreneurship nachzulesen' => 'Positionen der EU/OECD zum Thema Senior Entrepreneurship nachzulesen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 18,
                'length' => 8,
                'tags' => 'NN ART NN APPRART NN NN NN VVIZU',
                'type' => 'combined',
                'noWeight' => FALSE
            ),
            'thema senior entrepreneurship' => array (
                'variations' => array (
                    'thema senior entrepreneurship' => 'Thema Senior Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 24
                ),
                'distance' => 2,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd senior entrepreneurship' => array (
                'variations' => array (
                    'eu/oecd senior entrepreneurship' => 'EU/OECD Senior Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 4,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'position senior entrepreneurship' => array (
                'variations' => array (
                    'positionen senior entrepreneurship' => 'Positionen Senior Entrepreneurship'
                ),
                'count' => 1,
                'position' => array (
                    0 => 20
                ),
                'distance' => 6,
                'length' => 3,
                'tags' => 'NN NN NN',
                'type' => 'default',
                'noWeight' => FALSE
            ),
            'eu/oecd zu thema senior entrepreneurship nachlesen' => array (
                'variations' => array (
                    'eu/oecd zum thema senior entrepreneurship nachzulesen' => 'EU/OECD zum Thema Senior Entrepreneurship nachzulesen'
                ),
                'count' => 1,
                'position' => array (
                    0 => 22
                ),
                'distance' => 13,
                'length' => 6,
                'tags' => 'NN APPRART NN NN NN VVIZU',
                'type' => 'combined',
                'noWeight' => FALSE
            )

        );

       //=======================================================

        $expectedArrayTwo = $expectedArrayOne;
        $expectedArrayTwo['förderung']['weight'] = 0.17542506358195;
        $expectedArrayTwo['förderung senior']['weight'] = 0.27804214746402;
        $expectedArrayTwo['förderung entrepreneurship']['weight'] = 0.21033076499347;
        $expectedArrayTwo['förderung von senior entrepreneurship']['weight'] = 0.30812900130778;
        $expectedArrayTwo['senior']['weight'] = 0.27804214746402;
        $expectedArrayTwo['senior entrepreneurship']['weight'] = 0.69847138267913;
        $expectedArrayTwo['förderung senior entrepreneurship']['weight'] = 0.35085012716391;
        $expectedArrayTwo['entrepreneurship']['weight'] = 0.27804214746402;
        $expectedArrayTwo['entrepreneurship oecd']['weight'] = 0.15383926204571;
        $expectedArrayTwo['senior entrepreneurship oecd']['weight'] = 0.22136178427204;
        $expectedArrayTwo['förderung entrepreneurship oecd']['weight'] = 0.18717306346127;
        $expectedArrayTwo['förderung von senior entrepreneurship mit die oecd']['weight'] = 0.28656402257759;
        $expectedArrayTwo['kurzdossier']['weight'] = 0.17542506358195;
        $expectedArrayTwo['oecd kurzdossier']['weight'] = 0.27804214746402;
        $expectedArrayTwo['kurzdossier initiative']['weight'] = 0.21033076499347;
        $expectedArrayTwo['kurzdossier mensch']['weight'] = 0.15383926204571;
        $expectedArrayTwo['oecd kurzdossier initiative']['weight'] = 0.24794404907482;
        $expectedArrayTwo['oecd kurzdossier mensch']['weight'] = 0.2019902572292;
        $expectedArrayTwo['oecd die kurzdossier zu unternehmerisch initiative alt mensch']['weight'] = 0.32702382154185;
        $expectedArrayTwo['initiative']['weight'] = 0.17542506358195;
        $expectedArrayTwo['oecd initiative']['weight'] = 0.15383926204571;
        $expectedArrayTwo['initiative mensch']['weight'] = 0.27804214746402;
        $expectedArrayTwo['kurzdossier initiative mensch']['weight'] = 0.24794404907482;
        $expectedArrayTwo['oecd initiative mensch']['weight'] = 0.2019902572292;
        $expectedArrayTwo['mensch']['weight'] = 0.17542506358195;
        $expectedArrayTwo['kurzdossier zu unternehmerisch initiative alt mensch']['weight'] = 0.27248683127407;
        $expectedArrayTwo['position']['weight'] = 0.17542506358195;
        $expectedArrayTwo['position eu/oecd']['weight'] = 0.27804214746402;
        $expectedArrayTwo['position thema']['weight'] = 0.17542506358195;
        $expectedArrayTwo['position senior']['weight'] = 0.15383926204571;
        $expectedArrayTwo['position die eu/oecd zu thema senior']['weight'] = 0.27248683127407;
        $expectedArrayTwo['eu/oecd']['weight'] = 0.17542506358195;
        $expectedArrayTwo['eu/oecd thema']['weight'] = 0.27804214746402;
        $expectedArrayTwo['eu/oecd senior']['weight'] = 0.21033076499347;
        $expectedArrayTwo['eu/oecd entrepreneurship']['weight'] = 0.17542506358195;
        $expectedArrayTwo['position eu/oecd thema']['weight'] = 0.28701922642839;
        $expectedArrayTwo['position eu/oecd senior']['weight'] = 0.24794404907482;
        $expectedArrayTwo['position eu/oecd entrepreneurship']['weight'] = 0.22136178427204;
        $expectedArrayTwo['position die eu/oecd zu thema senior entrepreneurship']['weight'] = 0.34747425887823;
        $expectedArrayTwo['thema']['weight'] = 0.17542506358195;
        $expectedArrayTwo['thema senior']['weight'] = 0.27804214746402;
        $expectedArrayTwo['thema entrepreneurship']['weight'] = 0.27804214746402;
        $expectedArrayTwo['eu/oecd thema senior']['weight'] = 0.35085012716391;
        $expectedArrayTwo['eu/oecd thema entrepreneurship']['weight'] = 0.28701922642839;
        $expectedArrayTwo['position thema senior']['weight'] = 0.24794404907482;
        $expectedArrayTwo['position thema entrepreneurship']['weight'] = 0.22136178427204;
        $expectedArrayTwo['position die eu/oecd zu thema senior entrepreneurship nachlesen']['weight'] = 0.32702382154185;
        $expectedArrayTwo['thema senior entrepreneurship']['weight'] = 0.47607395778205;
        $expectedArrayTwo['eu/oecd senior entrepreneurship']['weight'] = 0.28701922642839;
        $expectedArrayTwo['position senior entrepreneurship']['weight'] = 0.22136178427204;
        $expectedArrayTwo['eu/oecd zu thema senior entrepreneurship nachlesen']['weight'] = 0.29614598739683;


        //=======================================================
        $expectedArrayTree = array (
            'förderung' => array (
                'variations' => array (
                        'förderung' => 'Förderung'
                ),
                'count' => 1,
                'position' => array (
                        0 => 0
                ),
                'distance' => 0,
                'length' => 1,
                'tags' => 'NN',
                'type' => 'default',
                'noWeight' => TRUE,
                'weight' => 0
            )
        );

        // prepare tagged data
        $tagger = new \RKW\RkwSearch\TreeTagger\TreeTagger(0, $this->configuration);
        $data = $tagger->setText($text)->execute()->getFilteredResults('distance');

        $this->expectedObjectOne = new \RKW\RkwSearch\Collection\AnalysedKeywords($expectedArrayOne);
        $this->expectedObjectTwo = new \RKW\RkwSearch\Collection\AnalysedKeywords($expectedArrayTwo);
        $this->expectedObjectThree = new \RKW\RkwSearch\Collection\AnalysedKeywords($expectedArrayTree);


        $this->fixture = new \RKW\RkwSearch\Keywords\Analyser();
        $this->fixture->setData($data);

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
    public function countMatchesAndGetResultsReturnInstanceOfCollectionAnalysedKeywords() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Collection\\AnalysedKeywords', $this->fixture->countMatches()->getResults());
    }

    /**
     * @test
     */
    public function countMatchesAndGetResultsReturnExpectedObjectOne() {

        $this->assertEquals($this->expectedObjectOne, $this->fixture->countMatches()->getResults());

    }

    /**
     * @test
     */
    public function countMatchesAndWeightMatchesAndGetResultsReturnInstanceOfCollectionAnalysedKeywords() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Collection\\AnalysedKeywords', $this->fixture->countMatches()->weightMatches()->getResults());
    }

    /**
     * @test
     */
    public function countMatchesAndWeightMatchesAndGetResultsReturnExpectedObjectTwo() {

        $this->assertEquals($this->expectedObjectTwo, $this->fixture->countMatches()->weightMatches()->getResults());
    }


    /**
     * @test
     */
    public function countMatchesAndWeightMatchesAndGetResultsWithNoMatchReturnExpectedObjectThree() {

        $this->configuration[0]['filter']['distance']['definition'][10]['noWeight'] = 1;

        $tagger = new \RKW\RkwSearch\TreeTagger\TreeTagger(0, $this->configuration);
        $data = $tagger->setText('Förderung')->execute()->getFilteredResults('distance');
        $this->fixture = new \RKW\RkwSearch\Keywords\Analyser();
        $this->fixture->setData($data);

        $this->assertEquals($this->expectedObjectThree, $this->fixture->countMatches()->weightMatches()->getResults());
    }

    /**
     * @test
     */
    public function countMatchesAndWeightMatchesAndGetResultsSummaryReturnsString() {

        $this->assertInternalType('string', $this->fixture->countMatches()->weightMatches()->getResultsSummary());
    }

    /**
     * @test
     */
    public function getTopKeywordsReturnsExpectedArray() {

        $result = array (
            'Senior Entrepreneurship' => 0.69847138267913,
            'Thema Senior Entrepreneurship' => 0.47607395778205,
            'EU/OECD Thema Senior' => 0.35085012716391,
            'Förderung Senior Entrepreneurship' => 0.35085012716391,
            'Positionen der EU/OECD zum Thema Senior Entrepreneurship' => 0.34747425887823,
            'Positionen der EU/OECD zum Thema Senior Entrepreneurship nachzulesen' => 0.32702382154185,
            'OECD den Kurzdossier zur unternehmerischen Initiative älterer Menschen' => 0.32702382154185,
            'Förderung von Senior Entrepreneurship' => 0.30812900130778,
            'EU/OECD zum Thema Senior Entrepreneurship nachzulesen' => 0.29614598739683,
            'EU/OECD Senior Entrepreneurship' => 0.28701922642839,
        );

        $this->assertEquals($result, $this->fixture->countMatches()->weightMatches()->getTopKeywords(10));
    }

} 