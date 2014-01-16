<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Checkout\Model\Session
 */
namespace Magento\Checkout\Model;
include(__DIR__ . '/../_files/session.php');

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int|null $orderId
     * @param int|null $incrementId
     * @param \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject $orderMock
     * @dataProvider getLastRealOrderDataProvider
     */
    public function testGetLastRealOrder($orderId, $incrementId, $orderMock)
    {
        $orderFactory = $this->getMockBuilder('Magento\Sales\Model\OrderFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $orderFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderMock));

        $messageCollFactory = $this->getMockBuilder('Magento\Message\CollectionFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $quoteFactory = $this->getMockBuilder('Magento\Sales\Model\QuoteFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $appState = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $appState->expects($this->any())->method('isInstalled')->will($this->returnValue(true));

        $request = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $request->expects($this->any())->method('getHttpHost')->will($this->returnValue(array()));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\Checkout\Model\Session',
            array(
                'request' => $this->getMock('Magento\App\RequestInterface', array(), array(), '', false),
                'orderFactory' => $orderFactory,
                'messageCollFactory' => $messageCollFactory,
                'quoteFactory' => $quoteFactory,
                'storage' => new \Magento\Session\Storage
            )
        );
        /** @var \Magento\Checkout\Model\Session $session */
        $session = $objectManager->getObject('Magento\Checkout\Model\Session', $constructArguments);
        $session->setLastRealOrderId($orderId);

        $this->assertSame($orderMock, $session->getLastRealOrder());
        if ($orderId == $incrementId) {
            $this->assertSame($orderMock, $session->getLastRealOrder());
        }
    }

    /**
     * @return array
     */
    public function getLastRealOrderDataProvider()
    {
        return array(
            array(null, 1, $this->_getOrderMock(1, null)),
            array(1, 1, $this->_getOrderMock(1, 1)),
            array(1, null, $this->_getOrderMock(null, 1))
        );
    }

    /**
     * @param int|null $incrementId
     * @param int|null $orderId
     * @return \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getOrderMock($incrementId, $orderId)
    {
        /** @var $order \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Order */
        $order = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(array('getIncrementId', 'loadByIncrementId', '__sleep', '__wakeup'))
            ->getMock();

        $order->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue($incrementId));

        if ($orderId) {
            $order->expects($this->once())
            ->method('loadByIncrementId')
            ->with($orderId);
        }

        if ($orderId == $incrementId) {
            $order->expects($this->once())
                ->method('getIncrementId')
                ->will($this->returnValue($incrementId));
        }

        return $order;
    }
}
