<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

class LastOrders extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer last orders grid for ajax
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
