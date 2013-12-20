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
        $customer = $this->getMock('Magento\Customer\Model\Customer', array(), array(), '', false);
        $customer->expects($this->once())->method('getName')->will($this->returnValue('John Doe'));

        $escapedName = new \stdClass();
        $escaper = $this->getMock('Magento\Escaper', array(), array(), '', false);
        $escaper
            ->expects($this->once())->method('escapeHtml')->with('John Doe')->will($this->returnValue($escapedName));

        $context = $this->getMock('Magento\View\Element\Template\Context', array(), array(), '', false);
        $context->expects($this->once())->method('getEscaper')->will($this->returnValue($escaper));

        $session = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);
        $session->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $block = new \Magento\Customer\Block\Account\Customer($context, $session);

        $this->assertSame($escapedName, $block->getCustomerName());
    }
}
