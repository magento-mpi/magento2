<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

class Carts extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Get shopping carts from all websites for specified client
     *
     * @return void
     */
    public function execute()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
