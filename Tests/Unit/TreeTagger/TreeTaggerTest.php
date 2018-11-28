<?php
namespace RKW\RkwSearch\Tests\TreeTagger;

/**
 * Class TreeTaggerTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class TreeTaggerTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\TreeTagger\TreeTagger
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\TreeTagger\Collection\Records
     */
    protected $fixture1;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $text;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->text = 'Der Beitrag geht auf zwei Aspekte besonders ein.';

        $resultArray = array (
            0 => 'Der' . "\t" . 'ART' . "\t" . 'die',
            1 => 'Beitrag' . "\t" . 'NN' . "\t" . 'Beitrag',
            2 => 'geht' . "\t" . 'VVFIN' . "\t" . 'gehen',
            3 => 'auf' . "\t" . 'APPR' . "\t" . 'auf',
            4 => 'zwei' . "\t" . 'CARD	zwei',
            5 => 'Aspekte' . "\t" . 'NN' . "\t" . 'Aspekt',
            6 => 'besonders' . "\t" . 'ADV' . "\t" . 'besonders',
            7 => 'ein' . "\t" . 'PTKVZ' . "\t" . 'ein',
            8 => '.' . "\t" . '$.' . "\t" . '.'
        );

        $this->data = array (
            'executableCode' => '{PATH}/cmd/utf8-tokenize.perl -a {PATH}/lib/german-abbreviations-utf8 $* | perl {PATH}/cmd/lookup.perl {PATH}/lib/german-lexicon-utf8.txt | {PATH}/bin/tree-tagger -token -lemma -sgml -pt-with-lemma {PATH}/lib/german-utf8.par | {PATH}/cmd/filter-german-tags'
        );


        $configuration = array (

            0 => array (

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

                'filter' => array (

                    'distance' => array (

                        'ignoreCardinalNumbers' => 1,
                        'ignoreWords' => 'Abb, Mrd, Mio',
                        'minWordLength' => 2,

                        'definition' => array (

                            10 => array (
                                'cur' => 'NN',

                                'prev' => 'ADJA',
                                'prevMaxDIstance' => 3,

                                'next' => 'ADJA',
                                'nextMaxDistance' => 3
                            ),

                            20 => array (
                                'cur' => 'NN',

                                'prev' => 'NN',
                                'prevMaxDistance' => 8
                            ),

                            30 => array (
                                'cur' => 'NN',

                                'next' => 'VVFIN',
                                'nextMaxDistance' => 10
                            ),

                            31 => array (
                                'cur' => 'NN',

                                'next' => 'VVINF',
                                'nextMaxDistance' => 10
                            ),

                            32 => array (
                                'cur' => 'NN',

                                'next' => 'VVIZU',
                                'nextMaxDistance' => 10
                            ),

                            33 => array (
                                'cur' => 'NN',

                                'next' => 'VVPP',
                                'nextMaxDistance' => 10
                            ),

                            34 => array (
                                'cur' => 'NN',

                                'next' => 'VAINF',
                                'nextMaxDistance' => 10
                            ),

                            35 => array (
                                'cur' => 'NN',

                                'next' => 'VAPP',
                                'nextMaxDistance' => 10
                            ),
                        )
                    )
                )
            )

        );

        $this->fixture = new \RKW\RkwSearch\TreeTagger\TreeTagger(0, $configuration);
        $this->fixture1 = new \RKW\RkwSearch\TreeTagger\Collection\Records ($resultArray);
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
    public function executableCodeWithDefaultParamsReturnsDefaultCode() {
        $this->assertSame($this->data['executableCode'], $this->fixture->getExecutableCode(TRUE));
    }


    /**
     * @test
     */
    public function setAndGetTextReturnsSetText() {
        $this->assertSame('Test', $this->fixture->setText('Test')->getText());
    }

    /**
     * @test
     */
    public function setAndGetTextWithSpecialCharsReturnsSetTextWithoutSpecialChars() {

        $this->assertSame('Dies ist ein. Test . mit Link und . ganz speziellen Zeichen , einem ordinären @-Zeichen und mit 5 Mio. sowie einer E-Mail .', $this->fixture->setText('Dies ist www.google.de/test.html ein<br> »Test« — mit Link www.google.de/tester +<br /> ganz http://www.göögle.de/test.html speziellen &quot;Zeichen&quot;, einem ordinären &#64;-Zeichen und mit 5 Mio. € sowie einer E-Mail test@testen.de.')->getText());

    }


    /**
     * @test
     */
    public function getTextStemmedReturnsStemmedText() {

        $this->fixture->setText($this->text)->execute();
        $this->assertSame('die Beitrag gehen auf zwei Aspekt besonders ein.', $this->fixture->getTextStemmed());

    }



    /**
     * @test
     */
    public function getResultsReturnsTreeTaggerCollectionRecordsInstance() {
        $this->assertInstanceOf('\RKW\RkwSearch\TreeTagger\Collection\Records', $this->fixture->setText($this->text)->execute()->getResults());
    }

    /**
     * @test
     */
    public function getResultsReturnsTreeTaggerCollectionRecordsInstanceWithExpectedData() {

        $this->assertEquals($this->fixture1, $this->fixture->setText($this->text)->execute()->getResults());
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getFilteredResultsGivenWrongFilterNameThrowsException() {
        $this->assertInternalType('array', $this->fixture->getFilteredResults('test'));
    }

    /**
     * @test
     */
    public function getFilteredResultsGivenFilterReturnsInstanceOfCollectionFilteredRecords() {
        $this->assertInstanceOf('RKW\\RkwSearch\\TreeTagger\\Collection\\FilteredRecords', $this->fixture->getFilteredResults('distance'));
    }

    /**
     * @test
     */
    public function executeReturnsSelfInstance() {
        $this->assertInstanceOf(get_class($this->fixture), $this->fixture->setText($this->text)->execute());
    }


    /**
     * @test
     */
    public function getSplitTextReturnsExpectedSplitArray() {
        $this->fixture->setText('RKW Kompetenzzentrum. Das RKW Kompetenzzentrum ist eine gemeinnützige Forschungs- und Entwicklungseinrichtung und bundesweit aktiv. In Eschborn arbeiten rund 80 MitarbeiterInnen und erforschen, wie sich kleine und mittlere Unternehmen in Deutschland im internationalen Wettbewerb behaupten können. Die Erkenntnisse fliessen in praxisnahe Empfehlungen ein, die wir kostenlos verbreiteten. Dafür werden wir vom Bundesministerium für Wirtschaft und Energie aufgrund eines Beschlusses des deutschen Bundestages gefördert. Das Projekt Entrepreneurship Education. Mit Veranstaltungen, Workshops und Beispielen guter Praxis wollen wir bei Jugendlichen sowohl die Wahrnehmung der Selbständigkeit als berufliche Alternative verbessern als auch das ökonomische Wissen ausbauen. Internationale Vergleiche wie der Global Entrepreneurship Monitor zeigen: In Deutschland ist eine unternehmerische Initiative unter Jugendlichen eher gering ausgeprägt. Damit kann ein besonderer Handlungsbedarf bei der gründungsbezogenen schulischen Ausbildung abgeleitet werden. Ziele. In diesem Projekt soll dargestellt werden, welche Erfahrung SchülerInnen mit Entrepreneurship-Education-Projekten machen und was ihnen die Teilnahme an diesen Projekten – auch nachhaltig – bringt. Mit den Erfahrungsberichten sollen Lehrkräfte dafür gewonnen werden, schulische Entrepreneurship-Education-Projekte in ihren Berufsalltag zu integrieren. Vor allem, wenn sie sehen, welche Vorteile die Projektteilnahme für ihre SchülerInnen bringen kann. Workshops und Veranstaltungen sollen Lehrkräfte sowie Lehramtsstudierende sensibilisieren, aber auch dazu befähigen, Projekte und Aktionen selbst durchzuführen, die Unternehmergeist für SchülerInnen erlebbar machen. Die Jugendlichen sollen motiviert werden, an Projekten zum Thema Entrepreneurship Education teilzunehmen. Sie sollen damit die Fähigkeiten erwerben, in wirtschaftlichen Zusammenhängen kompetent und mündig zu agieren, Eigeninitiative zu zeigen, Kreativität zu erleben, Verantwortung für andere zu übernehmen und im Team zu arbeiten. Zielgruppen des Projektes sind SchülerInnen, Lehrkräfte und Lehramtsstudierende, Massnahmen zur Umsetzung der Projektziele sind Veranstaltungen, Workshops und die Erarbeitung von Beispielen guter Praxis. Unternehmergeist in die Schulen – Fortbildungsveranstaltungen für Lehramtsstudierende in Baden-Württemberg. Im Rahmen des Projektes richtet das RKW Kompetenzzentrum in Kooperation mit dem Bundesministerium für Wirtschaft und Energie und der Initiative für Existenzgründungen und Unternehmensnachfolge – ifex des Ministeriums für Finanzen und Wirtschaft Baden-Württemberg Fortbildungsveranstaltungen für Lehramtsstudierende in Baden-Württemberg aus. Die Veranstaltungen werden durch die Karl Schlecht Stiftung unterstützt. Die in Aichtal bei Stuttgart ansässige Stiftung engagiert sich ab 2015 zunehmend im Bereich Entrepreneurship. Mitglied im Initiativkreis Unternehmergeist in die Schulen. Der vom Bundesministerium für Wirtschaft und Energie moderierte Initiativkreis Unternehmergeist in die Schulen hat es sich zum Ziel gesetzt, das Thema Unternehmensgründung nachhaltig an den Schulen in Deutschland zu etablieren. Das RKW Kompetenzzentrum ist seit Anfang 2014 Mitglied des Initiativkreises. Gründerwoche Deutschland. Gründerwoche Deutschland. Was ist die Gründerwoche Deutschland?. Die Gründerwoche Deutschland ist eine bundesweite Aktion unter der Federführung des Bundesministeriums für Wirtschaft und Energie. Die Gründerwoche Deutschland ist der deutsche Beitrag zur Global Entrepreneurship Week : Die weltweite Initiative will für Gründung und Unternehmertum sensibilisieren und dabei die Entwicklung von innovativen Ideen und unternehmerisches Denken und Handeln fördern. Alles über die GEW finden Sie auf Warum gibt es die Gründerwoche Deutschland?. Unternehmensgründungen sorgen für Fortschritt und Wachstum. Sie stehen für Kreativität, unternehmerische Entfaltung und schaffen Arbeitsplätze. Die Gründerwoche Deutschland will deshalb für eine neue Gründungskultur und ein freundliches Gründungsklima in Deutschland motivieren, inspirieren und über die Perspektiven der beruflichen Selbständigkeit informieren. An wen richtet sich die Gründerwoche Deutschland? Wer profitiert davon?. Die Zielgruppen der Gründerwoche sind in erster Linie junge Menschen: SchülerInnen, Auszubildende, Studierende und junge GründerInnen. Sie sollen an die Themen Existenzgründung und Unternehmertum herangeführt werden, erhalten praxisnahes Wissen rund um Gründung und unternehmerische Kompetenz. Wer setzt die Gründerwoche in die Praxis um?. Für die erfolgreiche Umsetzung der Gründerwoche ist das Engagement vieler Gründungsakteure und Förderer von Unternehmergeist gefragt, die sich als Sekundarschulen, Beruflichen Schulen, Förderschulen sowie Gymnasien. Für die hohe Qualität der Ausbildung sorgt ein Team qualifizierter Fachleute aus Wissenschaft, Pädagogik und Wirtschaft. Inhalte sind unter anderem:. Was ist ein Entrepreneur/Unternehmer? Was macht ihn erfolgreich?, Unternehmerische Chancen erkennen, Verdeutlichung sozial-ökologischer Verantwortung, Von Talenten und Hobbys zur Branchenwahl, Produkt und Dienstleistung, Marketing und Wettbewerbsvorteil, Gewinnund Verlustrechnung, Finanzierungsstrategien / Erstellung und Präsentation eines Businessplans. Im Mittelpunkt der Vermittlung steht eine Pädagogik der Ermutigung und individuellen Förderung. Durch die Arbeit an den Stärken der SchülerInnen werden diese zu Akteuren. Die Lehrkräfte lernen kreative Spiele als methodisch-didaktisches Instrumentarium kennen und erhalten Hinweise zur Implementierung der NFTE-Inhalte im Unterricht. NFTE und nachhaltige Schülerfirmen. Vorgeschaltete NFTE Kurse bewähren sich bereits in vielen Schulen als Impulsgeber und wirksame Basis für nachhaltige Schülerfirmen. Die Schülerfirmen können nach einem NFTE Kurs auf motivierte und wirtschaftlich bereits befähigte SchülerInnen zurückgreifen. Diese können ihre Interessen, Stärken und Schwächen besser einschätzen und bringen vor allem eigene Ideen ein. So können ganz neue und vielfältigere Schülerfirmen entstehen und bestehende bekommen qualifizierteres Personal. Die Unterrichtsmaterialien. Die unmittelbar erlebten Erfahrungen in den Trainings sowie arbeitssparend aufbereitete Unterrichtsmaterialien erleichtern den zertifizierten LehrerInnen die Durchführung des Unterrichts. Den LehrerInnen stehen zur Ausgestaltung des NFTE Kurses mit dem Schülerbuch, einem darauf abgestimmten Praxisheft mit passenden Übungsaufgaben und dem Lehrerbegleitheft didaktisch-methodisch sorgfältig ausgearbeitete Lernmaterialien zur Verfügung, die nach dem Training unmittelbar eingesetzt werden können. Bei Bedarf steht ihnen während der Durchführung des Kurses das NFTE Team beratend zur Seite. Zusätzlich werden jährliche Treffen zum Erfahrungsaustausch angeboten. Die Entlastung und der methodisch-didaktische Zugewinn. NFTE bietet LehrerInnen neue Impulse, Entlastung und zugleich Bereicherung für den Unterricht. Am meisten schätzen die TeilnehmerInnen der Fortbildungen den grossen methodisch-didaktischen Zugewinn: Durch einen handlungsorientierten Unterricht mit vielen lebendigen Spielen, der den neuesten Erkenntnissen der Gehirnforschung Rechnung trägt, können sie künftig auch das Interesse von zuvor noch wenig motivierten, perspektivlosen Jugendlichen für Wirtschaftsthemen wecken – aber auch solche SchülerInnen ansprechen, denen bereits eine mögliche spätere Selbstständigkeit vorschwebt.');
        $expected = array (
            'RKW Kompetenzzentrum. Das RKW Kompetenzzentrum ist eine gemeinnützige Forschungs- und Entwicklungseinrichtung und bundesweit aktiv. In Eschborn arbeiten rund 80 MitarbeiterInnen und erforschen, wie sich kleine und mittlere Unternehmen in Deutschland im internationalen Wettbewerb behaupten können. Die Erkenntnisse fliessen in praxisnahe Empfehlungen ein, die wir kostenlos verbreiteten. Dafür werden wir vom Bundesministerium für Wirtschaft und Energie aufgrund eines Beschlusses des deutschen Bundestages gefördert. Das Projekt Entrepreneurship Education. Mit Veranstaltungen, Workshops und Beispielen guter Praxis wollen wir bei Jugendlichen sowohl die Wahrnehmung der Selbständigkeit als berufliche Alternative verbessern als auch das ökonomische Wissen ausbauen. Internationale Vergleiche wie der Global Entrepreneurship Monitor zeigen: In Deutschland ist eine unternehmerische Initiative unter Jugendlichen eher gering ausgeprägt. Damit kann ein besonderer Handlungsbedarf bei der gründungsbezogenen schulischen Ausbildung abgeleitet werden. Ziele. In diesem Projekt soll dargestellt werden, welche Erfahrung SchülerInnen mit Entrepreneurship-Education-Projekten machen und was ihnen die Teilnahme an diesen Projekten – auch nachhaltig – bringt. Mit den Erfahrungsberichten sollen Lehrkräfte dafür gewonnen werden, schulische Entrepreneurship-Education-Projekte in ihren Berufsalltag zu integrieren. Vor allem, wenn sie sehen, welche Vorteile die Projektteilnahme für ihre SchülerInnen bringen kann. Workshops und Veranstaltungen sollen Lehrkräfte sowie Lehramtsstudierende sensibilisieren, aber auch dazu befähigen, Projekte und Aktionen selbst durchzuführen, die Unternehmergeist für SchülerInnen erlebbar machen. Die Jugendlichen sollen motiviert werden, an Projekten zum Thema Entrepreneurship Education teilzunehmen. Sie sollen damit die Fähigkeiten erwerben, in wirtschaftlichen Zusammenhängen kompetent und mündig zu agieren, Eigeninitiative zu zeigen, Kreativität zu erleben, Verantwortung für andere zu übernehmen und im Team zu arbeiten. Zielgruppen des Projektes sind SchülerInnen, Lehrkräfte und Lehramtsstudierende, Massnahmen zur Umsetzung der Projektziele sind Veranstaltungen, Workshops und die Erarbeitung von Beispielen guter Praxis. Unternehmergeist in die Schulen – Fortbildungsveranstaltungen für Lehramtsstudierende in Baden-Württemberg. Im Rahmen des Projektes richtet das RKW Kompetenzzentrum in Kooperation mit dem Bundesministerium für Wirtschaft und Energie und der Initiative für Existenzgründungen und Unternehmensnachfolge – ifex des Ministeriums für Finanzen und Wirtschaft Baden-Württemberg Fortbildungsveranstaltungen für Lehramtsstudierende in Baden-Württemberg aus. Die Veranstaltungen werden durch die Karl Schlecht Stiftung unterstützt. Die in Aichtal bei Stuttgart ansässige Stiftung engagiert sich ab 2015 zunehmend im Bereich Entrepreneurship. Mitglied im Initiativkreis Unternehmergeist in die Schulen. Der vom Bundesministerium für Wirtschaft und Energie moderierte Initiativkreis Unternehmergeist in die Schulen hat es sich zum Ziel gesetzt, das Thema Unternehmensgründung nachhaltig an den Schulen in Deutschland zu etablieren. Das RKW Kompetenzzentrum ist seit Anfang 2014 Mitglied des Initiativkreises. Gründerwoche Deutschland. Gründerwoche Deutschland. Was ist die Gründerwoche Deutschland?. Die Gründerwoche Deutschland ist eine bundesweite Aktion unter der Federführung des Bundesministeriums für Wirtschaft und Energie. Die Gründerwoche Deutschland ist der deutsche Beitrag zur Global Entrepreneurship Week : Die weltweite Initiative will für Gründung und Unternehmertum sensibilisieren und dabei die Entwicklung von innovativen Ideen und unternehmerisches Denken und Handeln fördern. Alles über die GEW finden Sie auf Warum gibt es die Gründerwoche Deutschland?. Unternehmensgründungen sorgen für Fortschritt und Wachstum. Sie stehen für Kreativität, unternehmerische Entfaltung',
            'und schaffen Arbeitsplätze. Die Gründerwoche Deutschland will deshalb für eine neue Gründungskultur und ein freundliches Gründungsklima in Deutschland motivieren, inspirieren und über die Perspektiven der beruflichen Selbständigkeit informieren. An wen richtet sich die Gründerwoche Deutschland? Wer profitiert davon?. Die Zielgruppen der Gründerwoche sind in erster Linie junge Menschen: SchülerInnen, Auszubildende, Studierende und junge GründerInnen. Sie sollen an die Themen Existenzgründung und Unternehmertum herangeführt werden, erhalten praxisnahes Wissen rund um Gründung und unternehmerische Kompetenz. Wer setzt die Gründerwoche in die Praxis um?. Für die erfolgreiche Umsetzung der Gründerwoche ist das Engagement vieler Gründungsakteure und Förderer von Unternehmergeist gefragt, die sich als Sekundarschulen, Beruflichen Schulen, Förderschulen sowie Gymnasien. Für die hohe Qualität der Ausbildung sorgt ein Team qualifizierter Fachleute aus Wissenschaft, Pädagogik und Wirtschaft. Inhalte sind unter anderem:. Was ist ein Entrepreneur/Unternehmer? Was macht ihn erfolgreich?, Unternehmerische Chancen erkennen, Verdeutlichung sozial-ökologischer Verantwortung, Von Talenten und Hobbys zur Branchenwahl, Produkt und Dienstleistung, Marketing und Wettbewerbsvorteil, Gewinnund Verlustrechnung, Finanzierungsstrategien / Erstellung und Präsentation eines Businessplans. Im Mittelpunkt der Vermittlung steht eine Pädagogik der Ermutigung und individuellen Förderung. Durch die Arbeit an den Stärken der SchülerInnen werden diese zu Akteuren. Die Lehrkräfte lernen kreative Spiele als methodisch-didaktisches Instrumentarium kennen und erhalten Hinweise zur Implementierung der NFTE-Inhalte im Unterricht. NFTE und nachhaltige Schülerfirmen. Vorgeschaltete NFTE Kurse bewähren sich bereits in vielen Schulen als Impulsgeber und wirksame Basis für nachhaltige Schülerfirmen. Die Schülerfirmen können nach einem NFTE Kurs auf motivierte und wirtschaftlich bereits befähigte SchülerInnen zurückgreifen. Diese können ihre Interessen, Stärken und Schwächen besser einschätzen und bringen vor allem eigene Ideen ein. So können ganz neue und vielfältigere Schülerfirmen entstehen und bestehende bekommen qualifizierteres Personal. Die Unterrichtsmaterialien. Die unmittelbar erlebten Erfahrungen in den Trainings sowie arbeitssparend aufbereitete Unterrichtsmaterialien erleichtern den zertifizierten LehrerInnen die Durchführung des Unterrichts. Den LehrerInnen stehen zur Ausgestaltung des NFTE Kurses mit dem Schülerbuch, einem darauf abgestimmten Praxisheft mit passenden Übungsaufgaben und dem Lehrerbegleitheft didaktisch-methodisch sorgfältig ausgearbeitete Lernmaterialien zur Verfügung, die nach dem Training unmittelbar eingesetzt werden können. Bei Bedarf steht ihnen während der Durchführung des Kurses das NFTE Team beratend zur Seite. Zusätzlich werden jährliche Treffen zum Erfahrungsaustausch angeboten. Die Entlastung und der methodisch-didaktische Zugewinn. NFTE bietet LehrerInnen neue Impulse, Entlastung und zugleich Bereicherung für den Unterricht. Am meisten schätzen die TeilnehmerInnen der Fortbildungen den grossen methodisch-didaktischen Zugewinn: Durch einen handlungsorientierten Unterricht mit vielen lebendigen Spielen, der den neuesten Erkenntnissen der Gehirnforschung Rechnung trägt, können sie künftig auch das Interesse von zuvor noch wenig motivierten, perspektivlosen Jugendlichen für Wirtschaftsthemen wecken – aber auch solche SchülerInnen ansprechen, denen bereits eine mögliche spätere Selbstständigkeit vorschwebt.'
        );

        $this->assertEquals($expected, $this->fixture->getSplitText());
    }

    /**
     * @test
     */
    public function getCacheReturnsInstanceOfTreeTaggerCache() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Cache\\TreeTaggerCache', $this->fixture->getCache());

    }



} 