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
 * Product attributes tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\NewAttribute\Product;

class Attributes extends \Magento\Adminhtml\Block\Catalog\Form
{
    protected function _prepareForm()
    {

        $form = new \Magento\Data\Form();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(\Mage::registry('product'));

        $fieldset = $form->addFieldset('group_fields', array());

        $attributes = $this->getGroupAttributes();

        $this->_setFieldset($attributes, $fieldset, array('gallery'));

        $values = \Mage::registry('product')->getData();
        /**
         * Set attribute default values for new product
         */
        if (!\Mage::registry('product')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($values[$attribute->getAttributeCode()])) {
                    $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }

        \Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form'=>$form));
        $form->addValues($values);
        $form->setFieldNameSuffix('product');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'   => 'Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Price',
            'image'   => 'Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Image',
            'boolean' => 'Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Boolean',
        );

        $response = new \Magento\Object();
        $response->setTypes(array());
        \Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response'=>$response));

        foreach ($response->getTypes() as $typeName=>$typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }

    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getForm()->getElement('group_fields')->getChildrenHtml();
    }
}
