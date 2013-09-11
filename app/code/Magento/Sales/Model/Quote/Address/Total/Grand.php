<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model\Quote\Address\Total;

class Grand extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Collect grand total address amount
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\Sales\Model\Quote\Address\Total\Grand
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        $grandTotal     = $address->getGrandTotal();
        $baseGrandTotal = $address->getBaseGrandTotal();

        $totals     = array_sum($address->getAllTotalAmounts());
        $baseTotals = array_sum($address->getAllBaseTotalAmounts());

        $address->setGrandTotal($grandTotal+$totals);
        $address->setBaseGrandTotal($baseGrandTotal+$baseTotals);
        return $this;
    }

    /**
     * Add grand total information to address
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\Sales\Model\Quote\Address\Total\Grand
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        $address->addTotal(array(
            'code'  => $this->getCode(),
            'title' => __('Grand Total'),
            'value' => $address->getGrandTotal(),
            'area'  => 'footer',
        ));
        return $this;
    }
}
