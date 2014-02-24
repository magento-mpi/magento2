<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;

/**
 * Collection of tax module calls
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
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
     * @param Product $_product
     * @param float $_minimalPriceValue inputed product price
     * @param bool $includingTax return price include tax flag
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
     * Get customer object
     *
     * @return bool|Customer
     */
    public function getCustomer()
    {
        return $this->taxCalculation->getCustomer();
    }

    /**
     * Specify customer object which can be used for rate calculation
     *
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        $this->taxCalculation->setCustomer($customer);
        return $this;
    }

    /**
     * Get request object with information necessary for getting tax rate
     *
     * @param null|bool|\Magento\Object $shippingAddress
     * @param null|bool||\Magento\Object $billingAddress
     * @param null|int $customerTaxClass
     * @param null|int $store
     * @return \Magento\Object
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
     * @param \Magento\Object $request
     * @return float
     */
    public function getRate($request)
    {
        return $this->taxCalculation->getRate($request);
    }
}
