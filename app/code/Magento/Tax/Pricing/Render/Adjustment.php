<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Pricing\Render;

use Magento\View\Element\Template;
use Magento\Pricing\Render\AbstractAdjustment;
use Magento\Catalog\Helper\Product\Price as PriceHelper;
use Magento\Pricing\PriceCurrencyInterface;

/**
 * @method string getIdSuffix()
 * @method string getDisplayLabel()
 */
class Adjustment extends AbstractAdjustment
{
    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * @param Template\Context $context
     * @param PriceHelper $helper
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        PriceHelper $helper,
        array $data = []
    ) {
        $this->priceHelper = $helper;
        parent::__construct($context, $priceCurrency, $data);
    }

    /**
     * Obtain code of adjustment type
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        //@TODO We can build two model using DI, not code. What about passing it in constructor?
        return \Magento\Tax\Pricing\Adjustment::CODE;
    }

    /**
     * Define if both prices should be displayed
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->priceHelper->displayBothPrices();
    }

    /**
     * Obtain display amount excluding tax
     *
     * @return string
     */
    public function getDisplayAmountExclTax()
    {
        return $this->convertAndFormatCurrency($this->getPrice()->getDisplayValue(null, 'tax'), false);
    }

    /**
     * Obtain display amount
     *
     * @param bool $includeContainer
     * @return string
     */
    public function getDisplayAmount($includeContainer = true)
    {
         return $this->convertAndFormatCurrency($this->getPrice()->getDisplayValue(), $includeContainer);
    }

    /**
     * Build identifier with prefix
     *
     * @param string $prefix
     * @return string
     */
    public function buildIdWithPrefix($prefix)
    {
        $productId = $this->getSaleableItem()->getId();
        return $prefix . $productId . $this->getIdSuffix();
    }
}
