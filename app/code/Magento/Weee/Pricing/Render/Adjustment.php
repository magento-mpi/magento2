<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Pricing\Render;

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
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Price $helper
     * @param \Magento\Weee\Helper\Data $weeeHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Helper\Product\Price $helper,
        \Magento\Weee\Helper\Data $weeeHelper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceHelper = $helper;
        $this->weeeHelper = $weeeHelper;
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
     * @return string
     */
    public function getAdjustmentCode()
    {
        return 'weee';
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

    /**
     * @param int|int[]|null $compareTo
     * @param string|null $zone
     * @param \Magento\Core\Model\Store|null $store
     * @return bool|int
     */
    public function typeOfDisplay($compareTo = null, $zone = null, $store = null)
    {
        return $this->weeeHelper->typeOfDisplay($compareTo, $zone, $store);
    }

    /**
     * @param SaleableInterface $product
     * @return float
     */
    public function getAmount(SaleableInterface $product)
    {
        return $this->weeeHelper->getAmount($product);
    }

    /**
     * @param SaleableInterface $product
     * @return \Magento\Object[]
     */
    public function getProductWeeeAttributesForDisplay(SaleableInterface $product)
    {
        return $this->weeeHelper->getProductWeeeAttributesForDisplay($product);
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
