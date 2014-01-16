<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Billing\Agreement\View\Tab;

class InfoTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/billing_agreement.php
     */
    public function testCustomerGridAction()
    {
        /** @var \Magento\Sales\Model\Resource\Billing\Agreement\Collection $billingAgreement */
        $billingAgreementCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Billing\Agreement\Collection')
            ->load();
        $agreementId = $billingAgreementCollection->getFirstItem()->getId();
        $this->dispatch('backend/sales/billing_agreement/view/agreement/' . $agreementId);

        $this->assertSelectCount('a[name="billing_agreement_info"]', 1, $this->getResponse()->getBody(),
           'Response for billing agreement info doesn\'t contain billing agreement info tab');

        $this->assertSelectRegExp('a', '/customer\@example.com/', 1, $this->getResponse()->getBody(),
            'Response for billing agreement info doesn\'t contain Customer info');
    }
}