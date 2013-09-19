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
namespace Magento\Catalog\Block\Product\View;

class Options extends \Magento\Core\Block\Template
{
    protected $_product;

    /**
     * Product option
     *
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_option;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_registry = $registry;
        $this->_option = $option;
        $this->_taxData = $taxData;
    }

    /**
     * Retrieve product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->_registry->registry('current_product')) {
                $this->_product = $this->_registry->registry('current_product');
            } else {
                $this->_product = \Mage::getSingleton('Magento\Catalog\Model\Product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Block\Product\View\Options
     */
    public function setProduct(\Magento\Catalog\Model\Product $product = null)
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
     * @param \Magento\Catalog\Model\Product\Option\Value|\Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $data = array();
        $data['price']      = $this->_coreData->currency($option->getPrice(true), false, false);
        $data['oldPrice']   = $this->_coreData->currency($option->getPrice(false), false, false);
        $data['priceValue'] = $option->getPrice(false);
        $data['type']       = $option->getPriceType();
        $data['excludeTax'] = $price = $this->_taxData->getPrice($option->getProduct(), $data['price'], false);
        $data['includeTax'] = $price = $this->_taxData->getPrice($option->getProduct(), $data['price'], true);
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
            /* @var $option \Magento\Catalog\Model\Product\Option */
            $priceValue = 0;
            if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($option->getValues() as $value) {
                    /* @var $value \Magento\Catalog\Model\Product\Option\Value */
                    $id = $value->getId();
                    $_tmpPriceValues[$id] = $this->_getPriceConfiguration($value);
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = $this->_getPriceConfiguration($option);
            }
            $config[$option->getId()] = $priceValue;
        }

        return $this->_coreData->jsonEncode($config);
    }

    /**
     * Get option html block
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     */
    public function getOptionHtml(\Magento\Catalog\Model\Product\Option $option)
    {
        $type = $this->getGroupOfOption($option->getType());
        $renderer = $this->getChildBlock($type);

        $renderer->setProduct($this->getProduct())
            ->setOption($option);

        return $this->getChildHtml($type, false);
    }
}
