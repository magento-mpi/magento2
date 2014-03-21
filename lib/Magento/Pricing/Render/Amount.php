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
 */
class Amount extends Template implements AmountRenderInterface
{
    /**
     * @var float
     */
    protected $amount;

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
     * @param PriceInterface $price
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render(PriceInterface $price, SaleableInterface $saleableItem, array $arguments = [])
    {
        $origArguments = $this->_data;
        // @todo probably use block vars instead
        $this->_data = array_replace($origArguments, $arguments);

        $this->amount = $price->getValue();
        $this->price = $price;
        $this->saleableItem = $saleableItem;

        // collect correspondent Price Adjustment Renders
        /** @var AdjustmentRenderInterface[] $adjustmentRenders */
        $adjustmentRenders = [];
        $cssClasses = isset($this->_data['css_classes']) ? $this->_data['css_classes'] : [];

        if (!$this->getSkipAdjustments()) {
            foreach ($this->getAdjustmentRenders() as $adjustmentRender) {
                $adjustmentCode = $adjustmentRender->getAdjustmentCode();
                //if ($this->price->hasAdjustment($adjustmentCode)) {
                    $adjustmentRenders[] = $adjustmentRender;
                    // update aggregated CSS classes list
                    $cssClasses[] = 'adj-' . $adjustmentCode;
                //}
            }
        }

        $html = $this->toHtml();

        // render Price Adjustment Renders if available
        // @todo resolve the key issue with decoration
        foreach ($adjustmentRenders as $adjustmentRender) {
            $html = $adjustmentRender->render($html, $this, $this->getData());
        }

        // restore original block arguments
        $this->_data = $origArguments;

        // return result
        return $html;
    }

    /**
     * (to use in templates only)
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @param float $amount
     * @return float
     */
    public function convertToDisplayCurrency($amount)
    {
        // @todo move to abstract pricing block
        return $amount;
    }

    /**
     * @return string
     */
    public function getDisplayCurrencySymbol()
    {
        // @todo move to abstract pricing block
        return '';
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
}
