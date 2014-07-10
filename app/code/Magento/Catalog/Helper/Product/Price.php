<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Helper\Product;

use Magento\Catalog\Model\Product;

/**
 * Collection of tax module calls
 */
class Price extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxData;


    /**
     * @param \Magento\Tax\Helper\Data $taxData
     */
    public function __construct(\Magento\Tax\Helper\Data $taxData)
    {
        $this->taxData = $taxData;
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
}
