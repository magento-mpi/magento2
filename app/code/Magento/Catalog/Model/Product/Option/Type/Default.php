<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product option default type
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Option_Type_Default extends Magento_Object
{
    /**
     * Option Instance
     *
     * @var Magento_Catalog_Model_Product_Option
     */
    protected $_option;

    /**
     * Product Instance
     *
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;



    /**
     * description
     *
     * @var    mixed
     */
    protected $_productOptions = array();

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Construct
     *
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($data);
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Option Instance setter
     *
     * @param Magento_Catalog_Model_Product_Option $option
     * @return Magento_Catalog_Model_Product_Option_Type_Default
     */
    public function setOption($option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Option Instance getter
     *
     * @throws Magento_Core_Exception
     * @return Magento_Catalog_Model_Product_Option
     */
    public function getOption()
    {
        if ($this->_option instanceof Magento_Catalog_Model_Product_Option) {
            return $this->_option;
        }
        throw new Magento_Core_Exception(__('The option instance type in options group is incorrect.'));
    }

    /**
     * Product Instance setter
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Option_Type_Default
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Product Instance getter
     *
     * @throws Magento_Core_Exception
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if ($this->_product instanceof Magento_Catalog_Model_Product) {
            return $this->_product;
        }
        throw new Magento_Core_Exception(__('The product instance type in options group is incorrect.'));
    }

    /**
     * Getter for Configuration Item Option
     *
     * @return Magento_Catalog_Model_Product_Configuration_Item_Option_Interface
     * @throws Magento_Core_Exception
     */
    public function getConfigurationItemOption()
    {
        if ($this->_getData('configuration_item_option') instanceof Magento_Catalog_Model_Product_Configuration_Item_Option_Interface) {
            return $this->_getData('configuration_item_option');
        }

        // Back compatibility with quote specific keys to set configuration item options
        if ($this->_getData('quote_item_option') instanceof Magento_Sales_Model_Quote_Item_Option) {
            return $this->_getData('quote_item_option');
        }

        throw new Magento_Core_Exception(__('The configuration item option instance in options group is incorrect.'));
    }

    /**
     * Getter for Configuration Item
     *
     * @return Magento_Catalog_Model_Product_Configuration_Item_Interface
     * @throws Magento_Core_Exception
     */
    public function getConfigurationItem()
    {
        if ($this->_getData('configuration_item') instanceof Magento_Catalog_Model_Product_Configuration_Item_Interface) {
            return $this->_getData('configuration_item');
        }

        // Back compatibility with quote specific keys to set configuration item
        if ($this->_getData('quote_item') instanceof Magento_Sales_Model_Quote_Item) {
            return $this->_getData('quote_item');
        }

        throw new Magento_Core_Exception(__('The configuration item instance in options group is incorrect.'));
    }

    /**
     * Getter for Buy Request
     *
     * @return Magento_Object
     * @throws Magento_Core_Exception
     */
    public function getRequest()
    {
        if ($this->_getData('request') instanceof Magento_Object) {
            return $this->_getData('request');
        }
        throw new Magento_Core_Exception(__('The BuyRequest instance in options group is incorrect.'));
    }

    /**
     * Store Config value
     *
     * @param string $key Config value key
     * @return string
     */
    public function getConfigData($key)
    {
        return $this->_coreStoreConfig->getConfig('catalog/custom_options/' . $key);
    }

    /**
     * Validate user input for option
     *
     * @throws Magento_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Magento_Catalog_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        $this->_checkoutSession->setUseNotice(false);

        $this->setIsValid(false);

        $option = $this->getOption();
        if (!isset($values[$option->getId()]) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            throw new Magento_Core_Exception(__('Please specify the product required option(s).'));
        } elseif (isset($values[$option->getId()])) {
            $this->setUserValue($values[$option->getId()]);
            $this->setIsValid(true);
        }
        return $this;
    }

    /**
     * Check skip required option validation
     *
     * @return bool
     */
    public function getSkipCheckRequiredOption()
    {
        return $this->getProduct()->getSkipCheckRequiredOption() ||
            $this->getProcessMode() == Magento_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE;
    }

    /**
     * Prepare option value for cart
     *
     * @throws Magento_Core_Exception
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid()) {
            return $this->getUserValue();
        }
        throw new Magento_Core_Exception(__('We couldn\'t add the product to the cart because of an option validation issue.'));
    }

    /**
     * Flag to indicate that custom option has own customized output (blocks, native html etc.)
     *
     * @return boolean
     */
    public function isCustomizedView()
    {
        return false;
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        return $optionValue;
    }

    /**
     * Return option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedView($optionInfo)
    {
        return isset($optionInfo['value']) ? $optionInfo['value'] : $optionInfo;
    }

    /**
     * Return printable option value
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        return $optionValue;
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     * (ex: Admin re-order, see Magento_Adminhtml_Model_Sales_Order_Create)
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        return $optionValue;
    }

    /**
     * Parse user input value and return cart prepared value, i.e. "one, two" => "1,2"
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $productOptionValues)
    {
        return $optionValue;
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        return $optionValue;
    }

    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param float $basePrice For percent price type
     * @return float
     */
    public function getOptionPrice($optionValue, $basePrice)
    {
        $option = $this->getOption();

        return $this->_getChargableOptionPrice(
            $option->getPrice(),
            $option->getPriceType() == 'percent',
            $basePrice
        );
    }

    /**
     * Return SKU for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param string $skuDelimiter Delimiter for Sku parts
     * @return string
     */
    public function getOptionSku($optionValue, $skuDelimiter)
    {
        return $this->getOption()->getSku();
    }

    /**
     * Return value => key all product options (using for parsing)
     *
     * @return array Array of Product custom options, reversing option values and option ids
     */
    public function getProductOptions()
    {
        if (!isset($this->_productOptions[$this->getProduct()->getId()])) {
            foreach ($this->getProduct()->getOptions() as $_option) {
                /* @var $option Magento_Catalog_Model_Product_Option */
                $this->_productOptions[$this->getProduct()->getId()][$_option->getTitle()] = array('option_id' => $_option->getId());
                if ($_option->getGroupByType() == Magento_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                    $optionValues = array();
                    foreach ($_option->getValues() as $_value) {
                        /* @var $value Magento_Catalog_Model_Product_Option_Value */
                        $optionValues[$_value->getTitle()] = $_value->getId();
                    }
                    $this->_productOptions[$this->getProduct()->getId()][$_option->getTitle()]['values'] = $optionValues;
                } else {
                    $this->_productOptions[$this->getProduct()->getId()][$_option->getTitle()]['values'] = array();
                }
            }
        }
        if (isset($this->_productOptions[$this->getProduct()->getId()])) {
            return $this->_productOptions[$this->getProduct()->getId()];
        }
        return array();
    }

    /**
     * Return final chargable price for option
     *
     * @param float $price Price of option
     * @param boolean $isPercent Price type - percent or fixed
     * @param float $basePrice For percent price type
     * @return float
     */
    protected function _getChargableOptionPrice($price, $isPercent, $basePrice)
    {
        if($isPercent) {
            return ($basePrice * $price / 100);
        } else {
            return $price;
        }
    }

}
