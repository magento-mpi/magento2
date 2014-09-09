<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_view->getPage()->getConfig()->setTitle(__('Order by SKU'));
        $this->_view->renderLayout();
    }
}
