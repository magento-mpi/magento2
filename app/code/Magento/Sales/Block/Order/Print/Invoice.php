<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order details block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Sales_Block_Order_Print_Invoice extends Magento_Sales_Block_Items_Abstract
{
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Order # %1', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->helper('Magento_Payment_Helper_Data')->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }

    public function getPrintUrl()
    {
        return Mage::getUrl('*/*/print');
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    protected function _prepareItem(Magento_Core_Block_Abstract $renderer)
    {
        $renderer->setPrintStatus(true);
        return parent::_prepareItem($renderer);
    }

    /**
     * Get html of invoice totlas block
     *
     * @param   Magento_Sales_Model_Order_Invoice $invoice
     * @return  string
     */
    public function getInvoiceTotalsHtml($invoice)
    {
        $html = '';
        $totals = $this->getChildBlock('invoice_totals');
        if ($totals) {
            $totals->setInvoice($invoice);
            $html = $totals->toHtml();
        }
        return $html;
    }

}

