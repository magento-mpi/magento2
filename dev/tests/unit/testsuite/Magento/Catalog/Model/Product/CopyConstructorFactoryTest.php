<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

class CopyConstructorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CopyConstructorFactory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;
    
    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('\Magento\ObjectManager');
        $this->_model = new CopyConstructorFactory($this->_objectManagerMock);
    }

    public function testCreateWithInvalidType()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            '\Magento\Object does not implement \Magento\Catalog\Model\Product\CopyConstructorInterface'
        );
        $this->_objectManagerMock->expects($this->never())->method('create');
        $this->_model->create('\Magento\Object');
    }

    public function testCreateWithValidType()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')->with('\Magento\Catalog\Model\Product\CopyConstructor\Composite')
            ->will($this->returnValue('object'));
        $this->assertEquals(
            'object',
            $this->_model->create('\Magento\Catalog\Model\Product\CopyConstructor\Composite')
        );
    }
}
