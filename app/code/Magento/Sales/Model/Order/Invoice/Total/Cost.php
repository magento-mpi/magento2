<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Invoice\Total;

class Cost extends AbstractTotal
{
    /**
     * Collect total cost of invoiced items
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
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
