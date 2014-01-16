<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected static $fixtureConfig;

    /**
     * @var array
     */
    protected static $fixtureConfigMerged;

    /**
     * @var \Magento\App\Config
     */
    protected $config;

    /**
     * @var \Magento\App\Config
     */
    protected $configMerged;

    public static function setUpBeforeClass()
    {
        self::$fixtureConfig = require __DIR__ . '/Config/_files/local.php';
        self::$fixtureConfigMerged = require __DIR__ . '/Config/_files/other/local_developer_merged.php';
    }

    protected function setUp()
    {
        $configLoader = $this->getMock('Magento\App\Config\Loader', array(), array(), '', false);
        $configLoader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));

        $this->config = new \Magento\App\Config(array(), $configLoader);
        $this->configMerged = new \Magento\App\Config(
            require __DIR__ . '/Config/_files/other/local_developer.php',
            $configLoader
        );
    }

    /**
     * @param string $connectionName
     * @param bool $testMerged
     * @param array|null $expectedResult
     * @dataProvider getConnectionDataProvider
     */
    public function testGetConnection($connectionName, $testMerged, $expectedResult)
    {
        $config = $testMerged ? $this->configMerged : $this->config;
        $this->assertEquals($expectedResult, $config->getConnection($connectionName));
    }

    public function getConnectionDataProvider()
    {
        return array(
            'existing connection' => array(
                'connection_one', false, array('name' => 'connection_one', 'dbName' => 'db_one')
            ),
            'unknown connection' => array(
                'connection_new', false, null
            ),
            'existing connection, added' => array(
                'connection_new', true, array('name' => 'connection_new', 'dbName' => 'db_new')
            ),
            'existing connection, overridden' => array(
                'connection_one', true, array('name' => 'connection_one', 'dbName' => 'overridden_db_one')
            ),
        );
    }

    public function testGetConnections()
    {
        $this->assertEquals(self::$fixtureConfig['connection'], $this->config->getConnections());
        $this->assertEquals(self::$fixtureConfigMerged['connection'], $this->configMerged->getConnections());
    }

    public function testGetResources()
    {
        $this->assertEquals(self::$fixtureConfig['resource'], $this->config->getResources());
        $this->assertEquals(self::$fixtureConfigMerged['resource'], $this->configMerged->getResources());
    }

    public function testGetCacheFrontendSettings()
    {
        $this->assertEquals(
            self::$fixtureConfig['cache']['frontend'],
            $this->config->getCacheFrontendSettings()
        );
        $this->assertEquals(
            self::$fixtureConfigMerged['cache']['frontend'],
            $this->configMerged->getCacheFrontendSettings()
        );
    }

    /**
     * @param string $cacheType
     * @param bool $testMerged
     * @param string|null $expectedResult
     * @dataProvider getCacheTypeFrontendIdDataProvider
     */
    public function testGetCacheTypeFrontendId($cacheType, $testMerged, $expectedResult)
    {
        $config = $testMerged ? $this->configMerged : $this->config;
        $this->assertEquals($expectedResult, $config->getCacheTypeFrontendId($cacheType));
    }

    public function getCacheTypeFrontendIdDataProvider()
    {
        return array(
            'existing cache type'               => array('cache_type_one', false, 'cache_frontend_one'),
            'unknown cache type'                => array('cache_type_new', false, null),
            'existing cache type, added'        => array('cache_type_new', true, 'cache_frontend_two'),
            'existing cache type, overridden'   => array('cache_type_one', true, 'cache_frontend_new'),
        );
    }
}
