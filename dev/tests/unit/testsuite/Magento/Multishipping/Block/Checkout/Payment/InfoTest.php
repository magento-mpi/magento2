<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Multishipping\Block\Checkout\Payment;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Info
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $multiShippingMock;

    protected function setUp()
    {
        $this->multiShippingMock =
            $this->getMock('Magento\Multishipping\Model\Checkout\Type\Multishipping', [], [], '', false);
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Multishipping\Block\Checkout\Payment\Info',
            [
                'multishipping' => $this->multiShippingMock,
            ]
        );
    }

    public function testGetPaymentInfo()
    {
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $paymentInfoMock = $this->getMock('Magento\Payment\Model\Info', [], [], '', false);
        $this->multiShippingMock->expects($this->once())->method('getQuote')->willReturn($quoteMock);
        $quoteMock->expects($this->once())->method('getPayment')->willReturn($paymentInfoMock);

        $this->model->getPaymentInfo();
    }
}
