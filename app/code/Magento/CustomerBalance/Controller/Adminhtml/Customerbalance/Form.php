<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Controller\Adminhtml\Customerbalance;

class Form extends \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance
{
    /**
     * Customer balance form
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
