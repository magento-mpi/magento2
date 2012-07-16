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
 * Create product attribute set selector
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_AttributeSet extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('settings', array(
            'legend' => Mage::helper('Mage_Catalog_Helper_Data')->__('Product Settings')
        ));

        $entityType = Mage::registry('product')->getResource()->getEntityType();

        $fieldset->addField('attribute_set_id', 'select', array(
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Attribute Set'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Attribute Set'),
            'name'  => 'set',
            'value' => Mage::registry('product')->getAttributeSetId(),
            'values'=> Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection')
                ->setEntityTypeFilter($entityType->getId())
                ->load()
                ->toOptionArray()
        ));

        $this->setForm($form);
    }
}
