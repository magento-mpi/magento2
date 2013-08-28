<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Order_Invoice_Total_Cost extends Magento_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total cost of invoiced items
     *
     * @param Magento_Sales_Model_Order_Invoice $invoice
     * @return Magento_Sales_Model_Order_Invoice_Total_Cost
     */
    public function collect(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $baseInvoiceTotalCost = 0;
        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getHasChildren()){
                $baseInvoiceTotalCost += $item->getBaseCost()*$item->getQty();
            }
        }
        $invoice->setBaseCost($baseInvoiceTotalCost);
        return $this;
    }
}
