<?php
namespace RKW\RkwSearch\Tests\OrientDb\Storage\Query\Formatter\Query;

/**
 * Class SubQueryTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class SubQueryTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\SubQuery
     */
    protected $fixture;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\SubQuery();
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
    public function formatWithParamsReturnsExpectedString() {

        $query = new \RKW\RkwSearch\OrientDb\Storage\Query\Query();
        $query->select(array('Test'));
        $data = array (
            $query
        );

        $this->assertSame('(SELECT Test FROM)', $this->fixture->format($data));
    }







} 