<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

use Magento\Catalog\Pricing\Price\AbstractPrice;
use Magento\ConfigurableProduct\Block\Product\View;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Catalog\Model\Product;

/**
 * Class PriceOptions
 *
 * @package Magento\ConfigurableProduct\Block\Product\View\Type
 */
class AttributePrice extends AbstractPrice
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
    protected $_storeManager;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Product\Price $priceHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Product\Price $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->taxData = $taxData;
        $this->priceHelper = $priceHelper;
        $this->_storeManager = $storeManager;
        parent::__construct($saleableItem, $quantity, $calculator);
    }

    /**
     * Returns prices for configurable product options
     *
     * @param array $options
     * @return array
     */
    public function getPriceOptions(array $options = [])
    {
        $defaultValues = [];
        $attributes = [];
        $preConfiguredValues = $this->getPreConfiguredValues($this->product);
        $configurableAttributes = $this->product->getTypeInstance()->getConfigurableAttributes($this->product);
        foreach ($configurableAttributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = [
                'id' => $productAttribute->getId(),
                'code' => $productAttribute->getAttributeCode(),
                'label' => $attribute->getLabel(),
                'options' => []
            ];

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }

                    /**
                     * @var $optionPrice \Magento\ConfigurableProduct\Pricing\Price\CustomOptionPrice
                     */
                    $optionPrice = $this->product
                        ->getPriceInfo()
                        ->getPrice('custom_option_price');
                    $optionValueAmount = $optionPrice->getOptionValueAmount($value);
                    $optionValueOldAmount = $optionPrice->getOptionValueOldAmount($value);

                    $price = $this->getOptionPrice($optionValueAmount);

                    // @todo resolve issue with weee specifics
                    $info['options'][] = [
                        'id' => $value['value_index'],
                        'label' => $value['label'],
                        'price' => $price,
                        'oldPrice' => $this->_registerJsPrice($optionValueOldAmount->getValue()),
                        'inclTaxPrice' => $this->_registerJsPrice($optionValueAmount->getValue()),
                        'exclTaxPrice' => $this->_registerJsPrice($optionValueAmount->getBaseAmount()),
                        'products' => $this->getProductsIndex($attributeId, $options, $value)
                    ];
                    $optionPrices[] = $price;
                }
            }

            $this->formatOptionsValues($optionPrices);
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
     * Get Products Index
     *
     * @param int $attributeId
     * @param array $options
     * @param array $value
     * @return array
     */
    protected function getProductsIndex($attributeId, array $options = array(), array $value = array())
    {
        if (isset($options[$attributeId][$value['value_index']])) {
            return $options[$attributeId][$value['value_index']];
        } else {
            return array();
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
     * Validating of super product option value
     *
     * @param int $attributeId
     * @param array $value
     * @param array $options
     * @return bool
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if (isset($options[$attributeId][$value['value_index']])) {
            return true;
        }

        return false;
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
     * Prepare formatted values for options choose
     *
     * @param array $optionPrices
     * @return void
     */
    protected function formatOptionsValues(array $optionPrices = array())
    {
        foreach ($optionPrices as $optionPrice) {
            foreach ($optionPrices as $additional) {
                $this->_preparePrice(abs($additional - $optionPrice));
            }
        }
    }

    /**
     * Calculation real price
     *
     * @param float $price
     * @param bool $isPercent
     * @return string
     */
    protected function _preparePrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->product->getFinalPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
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

        $price = $this->_storeManager->getStore()->convertPrice($price);
        if ($round) {
            $price = $this->_storeManager->getStore()->roundPrice($price);
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
        $taxHelper = $this->taxData;
        $defaultTax = $this->getDefaultTax($this->product);

        $currentTax = $this->getCurrentTax($this->product);

        $taxConfig = [
            'includeTax' => $taxHelper->priceIncludesTax(),
            'showIncludeTax' => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices' => $taxHelper->displayBothPrices(),
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
