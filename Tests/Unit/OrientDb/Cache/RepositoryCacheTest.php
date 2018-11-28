<?php
namespace RKW\RkwSearch\Tests\OrientDb\Cache;

/**
 * Class RepositoryCacheTest
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class RepositoryCacheTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\OrientDb\Cache\RepositoryCache
     */
    protected $fixture;


    /**
     * Set up fixture
     */
    public function setUp() {
        $this->fixture = new \RKW\RkwSearch\OrientDb\Cache\RepositoryCache('FE', 'Production');
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
    public function setAndGetIdentifierAsQueryReturnsExpectedValue() {

        $object = new \RKW\RkwSearch\OrientDb\Storage\Query\Query();
        $this->fixture->setIdentifier($object);
        $this->assertSame(sha1($object->getRaw()) ,$this->fixture->getIdentifier());

    }


    /**
     * @test
     */
    public function setAndGetIdentifierAsStringReturnsExpectedValue() {

        $text = 'Test';
        $this->fixture->setIdentifier($text);
        $this->assertSame(sha1($text) ,$this->fixture->getIdentifier());

    }


    /**
     * @test
     */
    public function getCacheManagerReturnsInstanceOfCacheManager() {

        $this->assertInstanceOf ('TYPO3\\CMS\\Core\\Cache\\CacheManager', $this->fixture->getCacheManager());

    }

    /**
     * @test
     */
    public function setContentAndFlushCachesByTagReturnFalse() {

        $data = NULL;

        $this->fixture->setContent($data, array ('Test'));
        $this->fixture->getCacheManager()->flushCachesByTag('Test');

        $this->assertFalse($this->fixture->getContent());

    }

    /**
     * @test
     */
    public function getAndSetContentGivenStringReturnString() {

        $data = 'Test';

        $this->fixture->setContent($data, array ('Test'));
        $this->assertSame($data, $this->fixture->getContent());

        $this->fixture->getCacheManager()->flushCachesByTag('Test');
    }


    /**
     * @test
     */
    public function getAndSetContentGivenArrayReturnArray() {

        $data = array ('Test');

        $this->fixture->setContent($data, array ('Test'));
        $this->assertSame($data, $this->fixture->getContent());

        $this->fixture->getCacheManager()->flushCachesByTag('Test');
    }


    /**
     * @test
     */
    public function getAndSetContentGivenNullReturnNull() {

        $data = NULL;

        $this->fixture->setContent($data, array ('Test'));
        $this->assertSame($data, $this->fixture->getContent());

        $this->fixture->getCacheManager()->flushCachesByTag('Test');
    }


    /**
     * @test
     */
    public function getAndSetContentWithStringReturnFalseInBeModeAndProductionContext() {

        $data = 'Test';
        $this->fixture = new \RKW\RkwSearch\OrientDb\Cache\RepositoryCache('BE', 'Production');

        $this->fixture->setContent($data, array ('Test'));
        $this->assertFalse($this->fixture->getContent());

        $this->fixture->getCacheManager()->flushCachesByTag('Test');
    }

    /**
     * @test
     */
    public function getAndSetContentWithStringReturnFalseInBeModeAndDevelopmentContext() {

        $data = 'Test';
        $this->fixture = new \RKW\RkwSearch\OrientDb\Cache\RepositoryCache('BE', 'Development');

        $this->fixture->setContent($data, array ('Test'));
        $this->assertFalse($this->fixture->getContent());

        $this->fixture->getCacheManager()->flushCachesByTag('Test');
    }


    /**
     * @test
     */
    public function getAndSetContentWithStringReturnFalseInFeModeAndDevelopmentContext() {

        $data = 'Test';
        $this->fixture = new \RKW\RkwSearch\OrientDb\Cache\RepositoryCache('FE', 'Development');

        $this->fixture->setContent($data, array ('Test'));
        $this->assertFalse($this->fixture->getContent());

        $this->fixture->getCacheManager()->flushCachesByTag('Test');
    }


} 