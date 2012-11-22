<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml super product links grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Get product type
     *
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    protected function _getProductType()
    {
        return Mage::getSingleton('Mage_Catalog_Model_Product_Type_Configurable');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Preparing layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid|Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $product = $this->_getProduct();
        $attributes = $this->_getProductType()->getConfigurableAttributes($product);

        foreach ($attributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();

            $this->getColumnSet()->addChild(
                $productAttribute->getAttributeCode(),
                'Mage_Backend_Block_Widget_Grid_Column',
                array(
                    'header' => $productAttribute->getFrontend()->getLabel(),
                    'index' => $productAttribute->getAttributeCode(),
                    'type' => 'options',
                    'options' => $this->getOptions($productAttribute->getSource()),
                    'sortable' => false
                )
            )
                ->setId($productAttribute->getAttributeCode())
                ->setGrid($this);
        }

        return $this;
    }

    /**
     * Get option as hash
     *
     * @param Mage_Eav_Model_Entity_Attribute_Source_Abstract $sourceModel
     * @return array
     */
    private function getOptions(Mage_Eav_Model_Entity_Attribute_Source_Abstract $sourceModel)
    {
        $result = array();
        foreach ($sourceModel->getAllOptions() as $option) {
            if ($option['value'] != '') {
                $result[$option['value']] = $option['label'];
            }
        }
        return $result;
    }
}