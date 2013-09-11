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
 * Product options block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_View_Options extends Magento_Core_Block_Template
{
    protected $_product;

    /**
     * Product option
     *
     * @var Magento_Catalog_Model_Product_Option
     */
    protected $_option;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_registry = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Catalog_Model_Product_Option $option
     * @param Magento_Core_Model_Registry $registry
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Catalog_Model_Product_Option $option,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        $this->_option = $option;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product object
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->_registry->registry('current_product')) {
                $this->_product = $this->_registry->registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('Magento_Catalog_Model_Product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Block_Product_View_Options
     */
    public function setProduct(Magento_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    public function getGroupOfOption($type)
    {
        $group = $this->_option->getGroupByType($type);

        return $group == '' ? 'default' : $group;
    }

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getProduct()->getOptions();
    }

    public function hasOptions()
    {
        if ($this->getOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Get price configuration
     *
     * @param Magento_Catalog_Model_Product_Option_Value|Magento_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $data = array();
        $data['price']      = Mage::helper('Magento_Core_Helper_Data')->currency($option->getPrice(true), false, false);
        $data['oldPrice']   = Mage::helper('Magento_Core_Helper_Data')->currency($option->getPrice(false), false, false);
        $data['priceValue'] = $option->getPrice(false);
        $data['type']       = $option->getPriceType();
        $data['excludeTax'] = $price = Mage::helper('Magento_Tax_Helper_Data')->getPrice($option->getProduct(), $data['price'], false);
        $data['includeTax'] = $price = Mage::helper('Magento_Tax_Helper_Data')->getPrice($option->getProduct(), $data['price'], true);
        return $data;
    }

    /**
     * Get json representation of
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getOptions() as $option) {
            /* @var $option Magento_Catalog_Model_Product_Option */
            $priceValue = 0;
            if ($option->getGroupByType() == Magento_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($option->getValues() as $value) {
                    /* @var $value Magento_Catalog_Model_Product_Option_Value */
                    $id = $value->getId();
                    $_tmpPriceValues[$id] = $this->_getPriceConfiguration($value);
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = $this->_getPriceConfiguration($option);
            }
            $config[$option->getId()] = $priceValue;
        }

        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($config);
    }

    /**
     * Get option html block
     *
     * @param Magento_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Magento_Catalog_Model_Product_Option $option)
    {
        $type = $this->getGroupOfOption($option->getType());
        $renderer = $this->getChildBlock($type);

        $renderer->setProduct($this->getProduct())
            ->setOption($option);

        return $this->getChildHtml($type, false);
    }
}
