<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Mview;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Mview\ActionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager', array(), array(), '', false);
        $this->model = new \Magento\Framework\Mview\ActionFactory($this->objectManagerMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage NotAction doesn't implement \Magento\Framework\Mview\ActionInterface
     */
    public function testGetWithException()
    {
        $notActionInterfaceMock = $this->getMock('NotAction', array(), array(), '', false);
        $this->objectManagerMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'NotAction'
        )->will(
            $this->returnValue($notActionInterfaceMock)
        );
        $this->model->get('NotAction');
    }

    public function testGet()
    {
        $actionInterfaceMock = $this->getMockForAbstractClass(
            'Magento\Framework\Mview\ActionInterface',
            array(),
            '',
            false
        );
        $this->objectManagerMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'Magento\Framework\Mview\ActionInterface'
        )->will(
            $this->returnValue($actionInterfaceMock)
        );
        $this->model->get('Magento\Framework\Mview\ActionInterface');
        $this->assertInstanceOf('Magento\Framework\Mview\ActionInterface', $actionInterfaceMock);
    }
}
