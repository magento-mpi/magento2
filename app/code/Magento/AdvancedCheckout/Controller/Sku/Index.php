<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Sku;

class Index extends \Magento\AdvancedCheckout\Controller\Sku
{
    /**
     * View Order by SKU page in 'My Account' section
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Order by SKU'));
        $this->_view->renderLayout();
    }
}
