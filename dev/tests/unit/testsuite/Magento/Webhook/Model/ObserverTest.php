<?php
/**
 * \Magento\Webhook\Model\Observer
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Webhook\Model\Observer */
    protected $_observer;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_webapiEventHandler;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_subscriptionSet;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_logger;

    protected function setUp()
    {
        $this->_webapiEventHandler = $this->_getBasicMock('Magento\Webhook\Model\Webapi\EventHandler');
        $this->_subscriptionSet = $this->_getBasicMock('Magento\Webhook\Model\Resource\Subscription\Collection');
        $this->_logger = $this->_getBasicMock('Magento\Core\Model\Logger');

        $this->_observer = new \Magento\Webhook\Model\Observer(
            $this->_webapiEventHandler,
            $this->_subscriptionSet,
            $this->_logger
        );
    }

    /**
     * @param string $className
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getBasicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAfterWebapiUserDeleteSuccess()
    {

        $mockSubscription = $this->getMockBuilder('Magento\Webhook\Model\Subscription')
            ->disableOriginalConstructor()
            ->setMethods(array('setStatus', 'save'))
            ->getMock();

        $this->_subscriptionSet->expects($this->once())
            ->method('getActivatedSubscriptionsWithoutApiUser')
            ->withAnyParameters()
            ->will($this->returnValue(array($mockSubscription)));

        $mockSubscription->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE))
            ->will($this->returnSelf());

        $mockSubscription->expects($this->once())
            ->method('save');

        $this->_logger->expects($this->never())
            ->method('logException');

        $this->_observer->afterWebapiUserDelete();
    }

    public function testAfterWebapiUserDeleteWithException()
    {

        $mockSubscription = $this->getMockBuilder('Magento\Webhook\Model\Subscription')
            ->disableOriginalConstructor()
            ->setMethods(array('setStatus', 'save'))
            ->getMock();

        $this->_subscriptionSet->expects($this->once())
            ->method('getActivatedSubscriptionsWithoutApiUser')
            ->withAnyParameters()
            ->will($this->returnValue(array($mockSubscription)));

        $mockSubscription->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Webhook\Model\Subscription::STATUS_INACTIVE))
            ->will($this->returnSelf());

        $exception = new \Exception('exception');
        $mockSubscription->expects($this->once())
            ->method('save')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_logger->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));

        $this->_observer->afterWebapiUserDelete();
    }

    public function testAfterWebapiUserChange()
    {
        $mockObserver = $this->_getBasicMock('Magento\Event\Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento\Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $model = 'model';
        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->returnValue($model));

        $this->_webapiEventHandler->expects($this->once())
            ->method('userChanged')
            ->with($this->equalTo($model));

        $this->_observer->afterWebapiUserChange($mockObserver);
    }

    public function testAfterWebapiUserChangeWithException()
    {
        $mockObserver = $this->_getBasicMock('Magento\Event\Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento\Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $exception = new \Exception('exception');
        $this->_logger->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));

        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_observer->afterWebapiUserChange($mockObserver);
    }

    public function testAfterWebapiRoleChange()
    {
        $mockObserver = $this->_getBasicMock('Magento\Event\Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento\Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $model = 'model';
        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->returnValue($model));

        $this->_webapiEventHandler->expects($this->once())
            ->method('roleChanged')
            ->with($this->equalTo($model));

        $this->_observer->afterWebapiRoleChange($mockObserver);
    }

    public function testAfterWebapiRoleChangeWithException()
    {
        $mockObserver = $this->_getBasicMock('Magento\Event\Observer');
        $mockVarienEvent = $this->getMockBuilder('Magento\Event')
            ->setMethods(array('getObject'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockObserver->expects($this->once())
            ->method('getEvent')
            ->withAnyParameters()
            ->will($this->returnValue($mockVarienEvent));

        $exception = new \Exception('exception');
        $this->_logger->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));

        $mockVarienEvent->expects($this->once())
            ->method('getObject')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_observer->afterWebapiRoleChange($mockObserver);
    }
}
