<?php
namespace RKW\RkwSearch\Tests\OrientDb\Storage;

/**
 * Class DatabaseLoaderTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class DatabaseLoaderTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\DatabaseLoader
     */
    protected $fixture;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Storage\DatabaseLoader();
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
    public function getHandleWithBinaryParamReturnsInstanceOfDatabaseInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Database\\DatabaseInterface', $this->fixture->getHandle('binary'));
    }


    /**
     * @test

    public function getHandleWithRestParamReturnsInstanceOfDatabaseInterface() {

        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Storage\\Database\\DatabaseInterface', $this->fixture->getHandle('rest'));

    }

     */



} 