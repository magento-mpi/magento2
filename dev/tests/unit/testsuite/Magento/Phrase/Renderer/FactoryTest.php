<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Phrase_Renderer_Factory
     */
    protected $_factory;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_factory = $objectManagerHelper->getObject('Magento_Phrase_Renderer_Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreate()
    {
        $className = 'class-name';
        $rendererMock = $this->getMock('Magento_Phrase_RendererInterface');

        $this->_objectManager->expects($this->once())->method('get')->with($className)
            ->will($this->returnValue($rendererMock));

        $this->assertEquals($rendererMock, $this->_factory->create($className));
    }

    /**
     * @expectedException InvalidArgumentException
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
