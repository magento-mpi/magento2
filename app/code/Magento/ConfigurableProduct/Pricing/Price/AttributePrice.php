<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute;
use Magento\Catalog\Pricing\Price\AbstractPrice;
use Magento\ConfigurableProduct\Block\Product\View;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\PriceModifierInterface;
use Magento\Pricing\Amount\AmountInterface;

/**
 * Class PriceOptions
 *
 * @package Magento\ConfigurableProduct\Block\Product\View\Type
 */
class AttributePrice extends AbstractPrice implements AttributePriceInterface
{
    /**
     * Default price type
     */
    const PRICE_CODE = 'attribute_price';

    /**
     * \Magento\Tax\Helper\Data $taxData     *
     */
    protected $taxData;

    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param PriceModifierInterface $modifier
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Product\Price $priceHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceModifierInterface $modifier,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Product\Price $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->priceModifier = $modifier;
        $this->taxData = $taxData;
        $this->priceHelper = $priceHelper;
        $this->storeManager = $storeManager;
        parent::__construct($saleableItem, $quantity, $calculator);
    }

    /**
     * Prepare JsonAttributes with Options Prices
     *
     * @param array $options
     * @return array
     */
    public function prepareJsonAttributes(array $options = [])
    {
        $defaultValues = [];
        $attributes = [];
        $preConfiguredValues = $this->getPreConfiguredValues($this->product);
        $configurableAttributes = $this->product->getTypeInstance()->getConfigurableAttributes($this->product);
        foreach ($configurableAttributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = [
                'id' => $attributeId,
                'code' => $productAttribute->getAttributeCode(),
                'label' => $attribute->getLabel(),
                'options' => $this->getPriceOptions($attributeId, $attribute, $options)
            ];
            $defaultValues[$attributeId] = $this->getAttributeConfigValue($preConfiguredValues, $attributeId);
            if ($this->_validateAttributeInfo($info)) {
                $attributes[$attributeId] = $info;
            }
        }
        return [
            'priceOptions' => $attributes,
            'defaultValues' => $defaultValues
        ];

    }

    /**
     * Returns prices for configurable product options
     *
     * @param int $attributeId
     * @param Attribute $attribute
     * @param array $options
     * @return array
     */
    public function getPriceOptions($attributeId, $attribute, array $options = [])
    {
        $prices = $attribute->getPrices();
        $optionPrices = [];
        if (is_array($prices)) {
            foreach ($prices as $value) {
                $optionValueAmount = $this->getOptionValueAmount($value);
                $optionValueOldAmount = $this->getOptionValueOldAmount($value);

                $price = $this->getOptionPrice($optionValueAmount);

                // @todo resolve issue with weee specifics
                $optionPrices[] = [
                    'id' => $value['value_index'],
                    'label' => $value['label'],
                    'price' => $price,
                    'oldPrice' =>
                        $this->_registerJsPrice(
                            $this->_convertPrice($optionValueOldAmount->getValue()),
                            true
                        ),
                    'inclTaxPrice' => $this->_registerJsPrice($optionValueAmount->getValue()),
                    'exclTaxPrice' => $this->_registerJsPrice($optionValueAmount->getBaseAmount()),
                    'products' => $this->getProductsIndex($attributeId, $options, $value)
                ];
            }
        }

        return $optionPrices;
    }

    /**
     * Get Option Value
     *
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueAmount(array $value = array())
    {
        $pricingValue = $this->getPricingValue($value);
        $this->product->setParentId(true);
        $amount = $this->priceModifier->modifyPrice($pricingValue, $this->product);

        return $this->calculator->getAmount($amount, $this->product);

    }

    /**
     * Get Option Value Amount with no Catalog Rules
     *
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueOldAmount(array $value = array())
    {
        $amount = $this->getPricingValue($value);

        return $this->calculator->getAmount($amount, $this->product);
    }

    /**
     * Prepare percent price value
     *
     * @param array $value
     * @return float
     */
    protected function preparePrice(array $value = array())
    {
        return $this->product
            ->getPriceInfo()
            ->getPrice('final_price')
            ->getValue() * $value['pricing_value'] / 100;
    }

    /**
     * Get value from array
     *
     * @param array $value
     * @return float
     */
    protected function getPricingValue(array $value = array())
    {
        if ($value['is_percent'] && !empty($value['pricing_value'])) {
            return $this->preparePrice($value);
        } else {
            return $value['pricing_value'];
        }
    }

    /**
     * Get Products Index
     *
     * @param int $attributeId
     * @param array $options
     * @param array $value
     * @return array
     */
    protected function getProductsIndex($attributeId, array $options = [], array $value = [])
    {
        if (isset($options[$attributeId][$value['value_index']])) {
            return $options[$attributeId][$value['value_index']];
        } else {
            return [];
        }
    }

    /**
     * @param \Magento\Object $preConfiguredValues
     * @param int $attributeId
     * @return mixed|null
     */
    protected function getAttributeConfigValue($preConfiguredValues, $attributeId)
    {
        if ($this->hasPreConfiguredValues()) {
            return $preConfiguredValues->getData('super_attribute/' . $attributeId);
        }
    }

    /**
     * Get PreConfigured Values
     *
     * @return array
     */
    protected function getPreConfiguredValues()
    {
        if ($this->hasPreconfiguredValues()) {
            return $this->product->getPreconfiguredValues();
        }
    }

    /**
     * Get Flag if Configurable Product has PreConfiguredValues
     *
     * @return bool
     */
    protected function hasPreConfiguredValues()
    {
        return $this->product->hasPreconfiguredValues();
    }

    /**
     * Validation of super product option
     *
     * @param array $info
     * @return bool
     */
    protected function _validateAttributeInfo(&$info)
    {
        if (count($info['options']) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }


    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param bool $round
     * @return float
     */
    protected function _convertPrice($price, $round = false)
    {
        if (empty($price)) {
            return 0;
        }

        $price = $this->storeManager->getStore()->convertPrice($price);
        if ($round) {
            $price = $this->storeManager->getStore()->roundPrice($price);
        }

        return $price;
    }

    /**
     * Get Custom Option Price
     * depending on display including or excluding Tax
     *
     * @param \Magento\Pricing\Amount\AmountInterface $optionValueAmount
     * @return string
     */
    protected function getOptionPrice($optionValueAmount)
    {
        if ($this->taxData->displayPriceIncludingTax()) {
            return $this->_registerJsPrice($optionValueAmount->getValue());
        } else {
            return $this->_registerJsPrice($optionValueAmount->getBaseAmount());
        }
    }

    /**
     * Returns tax config for Configurable options
     *
     * @return array
     */
    public function getTaxConfig()
    {
        // Use Amount->getAdjustments here
        $defaultTax = $this->getDefaultTax($this->product);
        $currentTax = $this->getCurrentTax($this->product);

        $taxConfig = [
            'includeTax' => $this->taxData->priceIncludesTax(),
            'showIncludeTax' => $this->taxData->displayPriceIncludingTax(),
            'showBothPrices' => $this->taxData->displayBothPrices(),
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'inclTaxTitle' => __('Incl. Tax')
        ];
        return $taxConfig;
    }

    /**
     * Get Default Tax value
     *
     * @return array
     */
    protected function getDefaultTax()
    {
        $_request = $this->priceHelper->getRateRequest(false, false, false);
        $_request->setProductClassId($this->product->getTaxClassId());
        $defaultTax = $this->priceHelper->getRate($_request);

        return $defaultTax;
    }

    /**
     * Get Current Tax Value
     *
     * @return float
     */
    protected function getCurrentTax()
    {
        $_request = $this->priceHelper->getRateRequest();
        $_request->setProductClassId($this->product->getTaxClassId());
        $currentTax = $this->priceHelper->getRate($_request);
        return $currentTax;
    }

    /**
     * Get price value
     *
     * @return float|bool
     */
    public function getValue()
    {
        // TODO: Implement getValue() method.
    }
}
