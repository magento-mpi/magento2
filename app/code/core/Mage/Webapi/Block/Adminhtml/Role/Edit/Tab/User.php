<?php
/**
 * Web API Role users tab
 *
 * @copyright {}
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_User setApiRole() setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole() getApiRole()
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
    public function getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
