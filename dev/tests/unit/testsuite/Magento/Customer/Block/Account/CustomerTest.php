<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCustomerName()
    {
        $customerName = 'John Doe';

        $customer = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $customerServiceMock = $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerAccountServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $customerServiceMock->expects($this->any())->method('getCustomer')->will($this->returnValue($customer));

        $viewHelperMock = $this->getMockBuilder('Magento\Customer\Helper\View')
            ->disableOriginalConstructor()
            ->getMock();
        $viewHelperMock->expects($this->any())->method('getCustomerName')->will($this->returnValue($customerName));

        $escaperMock = $this->getMockBuilder('Magento\Escaper')
            ->disableOriginalConstructor()
            ->getMock();
        $escaperMock->expects($this->any())->method('escapeHtml')->with($customerName)
            ->will($this->returnValue($customerName));

        $contextMock = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())->method('getEscaper')->will($this->returnValue($escaperMock));

        $httpContextMock = $this->getMockBuilder('Magento\App\Http\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $customerCurrent = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerCurrentService')
            ->disableOriginalConstructor()
            ->getMock();

        $block = new \Magento\Customer\Block\Account\Customer($contextMock, $customerServiceMock,
            $viewHelperMock, $httpContextMock, $customerCurrent);

        $this->assertSame($customerName, $block->getCustomerName());
    }
}
