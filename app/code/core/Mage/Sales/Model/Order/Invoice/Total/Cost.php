<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sales_Model_Order_Invoice_Total_Cost extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total cost of invoiced items
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Invoice_Total_Cost
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
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
