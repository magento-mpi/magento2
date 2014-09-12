<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;

class Weee extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Weee data
     *
     * @var \Magento\Weee\Helper\Data
     */
    protected $_weeeData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param array $data
     */
    public function __construct(\Magento\Weee\Helper\Data $weeeData, array $data = array())
    {
        $this->_weeeData = $weeeData;
        parent::__construct($data);
    }

    /**
     * Collect Weee amounts for the credit memo
     *
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $store = $creditmemo->getStore();

        $totalWeeeAmount = 0;
        $baseTotalWeeeAmount = 0;

        $totalWeeeAmountInclTax = 0;
        $baseTotalWeeeAmountInclTax = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy() || $item->getQty() <= 0) {
                continue;
            }

            $ratio = $item->getQty() / $orderItem->getQtyOrdered();

            $weeeAmountExclTax = $creditmemo->roundPrice($orderItem->getWeeeTaxAppliedRowAmount() * $ratio);
            $baseWeeeAmountExclTax = $creditmemo->roundPrice(
                $orderItem->getBaseWeeeTaxAppliedRowAmnt() * $ratio,
                'base'
            );

            $totalWeeeAmount += $weeeAmountExclTax;
            $baseTotalWeeeAmount += $baseWeeeAmountExclTax;

            $item->setWeeeTaxAppliedRowAmount($weeeAmountExclTax);
            $item->setBaseWeeeTaxAppliedRowAmount($baseWeeeAmountExclTax);

            $weeeAmountInclTax = $creditmemo->roundPrice(
                $this->_weeeData->getRowWeeeTaxInclTax($orderItem) * $ratio            );
            $baseWeeeAmountInclTax = $creditmemo->roundPrice(
                $this->_weeeData->getBaseRowWeeeTaxInclTax($orderItem) * $ratio,
                'base'
            );

            $totalWeeeAmountInclTax += $weeeAmountInclTax;
            $baseTotalWeeeAmountInclTax += $baseWeeeAmountInclTax;

            $newApplied = array();
            $applied = $this->_weeeData->getApplied($item);
            foreach ($applied as $one) {
                $one['base_row_amount'] = $baseWeeeAmountExclTax;
                $one['row_amount'] = $weeeAmountExclTax;
                $one['base_row_amount_incl_tax'] = $baseWeeeAmountInclTax;
                $one['row_amount_incl_tax'] = $weeeAmountInclTax;

                $newApplied[] = $one;
            }
            $this->_weeeData->setApplied($item, $newApplied);

            $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition() * $item->getQty());
            $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition() * $item->getQty());
        }

        if ($this->_weeeData->includeInSubtotal($store)) {
            $creditmemo->setSubtotal($creditmemo->getSubtotal() + $totalWeeeAmount);
            $creditmemo->setBaseSubtotal($creditmemo->getBaseSubtotal() + $baseTotalWeeeAmount);
        }

        $taxAmount = $totalWeeeAmountInclTax - $totalWeeeAmount;
        $baseTaxAmount = $baseTotalWeeeAmountInclTax - $baseTotalWeeeAmount;

        $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $taxAmount);
        $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTaxAmount);

        $creditmemo->setSubtotalInclTax(
            $creditmemo->getSubtotalInclTax() + $totalWeeeAmountInclTax
        );
        $creditmemo->setBaseSubtotalInclTax(
            $creditmemo->getBaseSubtotalInclTax() + $baseTotalWeeeAmountInclTax
        );

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalWeeeAmountInclTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalWeeeAmountInclTax);

        return $this;
    }
}
