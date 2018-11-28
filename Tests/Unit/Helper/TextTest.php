<?php
namespace RKW\RkwSearch\Tests\Helper;

/**
 * Class TextTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class TextTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Helper\Text
     */
    protected $fixture;


    /**
     * @var array
     */
    protected $configuration;

    /**
     * Set up fixture
     */
    public function setUp() {


        $this->configuration = array (

            0 => array (

                'stopWords' =>
                    'einer, eine, eines, einem, einen, der, die, das, dass, daß, du, er, sie, es, was,
                    wer, wie, wir, und, oder, ohne, mit, am, im, in, aus, auf, ist, sein, war, wird,
                    ihr, ihre, ihres, als, für, von, dich, dir, mich, mir, mein, kein, durch, wegen'
                ,

                'textFilterRegExpr' => array (

                    1 => array (
                        'search' => '/(„)|(“)|(”)|(")|(‚)|(‘)|(»)|(«)|(›)|(‹)|(€)/i',
                        'replace' => '/ /'
                    ),
                    2 => array (
                        'search' => '/(\([^\)]+\))|(((http:\/\/)|(www\.))([A-Za-z0-9\.\/\-_\#]+))|([0-9]+[a-z]{1,4})|([a-z0-9_\-]+@[a-z0-9_\-]+\.[a-z]{1,4})/i',
                        'replace' => '//'
                    ),
                    3 => array (
                        'search' => '/(\s—\s?)|(\s-\s?)/i',
                        'replace' => '/. /'
                    ),
                    4 => array (
                        'search' => '/(&)|(\+)/i',
                        'replace' => '/ und /'
                    ),

                ),
            ),
        );


        $this->fixture = new \RKW\RkwSearch\Helper\Text();
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
    public function removeStopWordsReturnsExpectedString() {

        $this->assertSame('tolle Hütte mehr Wohnraum hat auch nicht', $this->fixture->removeStopWords('Das ist eine tolle Hütte und mehr Wohnraum hat ihres auch nicht', 0, $this->configuration[0]['stopWords']));
        $this->assertSame('Fachkräfte finden binden', $this->fixture->removeStopWords('Fachkräfte finden und binden', 0, $this->configuration[0]['stopWords']));

    }

        /**
     * @test
     */
    public function sanitizeStringLuceneReturnsExpectedString() {

        $stringArray = array (
            '\test' => 'test',
            '\\test' => 'test',
            '/test' => 'test',
            '//test' => 'test',
            '^test' => 'test',
            'hallo ^test' => 'hallo test',
            'test^' => 'test',
            'test^a' => 'testa',
            'test^0.5' => 'test^0.5',
            'test~' => 'test~',
            'test~en' => 'testen',
            'test~0.5' => 'test~0.5',
            'test~ test' => 'test~ test',
            '+test dies+ +das' => '+test dies +das',
            '-test dies- -das' => '-test dies -das',
            '++test dies++ ++das' => '+test dies +das',
            '--test dies-- --das' => '-test dies -das',
            '!test dies! !das' => 'test dies das',
            'test {dies}' => 'test dies',
            'test [dies]' => 'test dies',
            'test $dies' => 'test dies',
            'test :dies' => 'test dies',
            'AND test dies' => 'test dies',
            'test AND dies' => 'test AND dies',
            'test dies AND' => 'test dies',
            'OR test dies' => 'test dies',
            'test OR dies' => 'test OR dies',
            'test dies OR' => 'test dies',
            '&& test dies' => 'test dies',
            'test && dies' => 'test && dies',
            'test dies &&' => 'test dies',
            '|| test dies' => 'test dies',
            'test || dies' => 'test || dies',
            'test dies ||' => 'test dies',
            '"test dies"' => '"test dies"',
            "'test dies'" => "'test dies'",
            'dies (test das' => 'dies test das',
            'dies test das)' => 'dies test das',
            'dies (test das)' => 'dies (test das)',
            'Blaeser - Benfer' => 'Blaeser Benfer',
            'Blaeser- Benfer' => 'Blaeser Benfer',
            'Blaeser-Benfer' => 'Blaeser-Benfer',
            'Blaeser -' => 'Blaeser',
            '-' => '',
            '--' => '',
            '+' => '',
            '++' => '',
            "\nTest" => 'Test',
            "\rTest" => 'Test',
        );

        foreach ($stringArray as $test => $result)
            $this->assertSame($result, $this->fixture->sanitizeStringLucene($test)
        );
    }

    /**
     * @test
     */
    public function sanitizeStringLuceneGivenStrictTrueReturnsExpectedString() {

        $stringArray = array (
            '\test' => 'test',
            '\\test' => 'test',
            '/test' => 'test',
            '//test' => 'test',
            '^test' => 'test',
            'hallo ^test' => 'hallo test',
            'test^' => 'test',
            'test^a' => 'testa',
            'test^0.5' => 'test',
            'test~' => 'test',
            'test~en' => 'testen',
            'test~0.5' => 'test',
            'test~ test' => 'test test',
            '+test dies+ +das' => 'test dies das',
            '-test dies- -das' => 'test dies das',
            '++test dies++ ++das' => 'test dies das',
            '--test dies-- --das' => 'test dies das',
            '!test dies! !das' => 'test dies das',
            'test {dies}' => 'test dies',
            'test [dies]' => 'test dies',
            'test $dies' => 'test dies',
            'test :dies' => 'test dies',
            'AND test dies' => 'test dies',
            'test AND dies' => 'test dies',
            'test dies AND' => 'test dies',
            'OR test dies' => 'test dies',
            'test OR dies' => 'test dies',
            'test dies OR' => 'test dies',
            '&& test dies' => 'test dies',
            'test && dies' => 'test dies',
            'test dies &&' => 'test dies',
            '|| test dies' => 'test dies',
            'test || dies' => 'test dies',
            'test dies ||' => 'test dies',
            '"test dies"' => 'test dies',
            '"test dies' => 'test dies',
            "'test dies'" => 'test dies',
            "'test dies" => 'test dies',
            'dies (test das' => 'dies test das',
            'dies test das)' => 'dies test das',
            'dies (test das)' => 'dies test das',
            'Blaeser - Benfer' => 'Blaeser Benfer',
            'Blaeser- Benfer' => 'Blaeser Benfer',
            'Blaeser-Benfer' => 'Blaeser-Benfer',
            'Blaeser -' => 'Blaeser',
            '-' => '',
            '--' => '',
            '+' => '',
            '++' => '',
            "\nTest" => 'Test',
            "\rTest" => 'Test',
        );

        foreach ($stringArray as $test => $result)
            $this->assertSame($result, $this->fixture->sanitizeStringLucene($test, TRUE)
        );
    }

    /**
     * @test
     */
    public function sanitizeStringOrientDbReturnsExpectedString() {

        $stringArray = array (
            '\test' => 'test',
            '\\test' => 'test',
            '/test' => 'test',
            '//test' => 'test',
            '^test' => 'test',
            'hallo ^test' => 'hallo test',
            'test^' => 'test',
            'test^a' => 'testa',
            'test^0.5' => 'test',
            'test~' => 'test',
            'test~en' => 'testen',
            'test~0.5' => 'test',
            'test~ test' => 'test test',
            '+test dies+ +das' => 'test dies das',
            '-test dies- -das' => 'test dies das',
            '++test dies++ ++das' => 'test dies das',
            '--test dies-- --das' => 'test dies das',
            '!test dies! !das' => 'test dies das',
            'test {dies}' => 'test dies',
            'test [dies]' => 'test dies',
            'test $dies' => 'test dies',
            'test :dies' => 'test dies',
            'AND test dies' => 'test dies',
            'test AND dies' => 'test dies',
            'test dies AND' => 'test dies',
            'OR test dies' => 'test dies',
            'test OR dies' => 'test dies',
            'test dies OR' => 'test dies',
            '&& test dies' => 'test dies',
            'test && dies' => 'test dies',
            'test dies &&' => 'test dies',
            '|| test dies' => 'test dies',
            'test || dies' => 'test dies',
            'test dies ||' => 'test dies',
            '"test dies"' => 'test dies',
            '"test dies' => 'test dies',
            "'test dies'" => 'test dies',
            "'test dies" => 'test dies',
            'dies (test das' => 'dies test das',
            'dies test das)' => 'dies test das',
            'dies (test das)' => 'dies test das',
            'Blaeser - Benfer' => 'Blaeser Benfer',
            'Blaeser- Benfer' => 'Blaeser Benfer',
            'Blaeser-Benfer' => 'Blaeser-Benfer',
            'Blaeser -' => 'Blaeser',
            '-' => '',
            '--' => '',
            '+' => '',
            '++' => '',
            "\nTest" => 'Test',
            "\rTest" => 'Test',
        );

        foreach ($stringArray as $test => $result)
            $this->assertSame($result, $this->fixture->sanitizeStringOrientDb($test)
        );
    }


    /**
     * @test
     */
    public function encodeGermanUmlautsReturnsExpectedString() {

        $this->assertSame(
            'Das konnte dumm Wasser Apfel Obst Unwesen',
            $this->fixture->encodeGermanUmlauts('Däs könnte dümm Waßer Äpfel Öbst Ünwesen')
        );
    }




    /**
     * @test
     */
    public function sanitizeStringGivenTextWithSpecialCharsReturnsTextWithoutSpecialChars() {

        $this->assertSame(
            'Dies ist ein. Test . mit Link und . ganz speziellen Zeichen , einem ordinären @-Zeichen und mit 5 Mio. sowie einer E-Mail .',
            $this->fixture->sanitizeString('Dies ist www.google.de/test.html ein<br> »Test« — mit Link www.google.de/tester +<br /> ganz http://www.göögle.de/test.html speziellen &quot;Zeichen&quot;, einem ordinären &#64;-Zeichen und mit 5 Mio. € sowie einer E-Mail test@testen.de.', 0, $this->configuration[0]['textFilterRegExpr'])
        );
    }


    /**
     * @test
     */
    public function mergeToStringGivenTwoStringsReturnsMergedStrings() {

        $data = array (
            'firstname' => 'Bernd',
            'middlename' => 'das',
            'lastname' => 'Brot'
        );

        $dataTwo = array (

            array (
                'firstname' => 'Bernd',
                'middlename' => 'das'
            ),

            array (
                'lastname' => 'Brot'
            )
        );

        $this->assertSame("Bernd. das. Brot", $this->fixture->mergeToString($data));
        $this->assertSame("Bernd. das. Brot", $this->fixture->mergeToString($dataTwo));

        $this->assertSame("Bernd das Brot", $this->fixture->mergeToString($data, ' '));
        $this->assertSame("Bernd das Brot", $this->fixture->mergeToString($dataTwo, ' '));
    }


    /**
     * @test
     */
    public function stripHtml() {

        $data = '<p class="test">Dies<br>ist<br />ein total doofer<br/>Test.<br/>Aber wir &quot;müssen das machen&quot;</p>.';

        $this->assertSame("Dies. ist. ein total doofer. Test. Aber wir \"müssen das machen\".", $this->fixture->stripHtml($data));

    }


} 