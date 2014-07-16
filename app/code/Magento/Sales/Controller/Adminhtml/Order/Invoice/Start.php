<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

class Start extends \Magento\Backend\App\Action
{
    /**
     * Start create invoice action
     *
     * @return void
     */
    public function execute()
    {
        /**
         * Clear old values for invoice qty's
         */
        $this->_getSession()->getInvoiceItemQtys(true);
        $this->_redirect('sales/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }
}
