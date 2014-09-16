<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Method;

class FreeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Payment\Model\Method\Free */
    protected $methodFree;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $paymentData  = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $logAdapterFactory = $this->getMock('Magento\Framework\Logger\AdapterFactory', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Framework\StoreManagerInterface', [], [], '', false);

        $this->methodFree = new \Magento\Payment\Model\Method\Free(
            $eventManager,
            $paymentData,
            $this->scopeConfig,
            $logAdapterFactory,
            $this->storeManager
        );
    }

    /**
     * @param string $orderStatus
     * @param string $paymentAction
     * @param mixed $result
     * @dataProvider getConfigPaymentActionProvider
     */
    public function testGetConfigPaymentAction($orderStatus, $paymentAction, $result)
    {
        $this->scopeConfig->expects($this->at(0))
            ->method('getValue')
            ->will($this->returnValue($orderStatus));

        if ($orderStatus != 'pending') {
            $this->scopeConfig->expects($this->at(1))
                ->method('getValue')
                ->will($this->returnValue($paymentAction));
        }
        $this->assertEquals($result, $this->methodFree->getConfigPaymentAction());
    }

    /**
     * @param float $grandTotal
     * @param bool $isActive
     * @param bool $notEmptyQuote
     * @param bool $result
     * @dataProvider getIsAvailableProvider
     */
    public function testIsAvailable($grandTotal, $isActive, $notEmptyQuote, $result)
    {
        $quote = null;
        if ($notEmptyQuote) {
            $quote = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
            $quote->expects($this->any())
                ->method('__call')
                ->with($this->equalTo('getGrandTotal'))
                ->will($this->returnValue($grandTotal));
        }

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->any())
            ->method('roundPrice')
            ->will($this->returnArgument(0));

        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($isActive));

        $this->assertEquals($result, $this->methodFree->isAvailable($quote));
    }

    /**
     * @return array
     */
    public function getIsAvailableProvider()
    {
        return [
            [0, true, true, true],
            [0.1, true, true, false],
            [0, false, false, false],
            [1, true, false, false],
            [0, true, false, false]
        ];
    }

    /**
     * @return array
     */
    public function getConfigPaymentActionProvider()
    {
        return [
            ['pending', 'action', null],
            ['processing', 'payment_action', 'payment_action']
        ];
    }
}
