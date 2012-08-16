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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created extends Mage_Adminhtml_Block_Widget
{

    protected $_template = 'catalog/product/attribute/new/created.phtml';

    protected function _prepareLayout()
    {

        $this->setChild(
            'attributes',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Attributes')
                ->setGroupAttributes($this->_getGroupAttributes())
        );

        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'   => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
                    'onclick' => 'addAttribute(true)'
                ))
        );

    }

    protected function _getGroupAttributes()
    {
        $attributes = array();
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */
        foreach($product->getAttributes($this->getRequest()->getParam('group')) as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getId() == $this->getRequest()->getParam('attribute')) {
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getAttributesBlockJson()
    {
        $result = array(
            $this->getRequest()->getParam('tab') => $this->getChildHtml('attributes')
        );

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }
} // Class Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created End
