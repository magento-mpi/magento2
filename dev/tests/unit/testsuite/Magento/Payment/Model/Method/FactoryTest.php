<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Payment\Model\Method;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_factory = $objectManagerHelper->getObject(
            'Magento\Payment\Model\Method\Factory',
            ['objectManager' => $this->_objectManagerMock]
        );
    }

    public function testCreateMethod()
    {
        $className = 'Magento\Payment\Model\Method\AbstractMethod';
        $methodMock = $this->getMock($className, [], [], '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            []
        )->will(
            $this->returnValue($methodMock)
        );

        $this->assertEquals($methodMock, $this->_factory->create($className));
    }

    public function testCreateMethodWithArguments()
    {
        $className = 'Magento\Payment\Model\Method\AbstractMethod';
        $data = ['param1', 'param2'];
        $methodMock = $this->getMock($className, [], [], '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            $data
        )->will(
            $this->returnValue($methodMock)
        );

        $this->assertEquals($methodMock, $this->_factory->create($className, $data));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage WrongClass class doesn't implement \Magento\Payment\Model\MethodInterface
     */
    public function testWrongTypeException()
    {
        $className = 'WrongClass';
        $methodMock = $this->getMock($className, [], [], '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            []
        )->will(
            $this->returnValue($methodMock)
        );

        $this->_factory->create($className);
    }
}
