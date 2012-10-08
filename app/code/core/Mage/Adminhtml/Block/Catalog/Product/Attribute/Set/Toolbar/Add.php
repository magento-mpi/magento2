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
 * description
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add extends Mage_Adminhtml_Block_Template
{

    protected $_template = 'catalog/product/attribute/set/toolbar/add.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Save Attribute Set'),
            'onclick'   => 'if (addSet.submit()) disableElements(\'save\');',
            'class' => 'save'
        ));
        $this->addChild('back_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
            'class' => 'back'
        ));

        $this->addChild('setForm', 'Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formset');
        return parent::_prepareLayout();
    }

    protected function _getHeader()
    {
        return Mage::helper('Mage_Catalog_Helper_Data')->__('Add New Attribute Set');
    }

    protected function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    protected function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    protected function getFormHtml()
    {
        return $this->getChildHtml('setForm');
    }

    /**
     * Return id of form, used by this block
     *
     * @return string
     */
    protected function getFormId()
    {
        return $this->getChildBlock('setForm')->getForm()->getId();
    }
}
