<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Sales_Pdf_Grandtotal extends Magento_Sales_Model_Order_Pdf_Total_Default
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
     * @param Magento_Tax_Model_Resource_Sales_Order_Tax_CollectionFactory $ordersFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Tax_Helper_Data $taxHelper,
        Magento_Tax_Model_Calculation $taxCalculation,
        Magento_Tax_Model_Config $taxConfig,
        Magento_Tax_Model_Resource_Sales_Order_Tax_CollectionFactory $ordersFactory,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($context, $taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Check if tax amount should be included to grandtotals block
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
        if (!$this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return parent::getTotalsForDisplay();
        }
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountExclTax = $this->getAmount() - $this->getSource()->getTaxAmount();
        $amountExclTax = ($amountExclTax > 0) ? $amountExclTax : 0;
        $amountExclTax = $this->getOrder()->formatPriceTxt($amountExclTax);
        $tax = $this->getOrder()->formatPriceTxt($this->getSource()->getTaxAmount());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        $totals = array(array(
            'amount'    => $this->getAmountPrefix().$amountExclTax,
            'label'     => __('Grand Total (Excl. Tax)') . ':',
            'font_size' => $fontSize
        ));

        if ($this->_taxConfig->displaySalesFullSummary($store)) {
           $totals = array_merge($totals, $this->getFullTaxInfo());
        }

        $totals[] = array(
            'amount'    => $this->getAmountPrefix().$tax,
            'label'     => __('Tax') . ':',
            'font_size' => $fontSize
        );
        $totals[] = array(
            'amount'    => $this->getAmountPrefix().$amount,
            'label'     => __('Grand Total (Incl. Tax)') . ':',
            'font_size' => $fontSize
        );
        return $totals;
    }
}
