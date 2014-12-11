<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
