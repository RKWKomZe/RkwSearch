<?php
namespace RKW\RkwSearch\Tests\FuzzySearch;

/**
 * Class ColognePhonetiTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class ColognePhoneticTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\FuzzySearch\ColognePhonetic
     */
    protected $fixture;


    /**
     * @var array
     */
    protected $data;


    /**
     * Set up fixture
     */
    public function setUp() {


         $this->data = array (
             'Fernández-Sánchez' => 'f376628648',
             'Blaeser-Benfer'  => 'b15871637',
             'Wallisch'  => 'w358',
             'Walisch'  => 'w358',
             'Böcher' => 'b147',
             'Boecher' => 'b147',
             'Fach' => 'f34',
             'Weg' => 'w34',
             'Senior Entrepreneurship' => 's867 e062716781',
             '"Text mit Non-Alpha' => 't2482 m62 n6651',
             'äußerst Ömmelig' => 'a08782 o0654',

         );

        $this->fixture = new \RKW\RkwSearch\FuzzySearch\ColognePhonetic();
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
    public function encodeReturnsExpectedValues() {

        foreach ($this->data as $key => $value)
            $this->assertSame($value, $this->fixture->encode($key));

    }

} 