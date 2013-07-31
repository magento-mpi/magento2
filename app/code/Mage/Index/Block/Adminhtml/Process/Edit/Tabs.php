<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Process_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('process_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Index_Helper_Data')->__('Index'));
    }
}
