<?php
namespace RKW\RkwSearch\Tests\Domain\Repository;

/**
 * Class ImportHookTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class RidMappingRepositoryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Domain\Repository\RidMappingRepository
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\Domain\Model\RidMapping
     */
    protected $model;


    /**
     * Set up fixture
     */
    public function setUp() {

        $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->fixture = $objectManager->get('RKW\RkwSearch\Domain\Repository\RidMappingRepository');

        $this->model = new \RKW\RkwSearch\Domain\Model\RidMapping();
        $this->model->setT3id(100000000);
        $this->model->setT3table('pages');


    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {

        unset($this->fixture);
    }


    //###############################################################


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function updateStatusPageGivenInvalidObjectThrowsException() {

        $this->fixture->updateStatusPage(NULL, 1, 'Test');

    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function updateStatusPageGivenInvalidStatusThrowsException() {

        $this->fixture->updateStatusPage($this->model, 88, 'Test');

    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function updateStatusGivenInvalidObjectThrowsException() {

        $this->fixture->updateStatus(NULL, 1, 'Test');

    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function updateStatusGivenInvalidStatusThrowsException() {

        $this->fixture->updateStatus($this->model, 88, 'Test');

    }

    /**
     * @test
     */
    public function getPagesRepositoryReturnsInstanceOfPagesRepository() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Domain\\Repository\\PagesRepository', $this->fixture->getPagesRepository());

    }

    /**
     * @test
     */
    public function getPagesLanguageOverlayRepositoryReturnsInstanceOfPagesLanguageOverlayRepository() {

        $this->assertInstanceOf('RKW\\RkwSearch\\Domain\\Repository\\PagesLanguageOverlayRepository', $this->fixture->getPagesLanguageOverlayRepository());

    }


} 