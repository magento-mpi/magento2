<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;

class DeploymentConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private static $fixture = [
        'segment1' => 'scalar_value',
        'segment2' => [
            'foo' => 1,
            'bar' => ['baz' => 2],
        ]
    ];

    /**
     * @var array
     */
    private static $flattenedFixture = [
        'segment1' => 'scalar_value',
        'segment2.foo' => 1,
        'segment2.bar.baz' => 2,
    ];

    /**
     * @var array
     */
    protected static $fixtureConfig;

    /**
     * @var array
     */
    protected static $fixtureConfigMerged;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $_deploymentConfig;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $_deploymentConfigMerged;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    public static function setUpBeforeClass()
    {
        self::$fixtureConfig = require __DIR__ . '/_files/config.php';
        self::$fixtureConfigMerged = require __DIR__ . '/_files/other/local_developer_merged.php';
    }

    protected function setUp()
    {
        $this->reader = $this->getMock('Magento\Framework\App\DeploymentConfig\Reader', [], [], '', false);
        $this->_deploymentConfig = new \Magento\Framework\App\DeploymentConfig($this->reader, array());
        $this->_deploymentConfigMerged = new \Magento\Framework\App\DeploymentConfig(
            $this->reader,
            require __DIR__ . '/_files/other/local_developer.php'
        );
    }

    public function testGetters()
    {
        $this->reader->expects($this->once())->method('load')->willReturn(self::$fixture);
        $this->assertSame(self::$flattenedFixture, $this->_deploymentConfig->get());
        // second time to ensure loader will be invoked only once
        $this->assertSame(self::$flattenedFixture, $this->_deploymentConfig->get());
        $this->assertSame('scalar_value', $this->_deploymentConfig->getSegment('segment1'));
        $this->assertSame(self::$fixture['segment2'], $this->_deploymentConfig->getSegment('segment2'));
        $this->assertTrue($this->_deploymentConfig->isAvailable());
    }

    public function testNotAvailable()
    {
        $this->reader->expects($this->once())->method('load')->willReturn([]);
        $object = new DeploymentConfig($this->reader);
        $this->assertFalse($object->isAvailable());
    }

    /**
     * @param string $connectionName
     * @param bool $testMerged
     * @param array|null $expectedResult
     * @dataProvider getConnectionDataProvider
     */
    public function testGetConnection($connectionName, $testMerged, $expectedResult)
    {
        $this->reader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));
        $arguments = $testMerged ? $this->_deploymentConfigMerged : $this->_deploymentConfig;
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
        $this->reader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));
        $this->assertEquals(self::$fixtureConfig['db']['connection'], $this->_deploymentConfig->getConnections());
        $this->assertEquals(
            self::$fixtureConfigMerged['db']['connection'],
            $this->_deploymentConfigMerged->getConnections()
        );
    }

    public function testGetResources()
    {
        $this->reader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));
        $this->assertEquals(self::$fixtureConfig['resource'], $this->_deploymentConfig->getResources());
        $this->assertEquals(self::$fixtureConfigMerged['resource'], $this->_deploymentConfigMerged->getResources());
    }

    public function testGetCacheFrontendSettings()
    {
        $this->reader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));
        $this->assertEquals(
            self::$fixtureConfig['cache']['frontend'],
            $this->_deploymentConfig->getCacheFrontendSettings()
        );
        $this->assertEquals(
            self::$fixtureConfigMerged['cache']['frontend'],
            $this->_deploymentConfigMerged->getCacheFrontendSettings()
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
        $this->reader->expects($this->atLeastOnce())->method('load')->will($this->returnValue(self::$fixtureConfig));
        $arguments = $testMerged ? $this->_deploymentConfigMerged : $this->_deploymentConfig;
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
