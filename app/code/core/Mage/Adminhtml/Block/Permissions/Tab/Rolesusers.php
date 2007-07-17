<?php
class Mage_Adminhtml_Block_Permissions_Tab_Rolesusers extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct()
    {
        parent::__construct();

        $roleId = $this->_request->getParam('rid', false);

        $users = Mage::getModel("permissions/users")->getCollection()->load();
        $this->setTemplate('adminhtml/permissions/rolesusers.phtml')
        	->assign('users', $users->getItems())
        	->assign('roleId', $roleId);
    }

    protected function _initChildren()
    {
        $this->setChild('userGrid', $this->getLayout()->createBlock('adminhtml/permissions_role_grid_user', 'taxClassGrid'));
    }

    protected function _getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }

    protected function _getJsObjectName()
    {
        return $this->getChild('userGrid')->getJsObjectName();
    }
}