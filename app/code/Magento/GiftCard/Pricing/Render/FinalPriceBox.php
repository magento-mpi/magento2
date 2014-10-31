<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Pricing\Render;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\PriceBox;

/**
 * Gift card final price box
 */
class FinalPriceBox extends PriceBox
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var array
     */
    protected $minMaxCache = [];

    /**
     * @var array
     */
    protected $amountsCache = [];

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param Template\Context $context
     * @param Product $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @param \Magento\Checkout\Model\Session
     */
    public function __construct(
        Template\Context $context,
        Product $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        PriceCurrencyInterface $priceCurrency,
        array $data = [],
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data
        );
        $this->calculateMinMaxPrices();
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return bool
     */
    public function isRegularPrice()
    {
        return !$this->isOpenAmountAvailable() && (count($this->getAmounts()) === 1);
    }

    /**
     * @return bool
     */
    public function isOpenAmountAvailable()
    {
        return $this->saleableItem->getAllowOpenAmount() ? true : false;
    }

    /**
     * @return bool|float
     */
    public function getRegularPrice()
    {
        $amount = $this->getAmounts();
        return count($amount) === 1 ? array_shift($amount) : false;
    }

    /**
     * @return array
     */
    public function getAmounts()
    {
        if (!empty($this->amountsCache)) {
            return $this->amountsCache;
        }

        foreach ($this->saleableItem->getGiftcardAmounts() as $amount) {
            $this->amountsCache[] = $amount['website_value'];
        }
        sort($this->amountsCache);
        return $this->amountsCache;
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @return string
     */
    public function convertAndFormatCurrency($amount, $includeContainer = true)
    {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer);
    }

    /**
     * @param float $amount
     * @return float
     */
    public function convertCurrency($amount)
    {
        return $this->priceCurrency->convert($amount);
    }

    /**
     * @return bool
     */
    public function isAmountAvailable()
    {
        return $this->saleableItem->getGiftcardAmounts() ? true : false;
    }

    /**
     * @return float|null
     */
    public function getOpenAmountMin()
    {
        return $this->saleableItem->getOpenAmountMin();
    }

    /**
     * @return float|null
     */
    public function getOpenAmountMax()
    {
        return $this->saleableItem->getOpenAmountMax();
    }

    /**
     * @return string
     */
    public function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @return float|null
     */
    public function getMinValue()
    {
        return  $this->minMaxCache['min'];
    }

    /**
     * @return bool
     */
    public function isMinEqualToMax()
    {
        return ($this->minMaxCache['min'] && $this->minMaxCache['max'])
            ? $this->minMaxCache['min'] === $this->minMaxCache['max']
            : false;
    }

    /**
     * @return $this
     */
    protected function calculateMinMaxPrices()
    {
        $min = null;
        $max = null;
        if ($this->isOpenAmountAvailable()) {
            $min = $this->getOpenAmountMin() ? $this->getOpenAmountMin() : 0;
            $max = $this->getOpenAmountMax() ? $this->getOpenAmountMax() : 0;
        }

        foreach ($this->getAmounts() as $amount) {
            $min = is_null($min) ? $amount : min($min, $amount);
            $max = is_null($max) ? $amount : max($max, $amount);
        }

        $this->minMaxCache = ['min' => $min, 'max' => $max];

        return $this;
    }

    /**
     * @param $key
     * @return string
     */
    public function getDefaultValue($key)
    {
        $value = parent::getDefaultValue($key);
        if (!$value) {
            return $this->checkoutSession->getData($key, true);
        }
        return $value;
    }
}
