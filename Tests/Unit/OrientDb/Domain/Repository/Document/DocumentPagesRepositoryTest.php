<?php
namespace RKW\RkwSearch\Tests\OrientDb\Domain\Repository\Document;
use RKW\RkwSearch\OrientDb\Helper\Common;

/**
 * Class DocumentPagesRepositoryTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DocumentPagesRepositoryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentPagesRepository
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentAuthorsRepository
     */
    protected $fixtureTwo;
    

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
     */
    protected $object;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentAuthors
     */
    protected $objectTwo;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
     */
    protected $objectThree;



    /**
     * Set up fixture
     */
    public function setUp() {

        $dataOne = array (
            'uid' => rand(201,300) + time(),

            'tstamp'  => time(),
            'crdate' => time(),
            'hidden' => 0,
            'deleted' => 0,
            'sorting' => 0,
            'sys_language_uid' => 2,
            'endtime' => 0,
            'starttime' => 0,

            'content' => 'Test',
            'doktype' => 1,
            'title' => 'Test',
            'subtitle' => 'Test Test',
            'abstract' => 'Test',
            'description' => 'Test',
            'keywords' => 'Test',
            'topKeywords' => 'a:50:{s:26:"in design für alle planen";d:0.30358803701764725;s:31:"vorfeld an entscheidungsprozess";d:0.29667133341072349;s:49:"an jede beliebig stelle mit die lektüre beginnen";d:0.29667133341072349;s:20:"anbieter von produkt";d:0.29667133341072349;s:6:"design";d:0.29667133341072349;s:50:"entwickler und anbieter von produkt dienstleistung";d:0.29428963137317726;s:49:"anbieter von produkt und dienstleistung in design";d:0.29381677168139381;s:56:"bürger in vorfeld an entscheidungsprozess und erreichen";d:0.28076815692081519;s:41:"beliebig stelle mit die lektüre beginnen";d:0.27715858213737637;s:62:"von produkt und dienstleistung in design für alle unternehmen";d:0.27616899752418711;s:93:"bürger in vorfeld an entscheidungsprozess und erreichen grösstmöglich nutzerfreundlichkeit";d:0.26925695345313261;s:60:"entwickler und anbieter von produkt dienstleistung in design";d:0.26794477589888249;s:50:"grösstmöglich nutzerfreundlichkeit und akzeptanz";d:0.26054726677578838;s:48:"unternehmen eine wesentlich wettbewerbsvorsprung";d:0.26054726677578838;s:68:"in design für alle unternehmen eine wesentlich wettbewerbsvorsprung";d:0.2601663529151324;s:51:"mit die konzept design für alle praktisch umsetzen";d:0.2601663529151324;s:56:"unternehmen neben gut denkanstoss instrument an die hand";d:0.2601663529151324;s:62:"unternehmen neben gut denkanstoss instrument an die hand geben";d:0.25697559050606344;s:40:"denkanstoss instrument an die hand geben";d:0.25041468753468082;s:29:"produkt dienstleistung design";d:0.2426972944468776;s:27:"entwickler anbieter produkt";d:0.2426972944468776;s:31:"anbieter produkt dienstleistung";d:0.2426972944468776;s:35:"entwickler und anbieter von produkt";d:0.24192505234563347;s:39:"an die bedürfnis wandelnd gesellschaft";d:0.24192505234563347;s:40:"angebot gehen über pur barrierefreiheit";d:0.24192505234563347;s:49:"ziel unternehmen neben gut denkanstoss instrument";d:0.23974617887690081;s:11:"unternehmen";d:0.23510646924747014;s:18:"sicherheit komfort";d:0.23510646924747011;s:16:"anbieter produkt";d:0.23510646924747011;s:30:"nutzerfreundlichkeit akzeptanz";d:0.23510646924747011;s:22:"produkt dienstleistung";d:0.23510646924747011;s:14:"konzept design";d:0.23510646924747011;s:28:"vorfeld entscheidungsprozess";d:0.23510646924747011;s:21:"dienstleistung design";d:0.23510646924747011;s:19:"entwickler anbieter";d:0.23510646924747011;s:22:"denkanstoss instrument";d:0.23510646924747011;s:49:"sicherheit und komfort mit ästhetisch gestaltung";d:0.2304090131715264;s:81:"entscheidungsprozess und erreichen grösstmöglich nutzerfreundlichkeit akzeptanz";d:0.2304090131715264;s:32:"bedürfnis wandelnd gesellschaft";d:0.20965616357299244;s:34:"unternehmen denkanstoss instrument";d:0.20965616357299244;s:29:"sicherheit komfort gestaltung";d:0.20965616357299244;s:27:"denkanstoss instrument hand";d:0.20965616357299244;s:36:"bürger vorfeld entscheidungsprozess";d:0.20965616357299244;s:42:"bürger in vorfeld an entscheidungsprozess";d:0.19908735810536721;s:33:"entwickler produkt dienstleistung";d:0.18717877128054344;s:30:"anbieter dienstleistung design";d:0.18717877128054344;s:23:"anbieter produkt design";d:0.18717877128054344;s:34:"entwickler anbieter dienstleistung";d:0.18717877128054344;s:33:"dienstleistung design unternehmen";d:0.18717877128054344;s:28:"ziel unternehmen denkanstoss";d:0.18717877128054344;}',
            'searchContent' => 'RAUM für ALLE. RAUM für ALLE (DfA-Reader). Die Publikation ist so angelegt, dass jeder Beitrag in sich geschlossen ist. Sie können also an jeder beliebigen Stelle mit der Lektüre beginnen. Ziel ist es Unternehmen neben guten Denkanstössen auch Instrumente an die Hand zu geben, mit denen sie das Konzept „Design für Alle“ praktisch umsetzen können. Kommunen, die im Design für Alle planen, gestalten nachhaltig und orientieren sich an den Bedürfnissen unserer sich wandelnden Gesellschaft. Sie beteiligen die Bürger schon im Vorfeld an Entscheidungsprozessen und erreichen so grösstmögliche Nutzerfreundlichkeit und Akzeptanz. Als Entwickler und Anbieter von Produkten und Dienstleistungen im Design für Alle haben Unternehmen einen wesentlichen Wettbewerbsvorsprung. Denn ihre Angebote gehen über pure Barrierefreiheit hinaus; sie verbinden Sicherheit und Komfort mit ästhetischer Gestaltung.',
            'searchContentFuzzy' => 'r76 f37 a05 r76 f37 a05 d23727 d2 p15426 i082 s8 a064542 d28 j027 b1274 i06 s84 g48586 i082 s8 k466 a058 a06 j027 b15146 s825 m62 d27 l5427 b1466 z85 i082 e08 u0627666 n616 g426 d26468286 a04 i06827662 a06 d2 h062 z8 g416 m62 d266 s8 d28 k4681 d2846 f37 a05 p1428 u06886 k466 k4666 d2 i06 d2846 f37 a05 p166 g482526 n64524 u062 o076276 s84 a06 d26 b1273686 u06877 s84 w3625626 g485832 s8 b12546 d2 b1747 s86 i06 v37352 a06 e06826481886 u062 e0746 s8 g47826454 n6873762542 u062 a048168 a058 e0623457 u062 a06127 v36 p12426 u062 d2682582646 i06 d2846 f37 a05 h016 u0627666 e066 w3862546 w3213718378164 d26 i07 a06412 g46 u017 p17 b177372 h068 s8 v371626 s8472 u062 k46372 m62 a082287 g4825264',
            'searchContentType' => 'default',
            'searchContentSize' => 'small',
        );


        $dataTwo = array (
            'uid' => rand(201,300) + time(),

            'tstamp'  => time(),
            'crdate' => time(),
            'hidden' => 0,
            'deleted' => 0,
            'sorting' => 0,
            'sys_language_uid' => 0,
            'endtime' => 0,
            'starttime' => 0,

            'firstname' => 'Test',
            'lastname' => 'Test',
            'titleBefore' => 'Test',
            'titleAfter' => 'Test',
            'topKeywords' => 'a:50:{s:26:"in design für alle planen";d:0.30358803701764725;s:31:"vorfeld an entscheidungsprozess";d:0.29667133341072349;s:49:"an jede beliebig stelle mit die lektüre beginnen";d:0.29667133341072349;s:20:"anbieter von produkt";d:0.29667133341072349;s:6:"design";d:0.29667133341072349;s:50:"entwickler und anbieter von produkt dienstleistung";d:0.29428963137317726;s:49:"anbieter von produkt und dienstleistung in design";d:0.29381677168139381;s:56:"bürger in vorfeld an entscheidungsprozess und erreichen";d:0.28076815692081519;s:41:"beliebig stelle mit die lektüre beginnen";d:0.27715858213737637;s:62:"von produkt und dienstleistung in design für alle unternehmen";d:0.27616899752418711;s:93:"bürger in vorfeld an entscheidungsprozess und erreichen grösstmöglich nutzerfreundlichkeit";d:0.26925695345313261;s:60:"entwickler und anbieter von produkt dienstleistung in design";d:0.26794477589888249;s:50:"grösstmöglich nutzerfreundlichkeit und akzeptanz";d:0.26054726677578838;s:48:"unternehmen eine wesentlich wettbewerbsvorsprung";d:0.26054726677578838;s:68:"in design für alle unternehmen eine wesentlich wettbewerbsvorsprung";d:0.2601663529151324;s:51:"mit die konzept design für alle praktisch umsetzen";d:0.2601663529151324;s:56:"unternehmen neben gut denkanstoss instrument an die hand";d:0.2601663529151324;s:62:"unternehmen neben gut denkanstoss instrument an die hand geben";d:0.25697559050606344;s:40:"denkanstoss instrument an die hand geben";d:0.25041468753468082;s:29:"produkt dienstleistung design";d:0.2426972944468776;s:27:"entwickler anbieter produkt";d:0.2426972944468776;s:31:"anbieter produkt dienstleistung";d:0.2426972944468776;s:35:"entwickler und anbieter von produkt";d:0.24192505234563347;s:39:"an die bedürfnis wandelnd gesellschaft";d:0.24192505234563347;s:40:"angebot gehen über pur barrierefreiheit";d:0.24192505234563347;s:49:"ziel unternehmen neben gut denkanstoss instrument";d:0.23974617887690081;s:11:"unternehmen";d:0.23510646924747014;s:18:"sicherheit komfort";d:0.23510646924747011;s:16:"anbieter produkt";d:0.23510646924747011;s:30:"nutzerfreundlichkeit akzeptanz";d:0.23510646924747011;s:22:"produkt dienstleistung";d:0.23510646924747011;s:14:"konzept design";d:0.23510646924747011;s:28:"vorfeld entscheidungsprozess";d:0.23510646924747011;s:21:"dienstleistung design";d:0.23510646924747011;s:19:"entwickler anbieter";d:0.23510646924747011;s:22:"denkanstoss instrument";d:0.23510646924747011;s:49:"sicherheit und komfort mit ästhetisch gestaltung";d:0.2304090131715264;s:81:"entscheidungsprozess und erreichen grösstmöglich nutzerfreundlichkeit akzeptanz";d:0.2304090131715264;s:32:"bedürfnis wandelnd gesellschaft";d:0.20965616357299244;s:34:"unternehmen denkanstoss instrument";d:0.20965616357299244;s:29:"sicherheit komfort gestaltung";d:0.20965616357299244;s:27:"denkanstoss instrument hand";d:0.20965616357299244;s:36:"bürger vorfeld entscheidungsprozess";d:0.20965616357299244;s:42:"bürger in vorfeld an entscheidungsprozess";d:0.19908735810536721;s:33:"entwickler produkt dienstleistung";d:0.18717877128054344;s:30:"anbieter dienstleistung design";d:0.18717877128054344;s:23:"anbieter produkt design";d:0.18717877128054344;s:34:"entwickler anbieter dienstleistung";d:0.18717877128054344;s:33:"dienstleistung design unternehmen";d:0.18717877128054344;s:28:"ziel unternehmen denkanstoss";d:0.18717877128054344;}',
            'searchContentType' => 'default',
            'searchContentSize' => 'small',

        );

        $dataThree = array (
            'uid' => rand(201,300) + time(),

            'tstamp'  => time(),
            'crdate' => time(),
            'hidden' => 0,
            'deleted' => 0,
            'sorting' => 0,
            'sys_language_uid' => 0,
            'endtime' => 0,
            'starttime' => 0,

            'content' => 'Test',
            'doktype' => 1,
            'title' => 'Test',
            'subtitle' => 'Test Test',
            'abstract' => 'Test',
            'description' => 'Test',
            'keywords' => 'Test',
            'topKeywords' => 'a:50:{s:26:"in design für alle planen";d:0.30358803701764725;s:31:"vorfeld an entscheidungsprozess";d:0.29667133341072349;s:49:"an jede beliebig stelle mit die lektüre beginnen";d:0.29667133341072349;s:20:"anbieter von produkt";d:0.29667133341072349;s:6:"design";d:0.29667133341072349;s:50:"entwickler und anbieter von produkt dienstleistung";d:0.29428963137317726;s:49:"anbieter von produkt und dienstleistung in design";d:0.29381677168139381;s:56:"bürger in vorfeld an entscheidungsprozess und erreichen";d:0.28076815692081519;s:41:"beliebig stelle mit die lektüre beginnen";d:0.27715858213737637;s:62:"von produkt und dienstleistung in design für alle unternehmen";d:0.27616899752418711;s:93:"bürger in vorfeld an entscheidungsprozess und erreichen grösstmöglich nutzerfreundlichkeit";d:0.26925695345313261;s:60:"entwickler und anbieter von produkt dienstleistung in design";d:0.26794477589888249;s:50:"grösstmöglich nutzerfreundlichkeit und akzeptanz";d:0.26054726677578838;s:48:"unternehmen eine wesentlich wettbewerbsvorsprung";d:0.26054726677578838;s:68:"in design für alle unternehmen eine wesentlich wettbewerbsvorsprung";d:0.2601663529151324;s:51:"mit die konzept design für alle praktisch umsetzen";d:0.2601663529151324;s:56:"unternehmen neben gut denkanstoss instrument an die hand";d:0.2601663529151324;s:62:"unternehmen neben gut denkanstoss instrument an die hand geben";d:0.25697559050606344;s:40:"denkanstoss instrument an die hand geben";d:0.25041468753468082;s:29:"produkt dienstleistung design";d:0.2426972944468776;s:27:"entwickler anbieter produkt";d:0.2426972944468776;s:31:"anbieter produkt dienstleistung";d:0.2426972944468776;s:35:"entwickler und anbieter von produkt";d:0.24192505234563347;s:39:"an die bedürfnis wandelnd gesellschaft";d:0.24192505234563347;s:40:"angebot gehen über pur barrierefreiheit";d:0.24192505234563347;s:49:"ziel unternehmen neben gut denkanstoss instrument";d:0.23974617887690081;s:11:"unternehmen";d:0.23510646924747014;s:18:"sicherheit komfort";d:0.23510646924747011;s:16:"anbieter produkt";d:0.23510646924747011;s:30:"nutzerfreundlichkeit akzeptanz";d:0.23510646924747011;s:22:"produkt dienstleistung";d:0.23510646924747011;s:14:"konzept design";d:0.23510646924747011;s:28:"vorfeld entscheidungsprozess";d:0.23510646924747011;s:21:"dienstleistung design";d:0.23510646924747011;s:19:"entwickler anbieter";d:0.23510646924747011;s:22:"denkanstoss instrument";d:0.23510646924747011;s:49:"sicherheit und komfort mit ästhetisch gestaltung";d:0.2304090131715264;s:81:"entscheidungsprozess und erreichen grösstmöglich nutzerfreundlichkeit akzeptanz";d:0.2304090131715264;s:32:"bedürfnis wandelnd gesellschaft";d:0.20965616357299244;s:34:"unternehmen denkanstoss instrument";d:0.20965616357299244;s:29:"sicherheit komfort gestaltung";d:0.20965616357299244;s:27:"denkanstoss instrument hand";d:0.20965616357299244;s:36:"bürger vorfeld entscheidungsprozess";d:0.20965616357299244;s:42:"bürger in vorfeld an entscheidungsprozess";d:0.19908735810536721;s:33:"entwickler produkt dienstleistung";d:0.18717877128054344;s:30:"anbieter dienstleistung design";d:0.18717877128054344;s:23:"anbieter produkt design";d:0.18717877128054344;s:34:"entwickler anbieter dienstleistung";d:0.18717877128054344;s:33:"dienstleistung design unternehmen";d:0.18717877128054344;s:28:"ziel unternehmen denkanstoss";d:0.18717877128054344;}',
            'searchContent' => 'RAUM für ALLE. RAUM für ALLE (DfA-Reader). Die Publikation ist so angelegt, dass jeder Beitrag in sich geschlossen ist. Sie können also an jeder beliebigen Stelle mit der Lektüre beginnen. Ziel ist es Unternehmen neben guten Denkanstössen auch Instrumente an die Hand zu geben, mit denen sie das Konzept „Design für Alle“ praktisch umsetzen können. Kommunen, die im Design für Alle planen, gestalten nachhaltig und orientieren sich an den Bedürfnissen unserer sich wandelnden Gesellschaft. Sie beteiligen die Bürger schon im Vorfeld an Entscheidungsprozessen und erreichen so grösstmögliche Nutzerfreundlichkeit und Akzeptanz. Als Entwickler und Anbieter von Produkten und Dienstleistungen im Design für Alle haben Unternehmen einen wesentlichen Wettbewerbsvorsprung. Denn ihre Angebote gehen über pure Barrierefreiheit hinaus; sie verbinden Sicherheit und Komfort mit ästhetischer Gestaltung.',
            'searchContentFuzzy' => 'r76 f37 a05 r76 f37 a05 d23727 d2 p15426 i082 s8 a064542 d28 j027 b1274 i06 s84 g48586 i082 s8 k466 a058 a06 j027 b15146 s825 m62 d27 l5427 b1466 z85 i082 e08 u0627666 n616 g426 d26468286 a04 i06827662 a06 d2 h062 z8 g416 m62 d266 s8 d28 k4681 d2846 f37 a05 p1428 u06886 k466 k4666 d2 i06 d2846 f37 a05 p166 g482526 n64524 u062 o076276 s84 a06 d26 b1273686 u06877 s84 w3625626 g485832 s8 b12546 d2 b1747 s86 i06 v37352 a06 e06826481886 u062 e0746 s8 g47826454 n6873762542 u062 a048168 a058 e0623457 u062 a06127 v36 p12426 u062 d2682582646 i06 d2846 f37 a05 h016 u0627666 e066 w3862546 w3213718378164 d26 i07 a06412 g46 u017 p17 b177372 h068 s8 v371626 s8472 u062 k46372 m62 a082287 g4825264',
            'searchContentType' => 'default',
            'searchContentSize' => 'small',
        );

        $this->object = new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages($dataOne);
        $this->objectTwo = new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentAuthors($dataTwo);
        $this->objectThree = new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages($dataThree);

        $queryFactory = new \RKW\RkwSearch\Search\QueryFactory();
        $this->fixture = new \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentPagesRepository($queryFactory);
        $this->fixtureTwo = new \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentAuthorsRepository();

        $this->fixture->setDeleteHard(TRUE);
        $this->fixtureTwo->setDeleteHard(TRUE);

        $this->fixture->setDebugMode(TRUE);
        $this->fixtureTwo->setDebugMode(TRUE);

        $this->fixture->add($this->object);
        $this->object->setRid($this->fixture->getLastQueryResult());

        $this->fixtureTwo->add($this->objectTwo);
        $this->objectTwo->setRid($this->fixtureTwo->getLastQueryResult());

        $this->fixture->add($this->objectThree);
        $this->objectThree->setRid($this->fixture->getLastQueryResult());

    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {

        // clean up
        $this->fixture->setDeleteHard(TRUE);
        $this->fixtureTwo->setDeleteHard(TRUE);

        $this->fixture->setDebugMode(TRUE);
        $this->fixtureTwo->setDebugMode(TRUE);

        $this->fixture->removeAll();
        $this->fixtureTwo->removeAll();
        unset($this->fixture);
        unset($this->fixtureTwo);
        unset($this->object);
        unset($this->objectTwo);

    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateAllGivenWrongObjectTypeThrowsException() {

        $this->assertTrue($this->fixture->relateAll($this->objectTwo));

    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateGivenWrongObjectTypeAsFirstParamThrowsException() {

        $this->fixture->relate($this->objectTwo, $this->objectTwo, 'test');

    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateGivenWrongObjectTypeAsSecondParamThrowsException() {

        $this->fixture->relate($this->object, $this->fixtureTwo, 'test');

    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateGivenEitherFieldNorEdgeClassThrowsException() {

        $this->fixture->relate($this->object, $this->objectTwo);
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateGivenWrongFieldThrowsException() {

        $this->fixture->relate($this->object, $this->objectTwo, 'test');
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateGivenWrongEdgeClassThrowsException() {

        $this->fixture->relate($this->object, $this->objectTwo, NULL, 'test');
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function unrelateAllByFieldGivenWrongObjectTypeThrowsException() {

        $this->fixture->unrelateAllByField($this->objectTwo, 'test');

    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function unrelateAllByFieldGivenWrongFieldThrowsException() {

        $this->fixture->unrelateAllByField($this->object, 'Test');
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function unrelateAllGivenNoEdgeClassThrowsException() {

        $this->fixture->unrelateAll($this->objectTwo, NULL, NULL);

    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function unrelateAllGivenNoVertexClassThrowsException() {

        $this->fixture->unrelateAll($this->objectTwo, 'test', NULL);

    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function updateGivenWrongObjectTypeThrowsException() {

        $this->fixture->update($this->objectTwo);
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function removeGivenWrongObjectTypeThrowsException() {

        $this->fixture->remove($this->objectTwo);
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function addGivenWrongObjectTypeThrowsException() {

        $this->fixture->add($this->objectTwo);
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function relateGivenTwoTimesTheSameObjectThrowsException() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->fixture->relate($this->object, $this->object, $firstKey);

    }


    /**
     * @test
     */
    public function relateAndUnrelateAllByFieldReturnTrue() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertTrue($this->fixture->relate($this->object, $this->objectTwo, $firstKey));
        $this->assertTrue($this->fixture->unrelateAllByField($this->object, $firstKey));

    }

    //==========================================

    /**
     * @test
     */
    public function addReturnsOne() {

        $this->assertSame(1, $this->fixture->add($this->object));
    }

    //==========================================

    /**
     * @test
     */
    public function updateReturnsOne() {

        $this->object->setTitle('TestUpdate');
        $this->assertSame(1, $this->fixture->update($this->object));
    }


    /**
     * @test
     */
    public function updateKeepsUidOfObject() {

        $uid = $this->object->getUid();
        $this->object->setTitle('TestUpdate');
        $this->fixture->update($this->object);
        $this->assertSame($uid, $this->object->getUid());
    }

    /**
     * @test
     */
    public function updateKeepsLanguageUidOfObject() {

        $lid = $this->object->getLanguageUid();
        $this->object->setTitle('TestUpdate');
        $this->fixture->update($this->object);
        $this->assertSame($lid, $this->object->getLanguageUid());
    }

    //==========================================

    /**
     * @test
     */
    public function removeReturnsOne() {

        $this->assertSame(1, $this->fixture->remove($this->object));
    }

    //==========================================

    /**
     * @test
     */
    public function countAllReturnsNumberGreaterThanOrEqualToZero() {

        $this->assertGreaterThanOrEqual(0, $this->fixture->countAll());

    }

    //==========================================

    /**
     * @test
     */
    public function findAllReturnsInstanceOfDocumentCollectionInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findAll());

    }

    /**
     * @test
     */
    public function findAllReturnsCollectionWithAtLeastOneCountableItem() {

        $this->assertGreaterThanOrEqual(1, $this->fixture->findAll()->count());

    }

    //==========================================

    /**
     * @test
     */
    public function findByUidReturnsInstanceOfDocumentCollectionInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findByUid($this->object->getUid()));

    }


    /**
     * @test
     */
    public function findByUidReturnsCollectionWithAtLeastOneCountableItem() {

        $this->assertGreaterThanOrEqual(1, $this->fixture->findByUid($this->object->getUid())->count());

    }

    //==========================================


    /**
     * @test
     */
    public function findByQueryFactoryReturnsInstanceOfDocumentCollectionInterface() {

        $this->fixture->getQueryFactory()->setSearchString('Test');
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findByQueryFactory());

    }


    //==========================================

    /**
     * @test
     */
    public function findRelatedGivenNonExistingUidReturnsNull() {

        $this->assertNull($this->fixture->findRelatedByQueryFactory(2, 0.29));
    }

    /**
     * @test
     */
    public function findRelatedByQueryFactoryWithHighToleranceReturnsAtLeastOneObject() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findRelatedByQueryFactory($this->object->getUid(), 0.29));
        $this->assertSame(1, count($this->fixture->findRelatedByQueryFactory($this->object->getUid(), 1000)));
    }

    /**
     * @test
     */
    public function findRelatedReturnsInstanceOfCollectionDocument() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findRelatedByQueryFactory($this->object->getUid(), 0.29));
    }


    /**
     * @test
     */
    public function findRelatedByQueryFactoryReturnsExpectedNumberOfResultsAndExpectedDocument() {

        $result = $this->fixture->findRelatedByQueryFactory($this->object->getUid(), 0.29);
        $this->assertSame(1, count($result));
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages',$result->first());
        $this->assertSame($this->objectThree->getUid(), $result->first()->getUid());
    }


    //==========================================


    /**
     * @test
     */
    public function findByRidReturnsInstanceOfDocumentPages() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages', $this->fixture->findByRid($this->object->getRid()));

    }


    //==========================================


    /**
     * @test
     */
    public function getObjectTypo3TableWithLanguageOverlayGivenObjectWithDefaultLanguageReturnsPagesTable() {

        $this->object->setLanguageUid(0);
        $this->assertSame('pages', $this->fixture->getObjectTypo3TableWithLanguageOverlay($this->object));

    }


    /**
     * @test
     */
    public function getObjectTypo3TableWithLanguageOverlayGivenObjectWithNonDefaultLanguageReturnPagesLanguageOverlayTable() {

        $this->object->setLanguageUid(2);
        $this->assertSame('pages_language_overlay', $this->fixture->getObjectTypo3TableWithLanguageOverlay($this->object));

    }

    //==========================================

    /**
     * @test
     */
    public function hasRelationFieldGivenFieldNameReturnsTrue() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertTrue($this->fixture->hasRelationField($firstKey));
    }


    /**
     * @test
     */
    public function hasRelationFieldGivenWrongFieldNameReturnsFalse() {

        $this->assertFalse($this->fixture->hasRelationField('test'));
    }

    //==========================================

    /**
     * @test
    */
    public function getRelationFieldsReturnsArray() {

        $this->assertInternalType('array',  $this->fixture->getRelationFields());
    }


    /**
     * @test
     */
    public function getRelationMmTableGivenFieldNameReturnsString() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('string',  $this->fixture->getRelationMmTable($firstKey));
    }


    /**
     * @test
     */
    public function getRelationMmTableGivenWrongFieldNameReturnsNull() {

        $this->assertNull($this->fixture->getRelationMmTable('test'));
    }

    //==========================================

    /**
     * @test
     */
    public function getRelationForeignTableGivenFieldNameReturnsString() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('string',  $this->fixture->getRelationForeignTable($firstKey));
    }


    /**
     * @test
     */
    public function getRelationForeignTableGivenWrongFieldNameReturnsNull() {

        $this->assertNull($this->fixture->getRelationMmTable('test'));
    }

    //==========================================

    /**
     * @test
     */
    public function getRelationForeignFieldGivenFieldNameReturnsString() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('string',  $this->fixture->getRelationForeignField($firstKey));
    }


    /**
     * @test
     */
    public function getRelationForeignFieldGivenWrongFieldNameReturnsDefaultValue() {

        $this->assertSame('uid_foreign', $this->fixture->getRelationForeignField('test'));
    }

    //==========================================

    /**
     * @test
     */
    public function getRelationForeignTableFieldGivenFieldNameReturnsString() {

        // only works for mm-relations
        $this->assertInternalType('string',  $this->fixture->getRelationForeignTableField('tx_rkwbasics_file'));
    }


    /**
     * @test
     */
    public function getRelationForeignTableFieldGivenWrongFieldNameReturnsNull() {

        $this->assertNull($this->fixture->getRelationForeignTableField('test'));
    }

    //==========================================

    /**
     * @test
     */
    public function getRelationForeignSortByGivenWrongFieldNameReturnsNull() {

        $this->assertNull($this->fixture->getRelationForeignSortBy('test'));
    }

    /**
     * @test
     */
    public function getRelationForeignSortByGivenFieldNameReturnsString() {

        // only works for mm-relations
        $this->assertInternalType('string',  $this->fixture->getRelationForeignSortBy('tx_rkwbasics_file'));
    }

    //==========================================


    /**
     * @test
     */
    public function getRelationForeignMatchFieldsReturnsEmptyArray() {

        $this->assertEmpty($this->fixture->getRelationForeignMatchFields('test'));
    }

    /**
     * @test
     */
    public function getRelationForeignMatchFieldsGivenFieldNameReturnsArray() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('array',  $this->fixture->getRelationForeignMatchFields($firstKey));
    }

    //==========================================

    /**
     * @test
     */
    public function getRelationLocalFieldGivenWrongFieldNameReturnsDefaultValue() {

        $this->assertSame('uid_local', $this->fixture->getRelationLocalField('test'));
    }

    /**
     * @test
     */
    public function getRelationLocalFieldGivenFieldNameReturnsString() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('string',  $this->fixture->getRelationLocalField($firstKey));
    }


    //==========================================

    /**
     * @test
     */
    public function getRelationEdgeClassGivenFieldNameReturnsString() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('string',  $this->fixture->getRelationEdgeClass($firstKey));
    }


    /**
     * @test
     */
    public function getRelationEdgeClassGivenWrongFieldNameReturnsNull() {

        $this->assertNull($this->fixture->getRelationEdgeClass('test'));
    }

    //==========================================

    /**
     * @test
     */
    public function getRelationVertexClassGivenFieldNameReturnsString() {

        $firstKey = key($this->fixture->getRelationFields());
        $this->assertInternalType('string',  $this->fixture->getRelationVertexClass($firstKey));
    }


    /**
     * @test
     */
    public function getRelationVertexClassGivenWrongFieldNameReturnsNull() {

        $this->assertNull($this->fixture->getRelationVertexClass('test'));
    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getRootlineRelationUidsGivenWrongObjectThrowsException() {

        $this->fixture->getRootlineRelationUids($this->objectTwo);
    }

    /**
     * @test
     */
    public function getRootlineRelationUidsReturnsArray() {

        $this->assertInternalType('array',  $this->fixture->getRootlineRelationUids($this->object));
    }

    //==========================================

    /**
     * @test
     */
    public function getOrientDbClassReturnsString() {

        $this->assertInternalType('string',  $this->fixture->getOrientDbClass());
    }

    //==========================================

    /**
     * @test
     */
    public function getTypo3TableReturnsString() {

        $this->assertInternalType('string',  $this->fixture->getTypo3Table());
    }

    //==========================================

    /**
     * @test
     */
    public function getObjectTypeReturnsString() {

        $this->assertInternalType('string',  $this->fixture->getObjectType());
    }

    //==========================================

    /**
     * @test
     */
    public function getRepositoryClassNameReturnsString() {

        $this->assertInternalType('string',  $this->fixture->getRepositoryClassName());
    }

    //==========================================


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getMappingRecordByObjectGivenWrongObjectInstanceThrowsException() {

        $this->fixture->getMappingRecordByObject(new \RKW\RkwSearch\Keywords\Indexer);
    }

    //==========================================


    /**
     * @test
     */
    public function getMappingRecordByObjectGivenObjectReturnsInstanceOfRidMapping() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Domain\\Model\\RidMapping', $this->fixture->getMappingRecordByObject($this->object));

    }

    //==========================================

    /**
     * @test
     */
    public function getMappingTableRepositoryReturnsInstanceOfRidMappingRepository() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Domain\\Repository\\RidMappingRepository', $this->fixture->getMappingTableRepository());

    }

    //==========================================

    /**
     * @test
     */
    public function getMappingTableModelReturnsInstanceOfRidMapping() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Domain\\Model\\RidMapping', $this->fixture->getMappingTableModel());

    }

    //==========================================

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getQueryFactoryWithoutQueryFactoryThrowsException() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentPagesRepository();
        $this->fixture->getQueryFactory();

    }


    /**
     * @test
     */
    public function getQueryFactoryReturnsInstanceOfQueryFactory() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Search\\QueryFactory', $this->fixture->getQueryFactory());

    }

    //==========================================

    /**
     * @test
     */
    public function getPersistenceManagerReturnsInstanceOfPersistenceManager() {

        $this->assertInstanceOf('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager', $this->fixture->getPersistenceManager());

    }

    //==========================================


    /**
     * @test
     */
    public function getCacheReturnsInstanceOfRepositoryCache() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Cache\\RepositoryCache', $this->fixture->getCache());

    }

    //==========================================

    /**
     * @test
     */
    public function getTypo3DatabaseReturnsInstanceOfDatabaseConnection() {

        $this->assertInstanceOf('TYPO3\\CMS\\Core\\Database\\DatabaseConnection', $this->fixture->getTypo3Database());
    }

    //==========================================

    /**
     * @test
     */
    public function getOrientDbDatabaseReturnsInstanceOfDatabaseInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Database\\DatabaseInterface', $this->fixture->getOrientDbDatabase());
    }

    //==========================================

    /**
     * @test
     */
    public function getTypoScriptConfigurationReturnsArray() {

        $this->assertInternalType('array', $this->fixture->getTypoScriptConfiguration());
    }

    //==========================================

    /**
     * @test
     */
    public function getEnvironmentModeReturnsString() {

        $this->assertInternalType('string', $this->fixture->getEnvironmentMode());
    }

    //==========================================


    /**
     * @test
     */
    public function findByTitleReturnsInstanceOfDocumentCollectionInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findByTitle('Test'));

    }


    /**
     * @test
     */
    public function findByTitleReturnsCollectionWithAtLeastOneCountableItem() {

        $this->assertGreaterThanOrEqual(1, $this->fixture->findByTitle('Test')->count());

    }

    //==========================================

    /**
     * @test
     */
    public function findLeftBySubtitleReturnsInstanceOfDocumentCollectionInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Collection\\Document', $this->fixture->findLeftBySubtitle('Test'));

    }


    /**
     * @test
     */
    public function findLeftBySubtitleReturnsCollectionWithAtLeastOneCountableItem() {

        $this->assertGreaterThanOrEqual(1, $this->fixture->findLeftBySubtitle('Test')->count());

    }

    //==========================================

    /**
     * @test
     */
    public function findOneByTitleReturnsInstanceOfDocumentPages() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages', $this->fixture->findOneByTitle('Test'));

    }


    //==========================================


    /**
     * @test
     */
    public function countByTitleReturnsAtLeastOneItem() {

        $this->assertGreaterThanOrEqual(1, $this->fixture->countByTitle('Test'));

    }



} 