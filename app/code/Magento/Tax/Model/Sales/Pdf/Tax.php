<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Sales_Pdf_Tax extends Magento_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Check if tax amount should be included to grandtotal block
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();
        $config= Mage::getSingleton('Magento_Tax_Model_Config');
        if ($config->displaySalesTaxWithGrandTotal($store)) {
            return array();
        }

        $totals = array();
        if ($config->displaySalesFullSummary($store)) {
           $totals = $this->getFullTaxInfo();
        }

        $totals = array_merge($totals, parent::getTotalsForDisplay());

        return $totals;
    }
}
