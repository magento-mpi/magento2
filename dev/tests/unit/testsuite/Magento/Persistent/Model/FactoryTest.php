<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Persistent\Model\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
        $this->_factory = $helper->getObject(
            'Magento\Persistent\Model\Factory',
            array('objectManager' => $this->_objectManagerMock)
        );
    }

    public function testCreate()
    {
        $className = 'SomeModel';

        $classMock = $this->getMock('SomeModel');
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            array()
        )->will(
            $this->returnValue($classMock)
        );

        $this->assertEquals($classMock, $this->_factory->create($className));
    }

    public function testCreateWithArguments()
    {
        $className = 'SomeModel';
        $data = array('param1', 'param2');

        $classMock = $this->getMock('SomeModel');
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            $data
        )->will(
            $this->returnValue($classMock)
        );

        $this->assertEquals($classMock, $this->_factory->create($className, $data));
    }
}
