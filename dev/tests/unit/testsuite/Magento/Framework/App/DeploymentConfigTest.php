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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    protected function setUp()
    {
        $this->reader = $this->getMock('Magento\Framework\App\DeploymentConfig\Reader', [], [], '', false);
    }

    public function testGetters()
    {
        $this->reader->expects($this->once())->method('load')->willReturn(self::$fixture);
        $object = new DeploymentConfig($this->reader);
        $this->assertSame(self::$fixture, $object->get());
        $this->assertSame(self::$fixture, $object->get()); // second time to ensure loader will be invoked only once
        $this->assertSame('scalar_value', $object->getSegment('segment1'));
        $this->assertSame(self::$fixture['segment2'], $object->getSegment('segment2'));
        $this->assertTrue($object->isAvailable());
    }

    public function testNotAvailable()
    {
        $this->reader->expects($this->once())->method('load')->willReturn([]);
        $object = new DeploymentConfig($this->reader);
        $this->assertFalse($object->isAvailable());
    }

    public function testOverride()
    {
        $this->reader->expects($this->once())->method('load')->willReturn(self::$fixture);
        $overridden = ['segment2' => 'different_value', 'segment3' => 'new'];
        $object = new DeploymentConfig($this->reader, $overridden);
        $this->assertEquals('scalar_value', $object->getSegment('segment1'));
        $this->assertEquals('different_value', $object->getSegment('segment2'));
        $this->assertEquals('new', $object->getSegment('segment3'));
        $this->assertSame(
            ['segment1' => 'scalar_value', 'segment2' => 'different_value', 'segment3' => 'new'],
            $object->get()
        );
    }
}
