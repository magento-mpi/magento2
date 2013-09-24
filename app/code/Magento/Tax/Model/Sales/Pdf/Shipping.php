<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Sales_Pdf_Shipping extends Magento_Sales_Model_Order_Pdf_Total_Default
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
     * Get array of arrays with totals information for display in PDF
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
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountInclTax = $this->getSource()->getShippingInclTax();
        if (!$amountInclTax) {
            $amountInclTax = $this->getAmount()+$this->getSource()->getShippingTaxAmount();
        }
        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        if ($this->_taxConfig->displaySalesShippingBoth($store)) {
            $totals = array(
                array(
                    'amount'    => $this->getAmountPrefix().$amount,
                    'label'     => __('Shipping (Excl. Tax)') . ':',
                    'font_size' => $fontSize
                ),
                array(
                    'amount'    => $this->getAmountPrefix().$amountInclTax,
                    'label'     => __('Shipping (Incl. Tax)') . ':',
                    'font_size' => $fontSize
                ),
            );
        } elseif ($this->_taxConfig->displaySalesShippingInclTax($store)) {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amountInclTax,
                'label'     => __($this->getTitle()) . ':',
                'font_size' => $fontSize
            ));
        } else {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amount,
                'label'     => __($this->getTitle()) . ':',
                'font_size' => $fontSize
            ));
        }

        return $totals;
    }
}
