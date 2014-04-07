<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\Express;

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Express\Checkout
     */
    protected $_checkoutModel;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteMock;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $paypalConfigMock = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);
        $this->_quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $this->_checkoutModel = $this->_objectManager->getObject(
            'Magento\Paypal\Model\Express\Checkout',
            ['params' => ['quote' => $this->_quoteMock, 'config' => $paypalConfigMock]]
        );
        parent::setUp();
    }

    public function testSetCustomerData()
    {
        /** @var \Magento\Customer\Service\V1\Data\Customer $customerDataMock */
        $customerDataMock = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $this->_quoteMock->expects($this->once())->method('assignCustomer')->with($customerDataMock);
        $customerDataMock->expects($this->once())->method('getId');
        $this->_checkoutModel->setCustomerData($customerDataMock);
    }

    public function testSetCustomerWithAddressChange()
    {
        /** @var \Magento\Customer\Service\V1\Data\Customer $customerDataMock */
        $customerDataMock = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        /** @var \Magento\Sales\Model\Quote\Address $customerDataMock */
        $quoteAddressMock = $this->getMock('Magento\Sales\Model\Quote\Address', [], [], '', false);
        $this->_quoteMock
            ->expects($this->once())
            ->method('assignCustomerWithAddressChange')
            ->with($customerDataMock, $quoteAddressMock, $quoteAddressMock);
        $customerDataMock->expects($this->once())->method('getId');
        $this->_checkoutModel->setCustomerWithAddressChange($customerDataMock, $quoteAddressMock, $quoteAddressMock);
    }
}
