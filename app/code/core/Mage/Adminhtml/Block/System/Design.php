<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Block_System_Design extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $this->setTemplate('system/design/index.phtml');

        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Add Design Change'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/new')."')",
                    'class'   => 'add'
                    ))
                );

        $this->setChild('grid', $this->getLayout()->createBlock(
            'Mage_Adminhtml_Block_System_Design_Grid',
            'design.grid'
        ));
        return parent::_prepareLayout();
    }
}
