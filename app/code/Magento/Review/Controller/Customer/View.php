<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Customer;

class View extends \Magento\Review\Controller\Customer
{
    /**
     * Render review details
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        if ($navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        $this->_view->getPage()->getConfig()->setTitle(__('Review Details'));
        $this->_view->renderLayout();
    }
}
