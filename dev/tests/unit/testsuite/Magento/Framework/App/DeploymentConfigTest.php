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
        'segment2/foo' => 1,
        'segment2/bar/baz' => 2,
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
    }

    public function testIsAvailable()
    {
        $this->reader->expects($this->once())->method('load')->willReturn(['a' => 1]);
        $object = new DeploymentConfig($this->reader);
        $this->assertTrue($object->isAvailable());
    }

    public function testNotAvailable()
    {
        $this->reader->expects($this->once())->method('load')->willReturn([]);
        $object = new DeploymentConfig($this->reader);
        $this->assertFalse($object->isAvailable());
    }

    /**
     * @param array $data
     * @expectedException \Exception
     * @expectedExceptionMessage Array key collision in deployment configuration file.
     * @dataProvider keyCollisionDataProvider
     */
    public function testKeyCollision(array $data)
    {
        $this->reader->expects($this->once())->method('load')->willReturn($data);
        $object = new DeploymentConfig($this->reader);
        $object->reload();
    }

    public function keyCollisionDataProvider()
    {
        return [
            [
                ['foo' => ['bar' => '1'], 'foo/bar' => '2'],
                ['foo/bar' => '1', 'foo' => ['bar' => '2']],
            ]
        ];
    }

}
