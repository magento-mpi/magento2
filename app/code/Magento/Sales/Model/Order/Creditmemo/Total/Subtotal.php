<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model\Order\Creditmemo\Total;

class Subtotal extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Collect Creditmemo subtotal
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return  \Magento\Sales\Model\Order\Creditmemo\Total\Subtotal
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
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
