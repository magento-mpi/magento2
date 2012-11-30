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
 * Adminhtml convert profiles list block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Convert_Gui extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'system_convert_gui';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profiles');
        $this->_addButtonLabel = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add New Profile');

        parent::_construct();
    }
}
