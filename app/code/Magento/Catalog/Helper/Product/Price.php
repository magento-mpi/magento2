<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Collection of tax module calls
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Product;

class Price extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxData;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $taxCalculation;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Tax\Model\Calculation $taxCalculation
    ) {
        $this->taxData = $taxData;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * Get product price with all tax settings processing
     *
     * @param $_product
     * @param $_minimalPriceValue
     * @param null $includingTax
     * @return float
     */
    public function getPrice($_product, $_minimalPriceValue, $includingTax = null)
    {
        return $this->taxData->getPrice($_product, $_minimalPriceValue, $includingTax);
    }

    /**
     * Check if we have display in catalog prices including and excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->taxData->displayBothPrices();
    }

    /**
     * Check if we have display in catalog prices including tax
     *
     * @return bool
     */
    public function displayPriceIncludingTax()
    {
        return $this->taxData->displayPriceIncludingTax();
    }

    /**
     * Check if product prices on input include tax
     *
     * @return bool
     */
    public function priceIncludesTax()
    {
        return $this->taxData->priceIncludesTax();
    }

    /**
     * Set customer to prepare tax calculation
     *
     * @param $customer
     */
    public function setCustomer($customer = null)
    {
        if (!$this->taxCalculation->getCustomer() && $customer) {
            $this->taxCalculation->setCustomer($customer);
        }
    }

    /**
     * Get request object with information necessary for getting tax rate
     *
     * @param   null|bool|\Magento\Object $shippingAddress
     * @param   null|bool||\Magento\Object $billingAddress
     * @param   null|int $customerTaxClass
     * @param   null|int $store
     * @return  \Magento\Object
     */
    public function getRateRequest(
        $shippingAddress = null,
        $billingAddress = null,
        $customerTaxClass = null,
        $store = null
    ) {
        return $this->taxCalculation->getRateRequest($shippingAddress, $billingAddress, $customerTaxClass, $store);
    }

    /**
     * Get calculation tax rate by specific request
     *
     * @param   \Magento\Object $request
     * @return  float
     */
    public function getRate($request)
    {
        return $this->taxCalculation->getRate($request);
    }
}
