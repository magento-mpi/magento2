<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class ApplyRewardSalesrulePointsTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplyRewardSalesrulePoints */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->_objectManagerHelper->getObject(
            'Magento\Reward\Model\Observer\ApplyRewardSalesrulePoints'
        );
        $this->eventMock = $this->getMock(
            '\Magento\Framework\Event',
            array('getCart', 'getInvoice'),
            array(),
            '',
            false
        );
        $this->observerMock = $this->getMock('\Magento\Framework\Event\Observer', array(), array(), '', false);
        $this->observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->eventMock));
    }

    public function testApplyRewardSalesrulePoints()
    {
        $invoiceMock = $this->getMock('\Magento\Sales\Model\Order\Invoice', array(), array(), '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', array(), array(), '', false);

        $this->eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $invoiceMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $invoiceMock->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $orderMock->expects($this->never())->method('getCustomerId');

        $this->model->execute($this->observerMock);
    }
} 
