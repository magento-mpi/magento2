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
 * Web API role edit page
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit extends Mage_Backend_Block_Widget_Form_Container
{
    /**
     * Initialize form container
     */
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_objectId = 'role_id';
        $this->_controller = 'adminhtml_role';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Webapi_Helper_Data')->__('Save API Role'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Webapi_Helper_Data')->__('Delete API Role'));
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getApiRole()->getId()) {
            return Mage::helper('Mage_Webapi_Helper_Data')
                ->__("Edit Role '%s'", $this->escapeHtml($this->getApiRole()->getRoleName()));
        } else {
            return Mage::helper('Mage_Webapi_Helper_Data')->__('New Role');
        }
    }
}
