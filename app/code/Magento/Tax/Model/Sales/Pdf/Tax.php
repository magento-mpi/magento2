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
     * @var Magento_Tax_Model_Config
     */
    protected $_taxConfig;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Tax_Helper_Data $taxHelper
     * @param Magento_Tax_Model_Calculation $taxCalculation
     * @param Magento_Tax_Model_Config $taxConfig
     * @param Magento_ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Tax_Helper_Data $taxHelper,
        Magento_Tax_Model_Calculation $taxCalculation,
        Magento_Tax_Model_Config $taxConfig,
        Magento_ObjectManager $objectManager,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($context, $taxHelper, $taxCalculation, $objectManager, $data);
    }

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
        if ($this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return array();
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = array();

        if ($this->_taxConfig->displaySalesFullSummary($store)) {
           $totals = $this->getFullTaxInfo();
        }

        $totals = array_merge($totals, parent::getTotalsForDisplay());

        return $totals;
    }


}
