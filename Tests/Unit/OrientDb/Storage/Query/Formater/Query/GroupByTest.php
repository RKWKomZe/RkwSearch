<?php
namespace RKW\RkwSearch\Tests\OrientDb\Storage\Query\Formatter\Query;

/**
 * Class GroupByTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class GroupByTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\GroupBy
     */
    protected $fixture;


    /**
     * Set up fixture
     */
    public function setUp() {

        $this->fixture = new \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query\GroupBy();
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

        $data = array (
            '@rid',
            'test'
        );

        $this->assertSame('GROUP BY @rid, test', $this->fixture->format($data));
    }







} 