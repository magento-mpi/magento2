<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Index_Block_Adminhtml_Process extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Index';
        $this->_controller = 'adminhtml_process';
        $this->_headerText = __('Index Management');
        parent::_construct();
        $this->_removeButton('add');
    }
}
