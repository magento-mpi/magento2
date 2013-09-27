<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml sales order column renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Items_Column_Default extends Magento_Adminhtml_Block_Template
{
    /**
     * @var Magento_Catalog_Model_Product_OptionFactory
     */
    protected $_optionFactory;

    /**
     * @param Magento_Catalog_Model_Product_OptionFactory $optionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Product_OptionFactory $optionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_optionFactory = $optionFactory;
        parent::__construct($coreData, $context, $data);
    }


    public function getItem()
    {
        if ($this->_getData('item') instanceof Magento_Sales_Model_Order_Item) {
            return $this->_getData('item');
        } else {
            return $this->_getData('item')->getOrderItem();
        }
    }

    public function getOrderOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    /**
     * Return custom option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedOptionValue($optionInfo)
    {
        // render customized option view
        $_default = $optionInfo['value'];
        if (isset($optionInfo['option_type'])) {
            try {
                $group = $this->_optionFactory->create()->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (Exception $e) {
                return $_default;
            }
        }
        return $_default;
    }

    public function getSku()
    {
        /*if ($this->getItem()->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $this->getItem()->getProductOptionByCode('simple_sku');
        }*/
        return $this->getItem()->getSku();
    }

}
