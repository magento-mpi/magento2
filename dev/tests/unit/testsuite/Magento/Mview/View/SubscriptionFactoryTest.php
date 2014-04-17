<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\View;

class SubscriptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\View\SubscriptionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager', array(), array(), '', false);
        $this->model = new SubscriptionFactory($this->objectManagerMock);
    }

    public function testCreate()
    {
        $subscriptionInterfaceMock = $this->getMockForAbstractClass(
            'Magento\Mview\View\SubscriptionInterface', array(), '', false
        );
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Mview\View\SubscriptionInterface', ['some_data'])
            ->will($this->returnValue($subscriptionInterfaceMock));
        $this->assertEquals($subscriptionInterfaceMock, $this->model->create(['some_data']));
    }
}
