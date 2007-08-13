<?php
/**
 * description
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/toolbar/add.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Attribute Set'),
                    'onclick'   => 'addSet.submit();',
                    'class' => 'save'
        )));
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/').'\')',
                    'class' => 'back'
        )));

        $this->setChild('setForm',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset')
        );
    }

    protected function _getHeader()
    {
        return __('Add New Attribute Set');
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

    protected function getFormId()
    {
        return $this->getChild('setForm')->getForm()->getId();
    }
}