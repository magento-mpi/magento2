<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Sales\Pdf;

class Tax extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Model\Resource\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Model\Resource\Sales\Order\Tax\CollectionFactory $ordersFactory,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
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

        $totals = array();

        if ($this->_taxConfig->displaySalesFullSummary($store)) {
           $totals = $this->getFullTaxInfo();
        }

        $totals = array_merge($totals, parent::getTotalsForDisplay());

        return $totals;
    }
}
