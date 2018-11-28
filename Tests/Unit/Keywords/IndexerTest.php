<?php
namespace RKW\RkwSearch\Tests\Keywords;

/**
 * Class IndexerTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class IndexerTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Keywords\Indexer
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\Keywords\Indexer
     */
    protected $fixtureTwo;


    /**
     * @var \RKW\RkwSearch\Domain\Model\RidMapping
     */
    protected $mappingModel;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentPagesRepository
     */
    protected $contentRepository;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
     */
    protected $contentModel;

    /**
     * @var \RKW\RkwSearch\Collection\AnalysedKeywords
     */
    protected $data;


    /**
     * @var \RKW\RkwSearch\Collection\AnalysedKeywords
     */
    protected $dataUnconnected;

    /**
     * Set up fixture
     */
    public function setUp() {


        $data = array (

            'entscheid' => array (

                'variations' => array (
                    'entscheiden' => 'Entscheiden'
                ),

                'count' => 4,
                'distance' => 0,
                'length' => 1,
                'weight' => 0.5,
                'tags' => 'NN',
                'type' => 'default'
            ),

            'erfolg' => array (

                'variations' => array (
                    'erfolgs' => 'Erfolgs'
                ),

                'count' => 1,
                'distance' => 0,
                'length' => 1,
                'weight' => 0.2153382790367,
                'tags' => 'NN',
                'type' => 'default'
            ),


            'entscheiden' => array (

                'variations' => array (
                    'entscheiden' => 'Entscheiden',
                    'entscheidens' => 'Entscheidens'
                ),

                'count' => 5,
                'distance' => 0,
                'length' => 1,
                'weight' => 0.55664137627969,
                'tags' => 'NN',
                'type' => 'default'
            ),


            'erfolg entscheid' => array (

                'variations' => array (
                    'erfolgs entscheiden' => 'Erfolgs Entscheiden'
                ),

                'count' => 1,
                'distance' => 8,
                'length' => 2,
                'weight' => 0.113767699081,
                'tags' => 'NN NN',
                'type' => 'default'
            ),

            'erfolg entscheiden' => array (

                'variations' => array (
                    'erfolgs entscheiden' => 'Erfolgs Entscheiden'
                ),

                'count' => 1,
                'distance' => 8,
                'length' => 2,
                'weight' => 0.113767699081,
                'tags' => 'NN NN',
                'type' => 'default'
            ),

            'entscheid entscheiden' => array (

                'variations' => array (
                    'entscheiden entscheidens' => 'Entscheiden Entscheidens'
                ),

                'count' => 1,
                'distance' => 2,
                'length' => 2,
                'weight' => 0.34130309724299,
                'tags' => 'NN NN',
                'type' => 'default'
            ),

        );

        $dataUnconnected = array (

            'entscheid' => array (

                'variations' => array (
                    'entscheiden' => 'Entscheiden'
                ),

                'count' => 4,
                'distance' => 0,
                'length' => 1,
                'weight' => 0.5,
                'tags' => 'NN',
                'type' => 'default'
            ),

            'erfolg' => array (

                'variations' => array (
                    'erfolgs' => 'Erfolgs'
                ),

                'count' => 1,
                'distance' => 0,
                'length' => 1,
                'weight' => 0.2153382790367,
                'tags' => 'NN',
                'type' => 'default'
            ),

        );

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
        $this->contentRepository = new \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentPagesRepository();
        $this->contentRepository->setDebugMode(TRUE);
        $this->contentRepository->setDeleteHard(TRUE);

        $this->contentRepository->add($this->contentModel);
        $rid = $this->contentRepository->getLastQueryResult();

        $this->mappingModel = new \RKW\RkwSearch\Domain\Model\RidMapping();
        $this->mappingModel->setRid($rid);
        $this->mappingModel->setClass('DocumentPages');
        $this->mappingModel->setT3lid($this->contentModel->getLanguageUid());


        $configuration = array(
            'keywords' => array (

                'vertexClass' => 'KeywordVariations',
                'edgeClass' => array (
                    'keyword2content' => 'EdgeContains',
                )
            ),
        );

        $this->fixture = new \RKW\RkwSearch\Keywords\Indexer($configuration);
        $this->fixture->getKeywordVariationsRepository()->setDebugMode(TRUE);

        $this->fixtureTwo = new \RKW\RkwSearch\Keywords\Indexer(array ('wrong' => 'configuration'));

        $this->fixture->setMappingModel($this->mappingModel);
        $this->fixtureTwo->setMappingModel($this->mappingModel);

        $this->fixture->getContentRepository()->setDebugMode(TRUE);

        $this->data = new \RKW\RkwSearch\Collection\AnalysedKeywords($data);
        $this->dataUnconnected = new \RKW\RkwSearch\Collection\AnalysedKeywords($dataUnconnected);

    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {

        if ($this->fixture) {
            $this->fixture->getKeywordVariationsRepository()->removeAll();
        }

        $this->contentRepository->removeAll();

        unset($this->fixture);
        unset($this->fixtureTwo);
    }



    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function indexWithoutDataThrowsException() {
        $this->fixture->index();
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function indexGivenWrongDataStructureThrowsException() {
        $this->fixture->setData(new \RKW\RkwSearch\Collection\AnalysedKeywords());
        $this->fixture->index();
    }


    /**
     * @test
     */
    public function indexGivenDataReturnsQueryCounterToExpectedValue() {
        $this->fixture->setData($this->data);
        $queryCounter = 0;
        $this->fixture->index($queryCounter);
        $this->assertEquals(16, $queryCounter);
    }

    /**
     * @test
     */
    public function indexGivenDataAndKeywordLimitFiveReturnsKeywordLimit() {
        $this->fixture->setData($this->data);
        $queryCounter = 0;
        $keywordCounter = 0;
        $this->fixture->index($queryCounter, $keywordCounter, 0, 4);
        $this->assertEquals(4, $keywordCounter);
    }

    /**
     * @test
     */
    public function indexGivenDataAndQueryLimitFiveReturnsNineQueries() {
        $this->fixture->setData($this->data);
        $queryCounter = 0;
        $keywordCounter = 0;
        $this->fixture->index($queryCounter, $keywordCounter, 5);
        $this->assertEquals(9, $queryCounter);
    }

    /**
     * @test
     */
    public function cleanupWithConnectedKeywordsReturnsZeroQueries() {
        $this->fixture->setData($this->data);
        $queryCounter = 0;
        $this->fixture->index($queryCounter);
        $this->assertEquals(0, $this->fixture->cleanup());
    }

    /**
     * @test
     */
    public function cleanupWithUnconnectedKeywordsReturnsFourQueries() {
        $this->fixture->setData($this->dataUnconnected);
        $queryCounter = 0;
        $this->fixture->index($queryCounter);
        $this->contentRepository->remove($this->contentModel);
        $this->assertEquals(2, $this->fixture->cleanup());
    }

    /**
     * @test
     */
    public function unrelateAllWithoutAnythingRelatedReturnsFalse() {

        $this->assertFalse($this->fixture->unrelateAll());
    }


    /**
     * @test
     */
    public function unrelateAllWithIndexReturnsTrue() {
        $this->fixture->setData($this->data);
        $queryCounter = 0;
        $this->fixture->index($queryCounter);
        $this->assertTrue($this->fixture->unrelateAll());
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function setDataGivenWrongInstanceThrowsException() {
        $this->fixture->setData(new \RKW\RkwSearch\Domain\Model\QueueAnalysedKeywords());
    }

    /**
     * @test
     */
    public function setDataReturnsTrue() {
        $this->assertTrue($this->fixture->setData($this->data));

    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function setMappingModelGivenWrongInstanceTypeThrowsException() {

        $this->fixture->setMappingModel(new \RKW\RkwSearch\Domain\Model\QueueAnalysedKeywords());
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function setMappingModelGivenModelWithoutRidThrowsException() {
        $this->mappingModel->setRid(NULL);
        $this->fixture->setMappingModel($this->mappingModel);
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function setMappingModelGivenModelWithoutClassThrowsException() {
        $this->mappingModel->setClass(NULL);
        $this->fixture->setMappingModel($this->mappingModel);
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function setMappingModelGivenModelWithoutT3lidThrowsException() {
        $this->mappingModel->setT3lid(NULL);
        $this->fixture->setMappingModel($this->mappingModel);
    }



    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getMappingModelWithoutSetMappingModelThrowsException() {
        $this->fixture = new \RKW\RkwSearch\Keywords\Indexer();
        $this->fixture->getMappingModel();
    }

    /**
     * @test
     */
    public function getMappingModelAndSetMappingModelReturnsInstanceOfRidMapping() {
        $this->fixture->setMappingModel($this->mappingModel);
        $this->assertInstanceOf('RKW\\RkwSearch\\Domain\\Model\\RidMapping', $this->fixture->getMappingModel());
    }

    /**
     * @test
     */
    public function getMappingModelAndSetMappingModelReturnsResetsAllModelGetter() {

        $this->fixture->setMappingModel($this->mappingModel);

        $contentModel = $this->fixture->getContentModel();
        $keywordVariationsModel = $this->fixture->getKeywordVariationsModel();

        $this->fixture->setMappingModel($this->mappingModel);

        $this->assertNotSame($contentModel, $this->fixture->getContentModel());
        $this->assertNotSame($keywordVariationsModel, $this->fixture->getKeywordVariationsModel());
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getContentModelGivenWrongClassNameThrowsException() {
        $this->mappingModel->setClass('Test');
        $this->fixture->setMappingModel($this->mappingModel);
        $this->fixture->getContentModel();
    }

    /**
     * @test
     */
    public function getContentModelReturnsInstanceOfDocumentPages() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages', $this->fixture->getContentModel());
    }

    /**
     * @test
     */
    public function getContentModelReturnsModelWithExpectedRid() {
        $this->assertSame($this->mappingModel->getRid(), $this->fixture->getContentModel()->getRid());
    }

    /**
     * @test
     */
    public function getContentModelReturnsModelWithExpectedLanguageUid() {
        $this->assertSame($this->mappingModel->getT3lid(), $this->fixture->getContentModel()->getLanguageUid());
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getVariationModelWithWrongConfigurationThrowsException() {
        $this->fixtureTwo->getKeywordVariationsModel();
    }

    /**
     * @test
     */
    public function getKeywordVariationModelReturnsInstanceOfKeywordBasesInterface() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\KeywordVariationsInterface', $this->fixture->getKeywordVariationsModel());
    }


    /**
     * @test
     */
    public function getContentRepositoryWithWrongConfigurationReturnsInstanceOfDocumentRepositoryInterface() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\DocumentRepositoryInterface', $this->fixtureTwo->getContentRepository());
    }


    /**
     * @test
     */
    public function getContentRepositoryReturnsInstanceOfDocumentRepositoryInterface() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\DocumentRepositoryInterface', $this->fixture->getContentRepository());
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getKeywordVariationRepositoryWithWrongConfigurationThrowsException() {
        $this->fixtureTwo->getKeywordVariationsRepository();
    }

    /**
     * @test
     */
    public function getKeywordVariationRepositoryReturnsInstanceOfKeywordBasesInterface() {
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\KeywordVariationsRepositoryInterface', $this->fixture->getKeywordVariationsRepository());
    }




}