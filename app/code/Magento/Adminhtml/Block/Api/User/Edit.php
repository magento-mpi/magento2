<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permissions user edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Api_User_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'api_user';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Magento_Adminhtml_Helper_Data')->__('Save User'));
        $this->_updateButton('delete', 'label', Mage::helper('Magento_Adminhtml_Helper_Data')->__('Delete User'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('api_user')->getId()) {
            return Mage::helper('Magento_Adminhtml_Helper_Data')->__("Edit User '%s'", $this->escapeHtml(Mage::registry('api_user')->getUsername()));
        }
        else {
            return Mage::helper('Magento_Adminhtml_Helper_Data')->__('New User');
        }
    }

}
