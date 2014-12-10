<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
            ['getCart', 'getInvoice'],
            [],
            '',
            false
        );
        $this->observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->eventMock));
    }

    public function testApplyRewardSalesrulePoints()
    {
        $invoiceMock = $this->getMock('\Magento\Sales\Model\Order\Invoice', [], [], '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);

        $this->eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $invoiceMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $invoiceMock->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $orderMock->expects($this->never())->method('getCustomerId');

        $this->model->execute($this->observerMock);
    }
}
