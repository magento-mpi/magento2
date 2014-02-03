<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Controller\Adminhtml;

class RecurringProfileTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/recurring_profile.php
     */
    public function testCustomerGridAction()
    {
        $this->getRequest()->setParam(RecurringProfile::PARAM_CUSTOMER_ID, 1);
        $this->dispatch('backend/sales/recurring_profile/customerGrid');
        $this->assertContains(FIXTURE_RECURRING_PROFILE_SCHEDULE_DESCRIPTION, $this->getResponse()->getBody());
    }
}
