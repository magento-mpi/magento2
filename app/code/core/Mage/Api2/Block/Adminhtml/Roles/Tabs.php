<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Block tabs for role edit page
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Block_Adminhtml_Roles_Tabs setRole(Mage_Api2_Model_Acl_Global_Role $role)
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 */
class Mage_Api2_Block_Adminhtml_Roles_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
        $this->setData('title', Mage::helper('Mage_Api2_Helper_Data')->__('Role Information'));
    }

    /**
     * Hook before html rendering
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Tabs
     */
    protected function _beforeToHtml()
    {
        $role = $this->getRole();
        if ($role && Mage_Api2_Model_Acl_Global_Role::isSystemRole($role)) {
            $this->setActiveTab('api2_role_section_resources');
        } else {
            $this->setActiveTab('api2_role_section_info');
        }
        return parent::_beforeToHtml();
    }
}
