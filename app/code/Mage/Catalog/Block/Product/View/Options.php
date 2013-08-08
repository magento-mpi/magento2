<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product options block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Options extends Magento_Core_Block_Template
{
    protected $_product;

    protected $_optionRenders = array();

    protected function _construct()
    {
        parent::_construct();
        $this->addOptionRenderer(
            'default',
            'Mage_Catalog_Block_Product_View_Options_Type_Default',
            'product/view/options/type/default.phtml'
        );
    }

    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('Mage_Catalog_Model_Product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Block_Product_View_Options
     */
    public function setProduct(Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Add option renderer to renderers array
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Mage_Catalog_Block_Product_View_Options
     */
    public function addOptionRenderer($type, $block, $template)
    {
        $this->_optionRenders[$type] = array(
            'block' => $block,
            'template' => $template,
            'renderer' => null
        );
        return $this;
    }

    /**
     * Get option render by given type
     *
     * @param string $type
     * @return array
     */
    public function getOptionRender($type)
    {
        if (isset($this->_optionRenders[$type])) {
            return $this->_optionRenders[$type];
        }

        return $this->_optionRenders['default'];
    }

    public function getGroupOfOption($type)
    {
        $group = Mage::getSingleton('Mage_Catalog_Model_Product_Option')->getGroupByType($type);

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
     * @param Mage_Catalog_Model_Product_Option_Value|Mage_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $data = array();
        $data['price']      = Mage::helper('Magento_Core_Helper_Data')->currency($option->getPrice(true), false, false);
        $data['oldPrice']   = Mage::helper('Magento_Core_Helper_Data')->currency($option->getPrice(false), false, false);
        $data['priceValue'] = $option->getPrice(false);
        $data['type']       = $option->getPriceType();
        $data['excludeTax'] = $price = Mage::helper('Mage_Tax_Helper_Data')->getPrice($option->getProduct(), $data['price'], false);
        $data['includeTax'] = $price = Mage::helper('Mage_Tax_Helper_Data')->getPrice($option->getProduct(), $data['price'], true);
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
            /* @var $option Mage_Catalog_Model_Product_Option */
            $priceValue = 0;
            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($option->getValues() as $value) {
                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
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
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template']);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}
