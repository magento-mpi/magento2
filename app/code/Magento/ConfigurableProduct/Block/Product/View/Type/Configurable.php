<?php
/**
 * Catalog super product configurable part block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Block\Product\View\Type;

use Magento\Catalog\Model\Product\PriceModifierInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface as CustomerAccountService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * Prices
     *
     * @var array
     */
    protected $_prices = array();

    /**
     * Prepared prices
     *
     * @var array
     */
    protected $_resPrices = array();

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct = null;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * @var CustomerAccountService
     */
    protected $_customerAccountService;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Catalog\Helper\Product\Price $priceHelper
     * @param CustomerAccountService $customerAccountService
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Helper\Product\Price $priceHelper,
        CustomerAccountService $customerAccountService,
        array $data = array()
    ) {
        $this->_catalogProduct = $catalogProduct;
        $this->_jsonEncoder = $jsonEncoder;
        $this->priceHelper = $priceHelper;
        $this->_customerAccountService = $customerAccountService;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        return $this->getProduct()->getTypeInstance()->getConfigurableAttributes($this->getProduct());
    }

    /**
     * Check if allowed attributes have options
     *
     * @return bool
     */
    public function hasOptions()
    {
        $attributes = $this->getAllowAttributes();
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $skipSaleableCheck = $this->_catalogProduct->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    /**
     * Retrieve current store
     *
     * @return \Magento\Store\Model\Store
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        return array();
    }

    /**
     * Composes configuration for js
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getJsonConfig()
    {
        $options = array();
        $store = $this->getCurrentStore();

        $currentProduct = $this->getProduct();

        foreach ($this->getAllowProducts() as $product) {
            $productId = $product->getId();
            $this->_imageHelper->init($product, 'image');

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;

                if (!$product->getImage() || $product->getImage() === 'no_selection') {
                    $options['images'][$productAttributeId][$attributeValue][$productId] = $baseImageUrl;
                } else {
                    $options['images'][$productAttributeId][$attributeValue][$productId] = (string)$this->_imageHelper;
                }
            }
        }

        $defaultValues = array();
        $preConfiguredValues = $this->getPreConfiguredValues($currentProduct);

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
                'id' => $productAttribute->getId(),
                'code' => $productAttribute->getAttributeCode(),
                'label' => $attribute->getLabel(),
                'options' => array()
            );

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
                    $optionPrice = $currentProduct
                        ->getPriceInfo()
                        ->getPrice('custom_option_price');
                    $optionValueAmount = $optionPrice->getOptionValueAmount($value);
                    $optionValueOldAmount = $optionPrice->getOptionValueOldAmount($value);

                    $price = $this->getOptionPrice($optionValueAmount);

                    // @todo resolve issue with weee specifics
                    $info['options'][] = array(
                        'id' => $value['value_index'],
                        'label' => $value['label'],
                        'price' => $price,
                        'oldPrice' => $this->_registerJsPrice($optionValueOldAmount->getValue()),
                        'inclTaxPrice' => $this->_registerJsPrice($optionValueAmount->getValue()),
                        'exclTaxPrice' => $this->_registerJsPrice($optionValueAmount->getBaseAmount()),
                        'products' => $this->getProductsIndex($options, $attributeId, $value)
                    );
                    $optionPrices[] = $price;
                }
            }

            $this->formatOptionsValues($optionPrices);
            $attributes = $this->collectOptionsAttributes($info, $attributeId);

            $defaultValues[$attributeId] = $this->getAttributeConfigValue($preConfiguredValues, $attributeId);
        }

        $this->setCustomer();
        $taxConfig = $this->getTaxConfig($currentProduct);

        $config = array(
            'attributes' => $attributes,
            'template' => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice' => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice' => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId' => $currentProduct->getId(),
            'chooseText' => __('Choose an Option...'),
            'taxConfig' => $taxConfig,
            'images' => $options['images']
        );

        $config = $this->addDefaultValuesToConfig($defaultValues, $config);

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->_jsonEncoder->encode($config);
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
     * @param \Magento\Object $preConfiguredValues
     * @param int $attributeId
     * @return mixed|null
     */
    protected function getAttributeConfigValue($preConfiguredValues, $attributeId)
    {
        if( $this->hasPreConfiguredValues()) {
            $configValue = $preConfiguredValues->getData('super_attribute/' . $attributeId);
            if($configValue)
            {
                return $configValue;
            }
        }
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
     * Calculation real price
     *
     * @param float $price
     * @param bool $isPercent
     * @return string
     */
    protected function _preparePrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getProduct()->getFinalPrice() * $price / 100;
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

        $price = $this->getCurrentStore()->convertPrice($price);
        if ($round) {
            $price = $this->getCurrentStore()->roundPrice($price);
        }

        return $price;
    }

    /**
     * Get Tax Config
     *
     * @return array
     */
    protected function getTaxConfig()
    {
        $currentProduct = $this->getProduct();
        $taxHelper = $this->_taxData;
        $defaultTax = $this->getDefaultTax($currentProduct);

        $currentTax = $this->getCurrentTax($currentProduct);

        $taxConfig = array(
            'includeTax' => $taxHelper->priceIncludesTax(),
            'showIncludeTax' => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices' => $taxHelper->displayBothPrices(),
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'inclTaxTitle' => __('Incl. Tax')
        );
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
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
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
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = $this->priceHelper->getRate($_request);
        return $currentTax;
    }

    /**
     * Get CustomerId from registry and set it to PriceHelper
     */
    protected function setCustomer()
    {
        if (is_null($this->priceHelper->getCustomer()->getId())
            && $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        ) {
            $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
            $this->priceHelper->setCustomer($this->_customerAccountService->getCustomer($customerId));
        }
    }

    /**
     * Prepare formatted values for options choose
     *
     * @param array $optionPrices
     * @return mixed
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
     * Collect Options Attributes
     *
     * @param array $info
     * @param int $attributeId
     * @return array
     */
    protected function collectOptionsAttributes($info, $attributeId)
    {
        $attributes = array();
        if ($this->_validateAttributeInfo($info)) {
            $attributes[$attributeId] = $info;
        }
        return $attributes;
    }

    /**
     * Add Default Values if set
     *
     * @param array $defaultValues
     * @param array $config
     * @return array
     */
    protected function addDefaultValuesToConfig($defaultValues, $config)
    {
        if ($this->getProduct()->hasPreconfiguredValues() && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }
        return $config;
    }

    /**
     * Get PreConfigured Values
     *
     * @return array
     */
    protected function getPreConfiguredValues()
    {
        $preConfiguredValues = null;
        if ($this->hasPreconfiguredValues()) {
            $preConfiguredValues = $this->getProduct()->getPreconfiguredValues();
        }
        return $preConfiguredValues;
    }

    /**
     * Get Flag if Configurable Product has PreConfiguredValues
     *
     * @return bool
     */
    protected function hasPreConfiguredValues()
    {
        return $this->getProduct()->hasPreconfiguredValues();
    }

    /**
     * Get Products Index
     *
     * @param $options
     * @param $attributeId
     * @param $value
     * @return array
     */
    protected function getProductsIndex($options, $attributeId, $value)
    {
        if (isset($options[$attributeId][$value['value_index']])) {
            return $options[$attributeId][$value['value_index']];
        } else {
            return array();
        }
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
        if ($this->_taxData->displayPriceIncludingTax()) {
            return $this->_registerJsPrice($optionValueAmount->getValue());
        } else {
            return $this->_registerJsPrice($optionValueAmount->getBaseAmount());
        }
    }
}
