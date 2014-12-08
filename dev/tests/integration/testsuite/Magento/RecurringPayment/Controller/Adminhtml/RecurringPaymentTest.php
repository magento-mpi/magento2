<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringPayment\Controller\Adminhtml;


class RecurringPaymentTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/RecurringPayment/_files/recurring_payment.php
     */
    public function testCustomerGridAction()
    {
        $this->getRequest()->setParam(RecurringPayment::PARAM_CUSTOMER_ID, 1);
        $this->dispatch('backend/sales/recurringPayment/customerGrid');
        $this->assertContains('Test Schedule', $this->getResponse()->getBody());
    }
}
