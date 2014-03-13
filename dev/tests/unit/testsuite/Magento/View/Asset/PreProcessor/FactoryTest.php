<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\PreProcessor\Factory
     */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->factory = new \Magento\View\Asset\PreProcessor\Factory($this->objectManagerMock);
    }

    public function testGetPreProcessorsCss()
    {
        $moduleNotation = $this->getMock('Magento\View\Asset\PreProcessor\ModuleNotation', array(), array(), '', false);
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\View\Asset\PreProcessor\ModuleNotation')
            ->will($this->returnValue($moduleNotation));

        $this->assertSame(array($moduleNotation), $this->factory->getPreProcessors('css'));
    }

    public function testGetPreProcessorsLess()
    {
        //should be changed when Less is implemented
        $this->assertEquals(array(), $this->factory->getPreProcessors('less'));
    }

    public function testGetPreProcessorsDefault()
    {
        $this->assertEquals(array(), $this->factory->getPreProcessors('default value'));
    }
}
