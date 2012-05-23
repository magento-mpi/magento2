<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Adminhtml_CheckoutControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('admin/checkout/loadBlock');
        $this->assertStringMatchesFormat('{"message":"%ACustomer not found%A"}', $this->getResponse()->getBody());
    }
}
