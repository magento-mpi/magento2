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
 * Sales order view items block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Order_Invoice_Items extends Magento_Sales_Block_Items_Abstract
{
    /**
     * Retrieve current order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getPrintInvoiceUrl($invoice)
    {
        return Mage::getUrl('*/*/printInvoice', array('invoice_id' => $invoice->getId()));
    }

    public function getPrintAllInvoicesUrl($order)
    {
        return Mage::getUrl('*/*/printInvoice', array('order_id' => $order->getId()));
    }

    /**
     * Get html of invoice totals block
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

    /**
     * Get html of invoice comments block
     *
     * @param   Magento_Sales_Model_Order_Invoice $invoice
     * @return  string
     */
    public function getInvoiceCommentsHtml($invoice)
    {
        $html = '';
        $comments = $this->getChildBlock('invoice_comments');
        if ($comments) {
            $comments->setEntity($invoice)
                ->setTitle(__('About Your Invoice'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
