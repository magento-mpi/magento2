<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permissions user edit page
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Edit extends Mage_Backend_Block_Widget_Form_Container
{
    /**
     * Initialize form container
     */
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_objectId = 'user_id';
        $this->_controller = 'adminhtml_user';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Webapi_Helper_Data')->__('Save API User'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Webapi_Helper_Data')->__('Delete API User'));
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getApiUser()->getId()) {
            return Mage::helper('Mage_Webapi_Helper_Data')
                ->__("Edit User '%s'", $this->escapeHtml($this->getApiUser()->getUserName()));
        } else {
            return Mage::helper('Mage_Webapi_Helper_Data')->__('New User');
        }
    }
}
