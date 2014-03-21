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

use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\Render\AdjustmentRenderInterface;
use Magento\View\Element\Template;
use Magento\Pricing\PriceCurrencyInterface;

class Adjustment extends Template implements AdjustmentRenderInterface
{
    /**
     * @var string
     */
    protected $originalHtmlOutput;

    /**
     * @var SaleableInterface
     */
    protected $product;

    /**
     * @var PriceInfoInterface
     */
    protected $priceInfo;

    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * @var \Magento\Tax\Pricing\Adjustment
     */
    protected $taxAdjustment;

    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Price $helper
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Helper\Product\Price $helper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceHelper = $helper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @param string $result html given to process by renderer
     * @param PriceInterface $price
     * @param SaleableInterface $product
     * @param array $arguments
     * @return string
     */
    public function render($result, PriceInterface $price, SaleableInterface $product, array $arguments = [])
    {
        $origArguments = $this->_data;
        // @todo probably use block vars instead
        $this->_data = array_replace($origArguments, $arguments);

        $this->originalHtmlOutput = $result;
        $this->price = $price;
        $this->product = $product;
        $this->priceInfo = $product->getPriceInfo();
        $this->taxAdjustment = $this->priceInfo->getAdjustment($this->getAdjustmentCode());

        $result = $this->toHtml();

        // restore original block arguments
        $this->_data = $origArguments;

        return $result;
    }

    /**
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPriceType($priceCode)
    {
        return $this->priceInfo->getPrice($priceCode);
    }

    /**
     * @return \Magento\Tax\Pricing\Adjustment
     */
    public function getTaxAdjustment()
    {
        return $this->taxAdjustment;
    }

    /**
     * @return string
     */
    public function getAdjustmentCode()
    {
        return 'tax';
    }

    /**
     * (to use in templates only)
     *
     * @return string
     */
    public function getOriginalPriceHtml()
    {
        return $this->originalHtmlOutput;
    }

    /**
     * (to use in templates only)
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function displayPriceIncludingTax()
    {
        return $this->priceHelper->displayPriceIncludingTax();
    }

    public function displayBothPrices()
    {
        return $this->priceHelper->displayBothPrices();
    }

    /**
     * Convert and format price value
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int $precision
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return float
     */
    public function convertAndFormatCurrency(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION,
        $store = null,
        $currency = null
    ) {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer, $precision, $store, $currency);
    }
}
