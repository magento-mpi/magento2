<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Price proxy for tax module
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
     * @param \Magento\Tax\Helper\Data $taxData
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData
    ) {
        $this->taxData = $taxData;
    }

    /**
     * Proxy method for templates
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
     * Proxy to tax module for template
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->taxData->displayBothPrices();
    }

    /**
     * Proxy to tax module for template
     *
     * @return bool
     */
    public function displayPriceIncludingTax()
    {
        return $this->taxData->displayPriceIncludingTax();
    }

    /**
     * proxy method for template
     *
     * @return bool
     */
    public function priceIncludesTax()
    {
        return $this->taxData->priceIncludesTax();
    }
}
