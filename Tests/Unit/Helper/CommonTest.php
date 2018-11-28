<?php
namespace RKW\RkwSearch\Tests\Helper;

/**
 * Class CommonTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class CommonTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Helper\Common
     */
    protected $fixture;


    /**
     * @var array
     */
    protected $matches;


    /**
     * Set up fixture
     */
    public function setUp() {

         $this->matches = array (
             'repository' => 'RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\DocumentPagesRepository',
             'model' => 'RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentPages',
             'model2' => 'RKW\\RkwSearch\\OrientDb\\Domain\\Model\\DocumentAuthors',
             'table' => 'pages',
             'modelClass' => 'DocumentPages',
             'repositoryClass' => 'DocumentPagesRepository'

         );

        $this->fixture = new \RKW\RkwSearch\Helper\Common();
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
    public function underscoreReturnsUnderscoredValue() {

        $stringRaw = 'thisIsToBeTransformed';
        $stringTransformed = 'this_is_to_be_transformed';
        $this->assertSame($stringTransformed, $this->fixture->underscore($stringRaw));

    }


    /**
     * @test
     */
    public function backslashReturnsBackslashedValue() {

        $stringRaw = 'ThisIsToBeTransformed';
        $stringTransformed = 'This\\Is\\To\\Be\\Transformed';
        $this->assertSame($stringTransformed, $this->fixture->backslash($stringRaw));

    }


    /**
     * @test
     */
    public function camelizeReturnsCamlizedValue() {

        $stringRaw = 'this_is_to_be_transformed';
        $stringTransformed = 'thisIsToBeTransformed';
        $this->assertSame($stringTransformed, $this->fixture->camelize($stringRaw));

    }



    /**
     * @test
     */
    public function getShortNameGivenRepositoryNameReturnsRepositoryShortName() {

        $this->assertSame($this->matches['repositoryClass'], $this->fixture->getShortName($this->matches['repository']));

    }

    /**
     * @test
     */
    public function getShortNameGivenRepositoryObjectReturnsRepositoryShortName() {

        $this->assertSame($this->matches['repositoryClass'], $this->fixture->getShortName(new $this->matches['repository']()));

    }


    /**
     * @test
     */
    public function getShortNameGivenRepositoryNameAndModelBooleanReturnsModelShortName() {

        $this->assertSame($this->matches['modelClass'], $this->fixture->getShortName($this->matches['repository'], TRUE));

    }


    /**
     * @test
     */
    public function getShortNameGivenRepositoryObjectAndModelBooleanReturnsModelShortName() {

        $this->assertSame($this->matches['modelClass'], $this->fixture->getShortName(new $this->matches['repository'](), TRUE));

    }


    /**
     * @test
     */
    public function getTyposcriptConfigurationReturnsArray() {

        $this->assertTrue(is_array($this->fixture->getTypoScriptConfiguration()));
    }
} 