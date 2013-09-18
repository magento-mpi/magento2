<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Weee\Model\Total\Creditmemo;

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
    public function __construct(
        \Magento\Weee\Helper\Data $weeeData,
        array $data = array()
    ) {
        $this->_weeeData = $weeeData;
        parent::__construct($data);
    }

    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $store = $creditmemo->getStore();

        $totalTax              = 0;
        $baseTotalTax          = 0;

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy()) {
                continue;
            }
            $orderItemQty = $item->getOrderItem()->getQtyOrdered();

            $totalTax += $item->getWeeeTaxAppliedAmount()*$item->getQty();
            $baseTotalTax += $item->getBaseWeeeTaxAppliedAmount()*$item->getQty();

            $newApplied = array();
            $applied = $this->_weeeData->getApplied($item);
            foreach ($applied as $one) {
                $one['base_row_amount'] = $one['base_amount']*$item->getQty();
                $one['row_amount'] = $one['amount']*$item->getQty();
                $one['base_row_amount_incl_tax'] = $one['base_amount_incl_tax']*$item->getQty();
                $one['row_amount_incl_tax'] = $one['amount_incl_tax']*$item->getQty();

                $newApplied[] = $one;
            }
            $this->_weeeData->setApplied($item, $newApplied);

            $item->setWeeeTaxRowDisposition($item->getWeeeTaxDisposition()*$item->getQty());
            $item->setBaseWeeeTaxRowDisposition($item->getBaseWeeeTaxDisposition()*$item->getQty());
        }

        if ($this->_weeeData->includeInSubtotal($store)) {
            $creditmemo->setSubtotal($creditmemo->getSubtotal() + $totalTax);
            $creditmemo->setBaseSubtotal($creditmemo->getBaseSubtotal() + $baseTotalTax);
        } else {
            $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $totalTax);
            $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTotalTax);
        }

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalTax);

        return $this;
    }
}
