<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class ProcessOrderCreationDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $importerMock;

    /**
     * @var \Magento\Reward\Model\Observer\ProcessOrderCreationData
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', ['isEnabledOnFront'], [], '', false);
        $this->importerMock = $this->getMock('\Magento\Reward\Model\PaymentDataImporter', [], [], '', false);

        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\ProcessOrderCreationData',
            ['rewardData' => $this->rewardDataMock, 'importer' => $this->importerMock]
        );
    }

    public function testPaymentDataImportIfRewardsDisabledOnFront()
    {
        $websiteId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['getStore', '__wakeup'], [], '', false);

        $orderCreateModel = $this->getMock('\Magento\Sales\Model\AdminOrder\Create', [], [], '', false);
        $orderCreateModel->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrderCreateModel'], [], '', false);
        $eventMock->expects($this->once())->method('getOrderCreateModel')->will($this->returnValue($orderCreateModel));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(false));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $quoteMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testPaymentDataImportIfPaymentNotSet()
    {
        $websiteId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['getStore', '__wakeup'], [], '', false);

        $orderCreateModel = $this->getMock('\Magento\Sales\Model\AdminOrder\Create', [], [], '', false);
        $orderCreateModel->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrderCreateModel', 'getRequest'], [], '', false);
        $eventMock->expects($this->once())->method('getOrderCreateModel')->will($this->returnValue($orderCreateModel));
        $eventMock->expects($this->once())->method('getRequest')->will($this->returnValue([]));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $quoteMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testPaymentDataImportIfUseRewardsNotSet()
    {
        $websiteId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', ['getStore', '__wakeup'], [], '', false);

        $orderCreateModel = $this->getMock('\Magento\Sales\Model\AdminOrder\Create', [], [], '', false);
        $orderCreateModel->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));

        $request = [
            'payment' => ['another_option' => true],
        ];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrderCreateModel', 'getRequest'], [], '', false);
        $eventMock->expects($this->once())->method('getOrderCreateModel')->will($this->returnValue($orderCreateModel));
        $eventMock->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $quoteMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testPaymentDataImportSuccess()
    {
        $websiteId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['getStore', '__wakeup', 'getPayment'],
            [],
            '',
            false
        );

        $orderCreateModel = $this->getMock('\Magento\Sales\Model\AdminOrder\Create', [], [], '', false);
        $orderCreateModel->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));

        $request = [
            'payment' => ['use_reward_points' => true],
        ];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrderCreateModel', 'getRequest'], [], '', false);
        $eventMock->expects($this->once())->method('getOrderCreateModel')->will($this->returnValue($orderCreateModel));
        $eventMock->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $paymentMock = $this->getMock('\Magento\Sales\Model\Quote\Payment', [], [], '', false);

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $quoteMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $quoteMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));

        $this->importerMock->expects($this->once())
            ->method('import')
            ->with($quoteMock, $paymentMock, true)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
