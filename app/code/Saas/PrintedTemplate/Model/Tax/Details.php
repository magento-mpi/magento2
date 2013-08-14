<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Tax details calculation model
 * It allows calculate information about each tax aplied on each quote item, and tax info for shipping method
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Tax_Details
{
    /**
     * Tax calculation model
     *
     * @var Magento_Tax_Model_Calculation
     */
    protected $_calculator;

    /**
     * Tax configuration object
     *
     * @var Magento_Tax_Model_Config
     */
    protected $_config;

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->_calculator = isset($data['calculator']) ? $data['calculator']
            : Mage::getSingleton('Magento_Tax_Model_Calculation');
        $this->_config = isset($data['config']) ? $data['config'] : Mage::getSingleton('Magento_Tax_Model_Config');
    }

    /**
     * Calculate tax details information for quote items
     * Return array with tax rates grouped by item IDs
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function calculateItemsTaxInfo(Magento_Sales_Model_Quote $quote)
    {
        $taxRateRequest = $this->_prepareRateRequest($quote);

        $rateInfo = array();
        foreach ($quote->getAllAddresses() as $address) {
            foreach ($address->getAllNonNominalItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->getTaxClassId()) {
                            $rateInfo[$child->getId()] = array();
                            $taxRateRequest->setProductClassId($child->getProduct()->getTaxClassId());
                            foreach ($this->_getRatesInfo($taxRateRequest) as $rate) {
                                $rateInfo[$child->getId()][] = $rate;
                            }
                        }
                    }
                } else if ($item->getProduct()->getTaxClassId()) {
                    $rateInfo[$item->getId()] = array();
                    $taxRateRequest->setProductClassId($item->getProduct()->getTaxClassId());
                    foreach ($this->_getRatesInfo($taxRateRequest) as $rate) {
                        $rateInfo[$item->getId()][] = $rate;
                    }
                }
            }
        }
        return $rateInfo;
    }

    /**
     * Calculate tax details information for shipping method
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return array
     */
    public function calculateShippingTaxInfo(Magento_Sales_Model_Quote $quote)
    {
        $taxRateRequest = $this->_prepareRateRequest($quote);
        $taxClass = $this->_config->getShippingTaxClass($quote->getStore());
        if (!$taxClass) {
            return array();
        }

        $taxRateRequest->setProductClassId($taxClass);

        return $this->_getRatesInfo($taxRateRequest);
    }

    /**
     * Prepare tax rate request object from quote model
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return Magento_Object
     */
    protected function _prepareRateRequest(Magento_Sales_Model_Quote $quote)
    {
        $taxRateRequest = $this->_calculator->setCustomer($quote->getCustomer())
            ->getRateRequest(
                $quote->getShippingAddress(),
                $quote->getBillingAddress(),
                $quote->getCustomerTaxClassId(),
                $quote->getStore()
            );

        return $taxRateRequest;
    }

    /**
     * Retrieve tax rates information based on request object
     *
     * @param Magento_Object $taxRateRequest
     * @return array
     */
    protected function _getRatesInfo($taxRateRequest)
    {
        $isAfterDiscount = $this->_config->applyTaxAfterDiscount($taxRateRequest->getStore());
        $isIncludingTax = $this->_config->discountTax($taxRateRequest->getStore());

        $rateInfo = array();
        foreach ($this->_calculator->getAppliedRates($taxRateRequest) as $process) {
            if (!isset($process['rates']) || !isset($process['rates'][0]) || !isset($process['percent'])) {
                continue;
            }
            if (count($process['rates']) > 1) {
                $totalRealPercent = 0;
                foreach ($process['rates'] as $rate) {
                    $totalRealPercent += $rate['percent'];
                }

                // there can be problems with rounding
                $realRateRatio = $process['percent'] / $totalRealPercent;
                foreach ($process['rates'] as $rate) {
                    $rate['real_percent'] = $rate['percent'] * $realRateRatio;
                    $rate['is_tax_after_discount'] = $isAfterDiscount;
                    $rate['is_discount_on_incl_tax'] = $isIncludingTax;
                    $rateInfo[] = $rate;
                }
            } else {
                $rate = $process['rates'][0];
                $rate['real_percent'] = $process['percent'];
                $rate['is_tax_after_discount'] = $isAfterDiscount;
                $rate['is_discount_on_incl_tax'] = $isIncludingTax;
                $rateInfo[] = $rate;
            }
        }

        return $rateInfo;
    }
}
