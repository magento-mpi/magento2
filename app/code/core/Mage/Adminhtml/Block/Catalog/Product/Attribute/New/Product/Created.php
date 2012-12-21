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
 * New product attribute created on product edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created extends Mage_Adminhtml_Block_Widget
{

    protected $_template = 'catalog/product/attribute/new/created.phtml';

    /**
     * Add additional blocks to layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'attributes',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Attributes')
                ->setGroupAttributes($this->_getGroupAttributes())
        );

        $this->addChild('close_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'   => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
            'onclick' => 'addAttribute(true)'
        ));
    }

    /**
     * Retrieve list of product attributes
     *
     * @return array
     */
    protected function _getGroupAttributes()
    {
        $attributes = array();
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        foreach($product->getAttributes($this->getRequest()->getParam('group')) as $attribute) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
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
        $result = array(
            $this->getRequest()->getParam('product_tab') => $this->getChildHtml('attributes')
        );
        $newAttributeSetId = $this->getRequest()->getParam('new_attribute_set_id');
        if ($newAttributeSetId) {
            $result['newAttributeSetId'] = $newAttributeSetId;
        }

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }
}
