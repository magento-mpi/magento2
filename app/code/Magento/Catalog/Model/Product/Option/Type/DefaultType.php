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
namespace Magento\Catalog\Model\Product\Option\Type;

class DefaultType extends \Magento\Object
{
    /**
     * Option Instance
     *
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_option;

    /**
     * Product Instance
     *
     * @var \Magento\Catalog\Model\Product
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
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Option Instance setter
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     */
    public function setOption($option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Option Instance getter
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Catalog\Model\Product\Option
     */
    public function getOption()
    {
        if ($this->_option instanceof \Magento\Catalog\Model\Product\Option) {
            return $this->_option;
        }
        \Mage::throwException(__('The option instance type in options group is incorrect.'));
    }

    /**
     * Product Instance setter
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Product Instance getter
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ($this->_product instanceof \Magento\Catalog\Model\Product) {
            return $this->_product;
        }
        \Mage::throwException(__('The product instance type in options group is incorrect.'));
    }

    /**
     * Getter for Configuration Item Option
     *
     * @return \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface
     */
    public function getConfigurationItemOption()
    {
        if ($this->_getData('configuration_item_option') instanceof \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface) {
            return $this->_getData('configuration_item_option');
        }

        // Back compatibility with quote specific keys to set configuration item options
        if ($this->_getData('quote_item_option') instanceof \Magento\Sales\Model\Quote\Item\Option) {
            return $this->_getData('quote_item_option');
        }

        \Mage::throwException(__('The configuration item option instance in options group is incorrect.'));
    }

    /**
     * Getter for Configuration Item
     *
     * @return \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface
     */
    public function getConfigurationItem()
    {
        if ($this->_getData('configuration_item') instanceof \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface) {
            return $this->_getData('configuration_item');
        }

        // Back compatibility with quote specific keys to set configuration item
        if ($this->_getData('quote_item') instanceof \Magento\Sales\Model\Quote\Item) {
            return $this->_getData('quote_item');
        }

        \Mage::throwException(__('The configuration item instance in options group is incorrect.'));
    }

    /**
     * Getter for Buy Request
     *
     * @return \Magento\Object
     */
    public function getRequest()
    {
        if ($this->_getData('request') instanceof \Magento\Object) {
            return $this->_getData('request');
        }
        \Mage::throwException(__('The BuyRequest instance in options group is incorrect.'));
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
     * @throws \Magento\Core\Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     */
    public function validateUserValue($values)
    {
        \Mage::getSingleton('Magento\Checkout\Model\Session')->setUseNotice(false);

        $this->setIsValid(false);

        $option = $this->getOption();
        if (!isset($values[$option->getId()]) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            \Mage::throwException(__('Please specify the product required option(s).'));
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
            $this->getProcessMode() == \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_LITE;
    }

    /**
     * Prepare option value for cart
     *
     * @throws \Magento\Core\Exception
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid()) {
            return $this->getUserValue();
        }
        \Mage::throwException(__('We couldn\'t add the product to the cart because of an option validation issue.'));
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
     * (ex: Admin re-order, see \Magento\Adminhtml\Model\Sales\Order\Create)
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
                /* @var $option \Magento\Catalog\Model\Product\Option */
                $this->_productOptions[$this->getProduct()->getId()][$_option->getTitle()] = array('option_id' => $_option->getId());
                if ($_option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {
                    $optionValues = array();
                    foreach ($_option->getValues() as $_value) {
                        /* @var $value \Magento\Catalog\Model\Product\Option\Value */
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
