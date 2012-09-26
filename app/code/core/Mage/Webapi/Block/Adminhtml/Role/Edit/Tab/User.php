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
 * Web API Role users tab
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Prepare form container
     *
     * @return Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'userGrid',
            $this->getLayout()->createBlock('Mage_Webapi_Block_Adminhtml_Role_Grid_User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    /**
     * Get users grid HTML
     *
     * @return string
     */
    public function _getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
