<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Transactions;

use \Magento\Backend\App\Action;

class Index extends \Magento\Sales\Controller\Adminhtml\Transactions
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Transactions'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_transactions');
        $this->_view->renderLayout();
    }
}
