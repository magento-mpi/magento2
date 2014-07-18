<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice;

abstract class View extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_invoice');
    }

    /**
     * Invoice information page
     *
     * @return void
     */
    public function execute()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $this->_forward('view', 'order_invoice', null, array('come_from' => 'invoice'));
        } else {
            $this->_forward('noroute');
        }
    }
}
