<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_System_Design_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('design_tabs');
        $this->setDestElementId('design_edit_form');
        $this->setTitle(Mage::helper('Mage_Core_Helper_Data')->__('Design Change'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('Mage_Core_Helper_Data')->__('General'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_System_Design_Edit_Tab_General')->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}
