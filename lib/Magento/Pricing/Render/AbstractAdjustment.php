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

/**
 * Adjustment render abstract
 *
 * @method string getZone()
 */
abstract class AbstractAdjustment extends Template implements AdjustmentRenderInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var AmountRenderInterface
     */
    protected $amountRender;

    /**
     * @var Template
     */
    protected $originalHtmlOutput;

    /**
     * @param Template\Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @param string $html html given to process by renderer
     * @param AmountRenderInterface $amountRender
     * @param array $arguments
     * @return string
     */
    public function render($html, AmountRenderInterface $amountRender, array $arguments = [])
    {
        $this->originalHtmlOutput = $html;
        $this->amountRender = $amountRender;

        //@todo probably use block vars instead

        $origArguments = $this->getData();
        $this->setData(array_replace($origArguments, $arguments));

        $html = $this->toHtml();

        // restore original block arguments
        $this->setData($origArguments);

        return $html;
    }

    /**
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPriceType($priceCode)
    {
        return $this->getSaleableItem()->getPriceInfo()->getPrice($priceCode);
    }

    /**
     * @return \Magento\Pricing\Price\PriceInterface
     */
    public function getPrice()
    {
        return $this->amountRender->getPrice();
    }

    /**
     * @return SaleableInterface
     */
    public function getSaleableItem()
    {
        return $this->amountRender->getSaleableItem();
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
     * @return string
     */
    public function convertAndFormatCurrency(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer, $precision);
    }

    /**
     * @return \Magento\Pricing\Adjustment\AdjustmentInterface
     */
    public function getAdjustment()
    {
        return $this->getSaleableItem()->getPriceInfo()->getAdjustment($this->getAdjustmentCode());
    }
}
