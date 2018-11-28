<?php
namespace RKW\RkwSearch\Tests\OrientDb\Storage\Query\Formatter\Query;

/**
 * Class QueryTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class SetTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\Set
     */
    protected $fixture;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\Set();
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
    public function formatWithoutParamsReturnsEmptyString() {

        $this->assertEmpty($this->fixture->format(array()));
    }


    /**
     * @test
     */
    public function formatWithParamsReturnsStringWithSetKeyword() {

        $data = array (
            'id' => 1,
            'name' => 'Test',
            'street' => 'Test'
        );

        $this->assertSame('SET id = 1, name = "Test", street = "Test"', $this->fixture->format($data));
    }







} 