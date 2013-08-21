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
 * New product attribute created on product edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created extends Magento_Adminhtml_Block_Widget
{

    protected $_template = 'catalog/product/attribute/new/created.phtml';

    /**
     * Retrieve list of product attributes
     *
     * @return array
     */
    protected function _getGroupAttributes()
    {
        $attributes = array();
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::registry('product');
        foreach($product->getAttributes($this->getRequest()->getParam('group')) as $attribute) {
            /** @var $attribute Magento_Eav_Model_Entity_Attribute */
            if ($attribute->getId() == $this->getRequest()->getParam('attribute')) {
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * Retrieve HTML for 'Close' button
     *
     * @return string
     */
    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    /**
     * Retrieve attributes data as JSON
     *
     * @return string
     */
    public function getAttributesBlockJson()
    {
        $result = array();
        if ($this->getRequest()->getParam('product_tab') == 'variations') {
            /** @var $attribute Magento_Eav_Model_Entity_Attribute */
            $attribute =
                Mage::getModel('Magento_Eav_Model_Entity_Attribute')->load($this->getRequest()->getParam('attribute'));
            $result = array(
                'tab' => $this->getRequest()->getParam('product_tab'),
                'attribute' => array(
                    'id' => $attribute->getId(),
                    'label' => $attribute->getFrontendLabel(),
                    'code' => $attribute->getAttributeCode(),
                    'options' => $attribute->getSourceModel() ? $attribute->getSource()->getAllOptions(false) : array()
                )
            );
        }
        $newAttributeSetId = $this->getRequest()->getParam('new_attribute_set_id');
        if ($newAttributeSetId) {
            /** @var $attributeSet Magento_Eav_Model_Entity_Attribute_Set */
            $attributeSet = Mage::getModel('Magento_Eav_Model_Entity_Attribute_Set')->load($newAttributeSetId);
            $result['set'] = array(
                'id' => $attributeSet->getId(),
                'label' => $attributeSet->getAttributeSetName(),
            );
        }

        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($result);
    }
}
