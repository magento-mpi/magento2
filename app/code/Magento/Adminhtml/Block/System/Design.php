<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Adminhtml_Block_System_Design extends Magento_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $this->setTemplate('system/design/index.phtml');

        $this->addChild('add_new_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Add Design Change'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/new')."')",
            'class'   => 'add'
        ));

        $this->getLayout()->getBlock('page-title')->setPageTitle('Store Design Schedule');

        return parent::_prepareLayout();
    }
}
