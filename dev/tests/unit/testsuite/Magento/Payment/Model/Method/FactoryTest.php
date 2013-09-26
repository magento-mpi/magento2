<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Method;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $this->_factory = $objectManagerHelper->getObject('Magento\Payment\Model\Method\Factory', array(
            'objectManager' => $this->_objectManagerMock,
        ));
    }

    public function testCreateMethod()
    {
        $className = 'Magento\Payment\Model\Method\AbstractMethod';
        $methodMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($methodMock));

        $this->assertEquals($methodMock, $this->_factory->create($className));
    }

    public function testCreateMethodWithArguments()
    {
        $className = 'Magento\Payment\Model\Method\AbstractMethod';
        $data = array('param1', 'param2');
        $methodMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, $data)
            ->will($this->returnValue($methodMock));

        $this->assertEquals($methodMock, $this->_factory->create($className, $data));
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage WrongClass class doesn't extend \Magento\Payment\Model\Method\AbstractMethod
     */
    public function testWrongTypeException()
    {
        $className = 'WrongClass';
        $methodMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($methodMock));

        $this->_factory->create($className);
    }
}
