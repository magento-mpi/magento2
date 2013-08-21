<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Order_Creditmemo_Total_Subtotal extends Magento_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect Creditmemo subtotal
     *
     * @param   Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Magento_Sales_Model_Order_Creditmemo_Total_Subtotal
     */
    public function collect(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax= 0;
        $baseSubtotalInclTax = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $item->calcRowTotal();

            $subtotal       += $item->getRowTotal();
            $baseSubtotal   += $item->getBaseRowTotal();
            $subtotalInclTax+= $item->getRowTotalInclTax();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax();
        }

        $creditmemo->setSubtotal($subtotal);
        $creditmemo->setBaseSubtotal($baseSubtotal);
        $creditmemo->setSubtotalInclTax($subtotalInclTax);
        $creditmemo->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $subtotal);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseSubtotal);
        return $this;
    }
}
