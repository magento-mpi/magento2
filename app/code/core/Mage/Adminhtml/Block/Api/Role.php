<?php
/**
 * Adminhtml permissioms role block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Api_Role extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'api_role';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Roles');
        $this->_addButtonLabel = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add New Role');
        parent::_construct();
    }

}
