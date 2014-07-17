<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Create;

use \Magento\Backend\App\Action;

class Index extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Orders'));
        $this->_title->add(__('New Order'));
        $this->_initSession();
        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Sales::sales_order');
        $this->_view->renderLayout();
    }
}
