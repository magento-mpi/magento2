<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\ActionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $this->model = new \Magento\Mview\ActionFactory($this->objectManagerMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage NotAction doesn't implement \Magento\Mview\ActionInterface
     */
    public function testGetWithException()
    {
        $notActionInterfaceMock = $this->getMock('NotAction', array(), array(), '', false);
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('NotAction')
            ->will($this->returnValue($notActionInterfaceMock));
        $this->model->get('NotAction');
    }

    public function testGet()
    {
        $actionInterfaceMock = $this->getMockForAbstractClass(
            'Magento\Mview\ActionInterface', array(), '', false
        );
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Mview\ActionInterface')
            ->will($this->returnValue($actionInterfaceMock));
        $this->model->get('Magento\Mview\ActionInterface');
        $this->assertInstanceOf('Magento\Mview\ActionInterface', $actionInterfaceMock);
    }
}
