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

class View extends \Magento\Sales\Controller\Adminhtml\Transactions
{
    /**
     * View Transaction Details action
     *
     * @return void
     */
    public function execute()
    {
        $txn = $this->_initTransaction();
        if (!$txn) {
            return;
        }
        $this->_title->add(__('Transactions'));
        $this->_title->add(sprintf("#%s", $txn->getTxnId()));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_transactions');
        $this->_view->renderLayout();
    }
}
