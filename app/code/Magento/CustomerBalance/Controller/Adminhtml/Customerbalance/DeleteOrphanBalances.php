<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
