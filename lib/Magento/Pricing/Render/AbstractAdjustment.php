<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Render;

use Magento\View\Element\Template;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\PriceCurrencyInterface;

abstract class AbstractAdjustment extends Template implements AdjustmentRenderInterface
{
    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Pricing\Render\AmountRenderInterface
     */
    protected $amountRender;

    /**
     * @var string
     */
    protected $originalHtmlOutput;

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
     * @param string $html html given to process by renderer
     * @param \Magento\Pricing\Render\AmountRenderInterface $amountRender
     * @param array $arguments
     * @return string
     */
    public function render($html, \Magento\Pricing\Render\AmountRenderInterface $amountRender, array $arguments = [])
    {
        //@todo probably use block vars instead

        $origArguments = $this->getData();
        $this->setData(array_replace($origArguments, $arguments));

        $this->originalHtmlOutput = $html;
        $this->amountRender = $amountRender;

        $html = $this->toHtml();

        // restore original block arguments
        $this->setData($origArguments);

        return $html;
    }

    /**
     * (to use in templates only)
     *
     * @return \Magento\Pricing\Price\PriceInterface
     */
    public function getPrice()
    {
        return $this->amountRender->getPrice();
    }

    /**
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPriceType($priceCode)
    {
        return $this->getProduct()->getPriceInfo()->getPrice($priceCode);
    }

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getProduct()
    {
        return $this->amountRender->getProduct();
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
