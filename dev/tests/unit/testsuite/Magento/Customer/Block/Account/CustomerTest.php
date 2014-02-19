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

        $sessionMock = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $customerServiceMock = $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $customerMock = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $customerMock->expects($this->any())->method('getName')->will($this->returnValue($customerName));

        $converterMock = $this->getMockBuilder('Magento\Customer\Model\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $converterMock->expects($this->any())->method('getCustomerModel')->will($this->returnValue($customerMock));

        $escapedName = new \stdClass();
        $escaperMock = $this->getMockBuilder('Magento\Escaper')
            ->disableOriginalConstructor()
            ->getMock();
        $escaperMock->expects($this->any())->method('escapeHtml')->with($customerName)
            ->will($this->returnValue($escapedName));

        $contextMock = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())->method('getEscaper')->will($this->returnValue($escaperMock));

        $block = new \Magento\Customer\Block\Account\Customer($contextMock, $sessionMock, $converterMock, $customerServiceMock);

        $this->assertSame($escapedName, $block->getCustomerName());
    }
}
