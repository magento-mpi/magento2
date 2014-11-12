<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class ArgumentsTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Framework\App\Arguments
     */
    protected $_arguments;

    /**
     * @var \Magento\Framework\App\Arguments
     */
    protected $_argumentsMerged;

    public static function setUpBeforeClass()
    {
        self::$fixtureConfig = require __DIR__ . '/Arguments/_files/config.php';
        self::$fixtureConfigMerged = require __DIR__ . '/Arguments/_files/other/local_developer_merged.php';
    }

    protected function setUp()
    {
        $loader = $this->getMock('Magento\Framework\App\Arguments\Loader', array(), array(), '', false);
        $loader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));

        $this->_arguments = new \Magento\Framework\App\Arguments(array(), $loader);
        $this->_argumentsMerged = new \Magento\Framework\App\Arguments(
            require __DIR__ . '/Arguments/_files/other/local_developer.php',
            $loader
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
        $arguments = $testMerged ? $this->_argumentsMerged : $this->_arguments;
        $this->assertEquals($expectedResult, $arguments->getConnection($connectionName));
    }

    public function getConnectionDataProvider()
    {
        return array(
            'existing connection' => array(
                'connection_one',
                false,
                array('name' => 'connection_one', 'dbname' => 'db_one')
            ),
            'unknown connection' => array('connection_new', false, null),
            'existing connection, added' => array(
                'connection_new',
                true,
                array('name' => 'connection_new', 'dbname' => 'db_new')
            ),
            'existing connection, overridden' => array(
                'connection_one',
                true,
                array('name' => 'connection_one', 'dbname' => 'overridden_db_one')
            )
        );
    }

    public function testGetConnections()
    {
        $this->assertEquals(self::$fixtureConfig['db']['connection'], $this->_arguments->getConnections());
        $this->assertEquals(self::$fixtureConfigMerged['db']['connection'], $this->_argumentsMerged->getConnections());
    }

    public function testGetResources()
    {
        $this->assertEquals(self::$fixtureConfig['resource'], $this->_arguments->getResources());
        $this->assertEquals(self::$fixtureConfigMerged['resource'], $this->_argumentsMerged->getResources());
    }

    public function testGetCacheFrontendSettings()
    {
        $this->assertEquals(self::$fixtureConfig['cache']['frontend'], $this->_arguments->getCacheFrontendSettings());
        $this->assertEquals(
            self::$fixtureConfigMerged['cache']['frontend'],
            $this->_argumentsMerged->getCacheFrontendSettings()
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
        $arguments = $testMerged ? $this->_argumentsMerged : $this->_arguments;
        $this->assertEquals($expectedResult, $arguments->getCacheTypeFrontendId($cacheType));
    }

    public function getCacheTypeFrontendIdDataProvider()
    {
        return array(
            'existing cache type' => array('cache_type_one', false, 'cache_frontend_one'),
            'unknown cache type' => array('cache_type_new', false, null),
            'existing cache type, added' => array('cache_type_new', true, 'cache_frontend_two'),
            'existing cache type, overridden' => array('cache_type_one', true, 'cache_frontend_new')
        );
    }
}
