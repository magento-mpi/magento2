<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends \Magento\Backend\Utility\Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/checkout/index/loadBlock');
        $this->assertStringMatchesFormat('{"message":"%ACustomer not found%A"}', $this->getResponse()->getBody());
    }
}
