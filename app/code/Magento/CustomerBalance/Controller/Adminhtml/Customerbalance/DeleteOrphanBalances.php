<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Controller\Adminhtml\Customerbalance;

class DeleteOrphanBalances extends \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance
{
    /**
     * Delete orphan balances
     *
     * @return void
     */
    public function execute()
    {
        $this->_balance->deleteBalancesByCustomerId((int)$this->getRequest()->getParam('id'));
        $this->_redirect('customer/index/edit/', ['_current' => true]);
    }
}
