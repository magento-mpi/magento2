<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User edit page
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Block_User_Edit extends Magento_Backend_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'user';
        $this->_blockGroup = 'Magento_User';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Magento_User_Helper_Data')->__('Save User'));
        $this->_updateButton('delete', 'label', Mage::helper('Magento_User_Helper_Data')->__('Delete User'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('permissions_user')->getId()) {
            $username = $this->escapeHtml(Mage::registry('permissions_user')->getUsername());
            return Mage::helper('Magento_User_Helper_Data')->__("Edit User '%s'", $username);
        } else {
            return Mage::helper('Magento_User_Helper_Data')->__('New User');
        }
    }

}
