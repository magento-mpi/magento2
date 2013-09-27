<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Order Total PDF model
 *
 * @method Magento_Sales_Model_Order getOrder()
 */
class Magento_Sales_Model_Order_Pdf_Total_Default extends Magento_Object
{
    /**
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxHelper;

    /**
     * @var Magento_Tax_Model_Calculation
     */
    protected $_taxCalculation;

    /**
     * @var Magento_Tax_Model_Sales_Order_Tax
     */
    protected $_taxOrder;

    /**
     * Initialize dependencies
     *
     * @param Magento_Tax_Helper_Data $taxHelper
     * @param Magento_Tax_Model_Calculation $taxCalculation
     * @param Magento_Tax_Model_Sales_Order_TaxFactory $taxFactory
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxHelper,
        Magento_Tax_Model_Calculation $taxCalculation,
        Magento_Tax_Model_Sales_Order_TaxFactory $taxFactory,
        array $data = array()
    ){
        $this->_taxHelper = $taxHelper;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrder = $taxFactory->create();
        parent::__construct($data);
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
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }

        $title = __($this->getTitle());
        if ($this->getTitleSourceField()) {
            $label = $title . ' (' . $this->getTitleDescription() . '):';
        } else {
            $label = $title . ':';
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = array(
            'amount'    => $amount,
            'label'     => $label,
            'font_size' => $fontSize
        );
        return array($total);
    }

    /**
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getFullTaxInfo()
    {
        $fontSize       = $this->getFontSize() ? $this->getFontSize() : 7;
        $taxClassAmount = $this->_taxHelper->getCalculatedTaxes($this->getOrder());
        $shippingTax    = $this->_taxHelper->getShippingTax($this->getOrder());
        $taxClassAmount = array_merge($shippingTax, $taxClassAmount);

        if (!empty($taxClassAmount)) {
            foreach ($taxClassAmount as &$tax) {
                $percent          = $tax['percent'] ? ' (' . $tax['percent']. '%)' : '';
                $tax['amount']    = $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($tax['tax_amount']);
                $tax['label']     = __($tax['title']) . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            $rates = $this->_taxOrder->getCollection()->loadByOrder($this->getOrder())->toArray();
            $fullInfo = $this->_taxCalculation->reproduceProcess($rates['items']);
            $tax_info = array();

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }

                    $_amount = $info['amount'];

                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' (' . $rate['percent']. '%)' : '';

                        $tax_info[] = array(
                            'amount'    => $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount),
                            'label'     => __($rate['title']) . $percent . ':',
                            'font_size' => $fontSize
                        );
                    }
                }
            }
            $taxClassAmount = $tax_info;
        }

        return $taxClassAmount;
    }

    /**
     * Check if we can display total information in PDF
     *
     * @return bool
     */
    public function canDisplay()
    {
        $amount = $this->getAmount();
        return $this->getDisplayZero() || ($amount != 0);
    }

    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod($this->getSourceField());
    }

    /**
     * Get title description from source
     *
     * @return mixed
     */
    public function getTitleDescription()
    {
        return $this->getSource()->getDataUsingMethod($this->getTitleSourceField());
    }
}
