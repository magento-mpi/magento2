<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Process extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_Index';
        $this->_controller = 'adminhtml_process';
        $this->_headerText = Mage::helper('Mage_Index_Helper_Data')->__('Index Management');
        parent::_construct();
        $this->_removeButton('add');
    }
}
