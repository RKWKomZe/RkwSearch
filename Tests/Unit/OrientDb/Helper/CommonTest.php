<?php
namespace RKW\RkwSearch\Tests\OrientDb\Helper;

/**
 * Class CommonTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class CommonTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Helper\Common
     */
    protected $fixture;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages
     */
    protected $object;


    /**
     * @var array
     */
    protected $data;


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


         $this->data = array (
             '@type' => 'd',
             '@rid'  => '#11:1151',
             '@version'  => 6,
             '@class'  => 'DocumentPages',

             'uid' =>  12,

             'tstamp'  => time(),
             'crdate' => time(),
             'hidden' => 1,
             'deleted' => 0,
             'sorting' => 0,
             'sysLanguageUid' => 0,
             'endtime' => 0,
             'starttime' => 0,

             'content' => 'Test',
             'doktype' => 1,
             'title' => 'Test',
             'subtitle' => 'Test',
             'abstract' => 'Test',
             'description' => 'Test',
             'keywords' => 'Test'
         );


        $this->object = new \RKW\RkwSearch\OrientDb\Domain\Model\DocumentPages($this->data);
        $this->fixture = new \RKW\RkwSearch\OrientDb\Helper\Common();
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
    public function getOrientClassNameFromTableNameGivenPagesTableNameReturnsExpectedOrientDbClassName() {

        $this->assertSame($this->matches['modelClass'], $this->fixture->getOrientClassNameFromTableName($this->matches['table']));
    }

    /**
     * @test
     */
    public function getOrientClassNameFromTableNameGivenWrongTableNameReturnsNull() {

        $this->assertNull($this->fixture->getOrientClassNameFromTableName('test'));
    }

    /**
     * @test
     */
    public function getTypo3TableFromOrientClassGivenOrientDbClassNameReturnsExpectedTableName() {

        $this->assertSame($this->matches['table'], $this->fixture->getTypo3TableFromOrientClass($this->matches['modelClass']));
    }

    /**
     * @test
     */
    public function getTypo3TableFromOrientClassGivenWrongOrientClassReturnsExpectedTableName() {

        $this->assertNull($this->fixture->getTypo3TableFromOrientClass('test'));
    }


    /**
     * @test
     */
    public function getTypo3TableFromOrientClassGivenOrientDbModelReturnsExpectedTableName() {

        $this->assertSame($this->matches['table'], $this->fixture->getTypo3TableFromOrientClass(new $this->matches['model']()));
    }



    /**
     * @test
     */
    public function getOrientModelFromTableNameGivenTableNameReturnsExpectedOrientDbModelNamespace() {

        $this->assertSame($this->matches['model'], $this->fixture->getOrientModelFromTableName($this->matches['table']));

    }



    /**
     * @test
     */
    public function getOrientRepositoryFromTableNameReturnsOrientDbRepositoryNamespace() {

        $this->assertSame($this->matches['repository'], $this->fixture->getOrientRepositoryFromTableName($this->matches['table']));

    }


    /**
     * @test
     */
    public function getOrientModelFromClassNameReturnsOrientDbModelNamespace() {

        $this->assertSame($this->matches['model'], $this->fixture->getOrientModelFromClassName($this->matches['modelClass']));

    }


    /**
     * @test
     */
    public function getOrientRepositoryFromClassNameReturnsOrientDbRepositoryNamespace() {

        $this->assertSame($this->matches['repository'], $this->fixture->getOrientRepositoryFromClassName($this->matches['modelClass']));

    }


} 