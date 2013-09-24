<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Phrase\Renderer\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_factory = $objectManagerHelper->getObject('Magento\Phrase\Renderer\Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreate()
    {
        $className = 'class-name';
        $rendererMock = $this->getMock('Magento\Phrase\RendererInterface');

        $this->_objectManager->expects($this->once())->method('get')->with($className)
            ->will($this->returnValue($rendererMock));

        $this->assertEquals($rendererMock, $this->_factory->create($className));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrong renderer class-name
     */
    public function testWrongRendererException()
    {
        $className = 'class-name';
        $rendererMock = $this->getMock('WrongInterface');

        $this->_objectManager->expects($this->once())->method('get')->with($className)
            ->will($this->returnValue($rendererMock));

        $this->_factory->create($className);
    }
}
