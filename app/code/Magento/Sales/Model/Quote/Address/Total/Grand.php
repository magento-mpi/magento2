<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sales_Model_Quote_Address_Total_Grand extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Collect grand total address amount
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Quote_Address_Total_Grand
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
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
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Quote_Address_Total_Grand
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
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
