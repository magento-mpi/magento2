<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_AdvancedCheckout_Controller_Adminhtml_CheckoutTest extends Magento_Backend_Utility_Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/checkout/loadBlock');
        $this->assertStringMatchesFormat('{"message":"%ACustomer not found%A"}', $this->getResponse()->getBody());
    }
}
