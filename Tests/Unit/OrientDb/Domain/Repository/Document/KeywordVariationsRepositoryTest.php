<?php
namespace RKW\RkwSearch\Tests\OrientDb\Domain\Repository\Document;
use RKW\RkwSearch\OrientDb\Helper\Common;

/**
 * Class KeywordVariationsRepositoryTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class KeywordVariationsRepositoryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepository
     */
    protected $fixture;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariations
     */
    protected $object;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
     */
    protected $objectTwo;


    /**
     * Set up fixture
     */
    public function setUp() {

        $data = array (
            'name' => 'test',
            'nameFuzzy' => 'T4575',
            'nameLength' => 1,
            'nameCaseSensitive' => 'Test',

        );


        $this->object = new \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariations($data);
        $this->objectTwo = new \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariations($data);

        $this->fixture = new \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepository();
        $this->fixture->setDebugMode(TRUE);
        $this->fixture->setDeleteHard(TRUE);

        $this->fixture->add($this->objectTwo);

    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {

        // since we have some exceptions tests we need to delete here the dead bodies
        $this->fixture->removeAll();
        unset($this->fixture);
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function addGivenWrongObjectTypeThrowsException() {

        $this->assertTrue($this->fixture->add(new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages()));

    }


    /**
     * @test
     */
    public function addSetsQueryCounterToOne() {

        $counter = 0;
        $this->fixture->add($this->object, $counter);
        $this->assertEquals(1, $counter);
    }




} 