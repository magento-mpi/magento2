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

use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\View\Element\Template;
use Magento\Pricing\PriceCurrencyInterface;

/**
 * Price amount renderer
 *
 * @method string getAdjustmentCssClasses()
 * @method string getDisplayLabel()
 * @method string getPriceId()
 * @method bool getIncludeContainer()
 * @method bool getSkipAdjustments()
 */
class Amount extends Template implements AmountRenderInterface
{
    /**
     * @var SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * @var AdjustmentRenderInterface[]
     */
    protected $adjustmentRenders;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var RendererPool
     */
    protected $rendererPool;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @param Template\Context $context
     * @param AmountInterface $amount
     * @param PriceCurrencyInterface $priceCurrency
     * @param RendererPool $rendererPool
     * @param SaleableInterface $saleableItem
     * @param \Magento\Pricing\Price\PriceInterface $price
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AmountInterface $amount,
        PriceCurrencyInterface $priceCurrency,
        RendererPool $rendererPool,
        SaleableInterface $saleableItem = null,
        PriceInterface $price = null,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->amount = $amount;
        $this->saleableItem = $saleableItem;
        $this->price = $price;
        $this->priceCurrency = $priceCurrency;
        $this->rendererPool = $rendererPool;
    }

    /**
     * @return float
     */
    public function getDisplayValue()
    {
        return $this->getAmount()->getValue();
    }

    /**
     * @return AmountInterface
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return SaleableInterface
     */
    public function getSaleableItem()
    {
        return $this->saleableItem;
    }

    /**
     * @return \Magento\Pricing\Price\PriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        // render Price Adjustment Renders if available
        $adjustmentRenders = $this->getApplicableAdjustmentRenders();
        if ($adjustmentRenders) {
            $html = $this->applyAdjustments($html, $adjustmentRenders);
        }

        return $html;
    }

    /**
     * Collect correspondent Price Adjustment Renders
     *
     * @return AdjustmentRenderInterface[]
     */
    protected function getApplicableAdjustmentRenders()
    {
        if (!$this->hasSkipAdjustments()) {
            return $this->getAdjustmentRenders();
        } else {
            return [];
        }
    }

    /**
     * @return AdjustmentRenderInterface[]
     */
    protected function getAdjustmentRenders()
    {
        return $this->rendererPool->getAdjustmentRenders($this->saleableItem, $this->price);
    }

    /**
     * @param string $html
     * @param AdjustmentRenderInterface[] $adjustmentRenders
     * @return string
     */
    protected function applyAdjustments($html, $adjustmentRenders)
    {
        $this->setAdjustmentCssClasses($adjustmentRenders);
        $data = $this->getData();
        foreach ($adjustmentRenders as $adjustmentRender) {
            $html = $adjustmentRender->render($html, $this, $data);
        }
        return $html;
    }

    /**
     * Convert and format price value
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int $precision
     * @return float
     */
    public function convertAndFormatCurrency(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer, $precision);
    }

    /**
     * @param AdjustmentRenderInterface[] $adjustmentRenders
     * @return array
     */
    protected function setAdjustmentCssClasses($adjustmentRenders)
    {
        $cssClasses = $this->hasData('css_classes') ? explode(' ', $this->getData('css_classes')) : [];
        $cssClasses = array_merge($cssClasses, array_keys($adjustmentRenders));
        $this->setData('adjustment_css_classes', join(' ', $cssClasses));
        return $this;
    }
}
