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
     * @var array
     */
    protected $adjustmentRenders;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Template\Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param string $template
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        $template,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
        $this->setTemplate($template);
    }

    /**
     * Retrieve amount html for given price, item and arguments
     *
     * @param PriceInterface $price
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render(PriceInterface $price, SaleableInterface $saleableItem, array $arguments = [])
    {
        $this->price = $price;
        $this->saleableItem = $saleableItem;

        // @todo probably use block vars instead
        $origArguments = $this->getData();
        $this->setData(array_replace($origArguments, $arguments));

        $adjustmentRenders = $this->getApplicableAdjustmentRenders();

        // render Price Adjustment Renders if available
        $html = $adjustmentRenders ? $this->applyAdjustments($adjustmentRenders) : $this->toHtml();

        // restore original block arguments
        $this->setData($origArguments);

        return $html;
    }

    /**
     * (to use in templates only)
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->price->getDisplayValue();
    }

    /**
     * (to use in templates only)
     *
     * @return PriceInterface
     */
    public function getPrice()
    {
        // @todo move to abstract pricing block
        return $this->price;
    }

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getSaleableItem()
    {
        // @todo move to abstract pricing block
        return $this->saleableItem;
    }

    /**
     * @return AdjustmentRenderInterface[]
     */
    protected function getAdjustmentRenders()
    {
        if ($this->adjustmentRenders === null) {
            $this->adjustmentRenders = [];
            /** @var \Magento\View\Element\RendererList $adjustmentsList */
            $adjustmentsList = $this->getLayout()->getBlock('price.render.adjustments');
            if ($adjustmentsList) {
                $adjustments = $adjustmentsList->getChildNames();
                foreach ($adjustments as $adjustmentBlockName) {
                    $this->adjustmentRenders[] = $adjustmentsList->getLayout()->getBlock($adjustmentBlockName);
                }
            }
        }
        return $this->adjustmentRenders;
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

    /**
     * Collect correspondent Price Adjustment Renders
     *
     * @return AdjustmentRenderInterface[]
     */
    protected function getApplicableAdjustmentRenders()
    {
        $adjustmentRenders = [];

        if (!$this->getSkipAdjustments()) {
            foreach ($this->getAdjustmentRenders() as $adjustmentRender) {
                $adjustmentCode = $adjustmentRender->getAdjustmentCode();
                    $cssClass = 'adj-' . $adjustmentCode;
                    $adjustmentRenders[$cssClass] = $adjustmentRender;
            }
        }

        return $adjustmentRenders;
    }

    /**
     * @param AdjustmentRenderInterface[] $adjustmentRenders
     * @return array
     */
    protected function setAdjustmentCssClasses($adjustmentRenders)
    {
        $cssClasses = $this->hasData('css_classes') ? $this->getData('css_classes') : [];
        $cssClasses = array_merge($cssClasses, array_keys($adjustmentRenders));

        $this->setData('adjustment_css_classes', join(' ', $cssClasses));

        return $this;
    }

    /**
     * @param AdjustmentRenderInterface[] $adjustmentRenders
     * @return string
     */
    protected function applyAdjustments($adjustmentRenders)
    {
        // @todo resolve the key issue with decoration

        $html = $this->toHtml();

        $this->setAdjustmentCssClasses($adjustmentRenders);
        $data = $this->getData();
        foreach ($adjustmentRenders as $adjustmentRender) {
            $html = $adjustmentRender->render($html, $this, $data);
        }

        return $html;
    }
}
