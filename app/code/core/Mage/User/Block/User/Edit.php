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
 * Adminhtml permissions user edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_User_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'permissions_user';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save User'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete User'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('permissions_user')->getId()) {
            return Mage::helper('Mage_Adminhtml_Helper_Data')->__("Edit User '%s'", $this->escapeHtml(Mage::registry('permissions_user')->getUsername()));
        }
        else {
            return Mage::helper('Mage_Adminhtml_Helper_Data')->__('New User');
        }
    }

}
