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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('convert_profile_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Import/Export Profile'));
    }

    protected function _beforeToHtml()
    {
        $new = !Mage::registry('current_convert_profile')->getId();

        $this->addTab('edit', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profile Actions XML'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Edit')->initForm()->toHtml(),
            'active'    => true,
        ));

        if (!$new) {
            $this->addTab('run', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Run Profile'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run')->toHtml(),
            ));

            $this->addTab('history', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profile History'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_History')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }
}
